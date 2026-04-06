<?php
/**
 * Upload de firma digital - AJAX
 * Maneja la subida de la firma digital del revisor
 */

session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit;
}

// Verificar que se haya subido un archivo
if (!isset($_FILES['firma']) || $_FILES['firma']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se recibió el archivo o hubo un error en la subida']);
    exit;
}

$file = $_FILES['firma'];

// Validar tipo de archivo (solo imágenes)
$allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PNG o JPG']);
    exit;
}

// Validar tamaño (máximo 2MB)
if ($file['size'] > 2 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El archivo no debe superar 2MB']);
    exit;
}

try {
    // Cargar variables de entorno
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
    
    // Conectar a la base de datos
    require_once __DIR__ . '/../app/Core/Database.php';
    $db = new App\Core\Database();
    $pdo = $db->getConnection();
    
    // Leer el archivo como BLOB
    $firmaData = file_get_contents($file['tmp_name']);
    
    // Verificar si ya existe una firma para este usuario
    $stmt = $pdo->prepare("SELECT id FROM firmas_digitales WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $existingFirma = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingFirma) {
        // Actualizar firma existente
        $stmt = $pdo->prepare("
            UPDATE firmas_digitales 
            SET firma_data = ?, activa = 1, updated_at = NOW()
            WHERE user_id = ?
        ");
        $stmt->execute([$firmaData, $_SESSION['user_id']]);
    } else {
        // Insertar nueva firma
        $stmt = $pdo->prepare("
            INSERT INTO firmas_digitales (user_id, firma_data, activa, created_at)
            VALUES (?, ?, 1, NOW())
        ");
        $stmt->execute([$_SESSION['user_id'], $firmaData]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Firma digital guardada correctamente'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la firma: ' . $e->getMessage()
    ]);
}
