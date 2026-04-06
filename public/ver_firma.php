<?php
/**
 * Ver firma digital
 * Muestra la imagen de la firma desde la base de datos
 */

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

// Verificar que se haya proporcionado el user_id
if (!isset($_GET['user_id'])) {
    http_response_code(400);
    exit('User ID requerido');
}

$userId = (int)$_GET['user_id'];

try {
    // Conectar a la base de datos
    require_once __DIR__ . '/../app/Core/Database.php';
    $db = new App\Core\Database();
    $pdo = $db->getConnection();
    
    // Obtener la firma del usuario
    $stmt = $pdo->prepare("
        SELECT firma_data 
        FROM firmas_digitales 
        WHERE user_id = ? AND activa = 1
    ");
    $stmt->execute([$userId]);
    $firma = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$firma || !$firma['firma_data']) {
        http_response_code(404);
        exit('Firma no encontrada');
    }
    
    // Detectar el tipo de imagen
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($firma['firma_data']);
    
    // Enviar la imagen
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . strlen($firma['firma_data']));
    header('Cache-Control: public, max-age=3600');
    
    echo $firma['firma_data'];
    
} catch (Exception $e) {
    http_response_code(500);
    exit('Error al cargar la firma');
}
