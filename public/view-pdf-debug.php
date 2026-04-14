<?php
/**
 * Versión de debug para diagnosticar problemas con el PDF
 */

session_start();

if (empty($_SESSION['reviewer_id'])) {
    die('No hay sesión de revisor');
}

$formId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// Cargar .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$name, $value] = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

try {
    $db = \App\Core\Database::getConnection();
    
    echo '<h1>Debug PDF - Formulario #' . $formId . '</h1>';
    
    // 1. Verificar formulario
    $stmt = $db->prepare("SELECT id, form_type, approval_status, approved_by FROM forms WHERE id = ?");
    $stmt->execute([$formId]);
    $form = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo '<h2>1. Formulario Principal</h2>';
    echo '<pre>' . print_r($form, true) . '</pre>';
    
    // 2. Verificar firma del revisor
    if (!empty($form['approved_by'])) {
        $stmtF = $db->prepare("SELECT id, name, firma_digital IS NOT NULL as tiene_firma, firma_mime_type FROM users WHERE name LIKE ? AND role = 'revisor' LIMIT 1");
        $stmtF->execute(['%' . $form['approved_by'] . '%']);
        $revisor = $stmtF->fetch(PDO::FETCH_ASSOC);
        
        echo '<h2>2. Revisor que aprobó</h2>';
        echo '<pre>' . print_r($revisor, true) . '</pre>';
    }
    
    // 3. Verificar declaraciones relacionadas
    $stmtDecl = $db->prepare("SELECT id, form_type, related_form_id FROM forms WHERE related_form_id = ? AND form_type LIKE 'declaracion%'");
    $stmtDecl->execute([$formId]);
    $declaraciones = $stmtDecl->fetchAll(PDO::FETCH_ASSOC);
    
    echo '<h2>3. Declaraciones Relacionadas</h2>';
    echo '<p>Total encontradas: ' . count($declaraciones) . '</p>';
    echo '<pre>' . print_r($declaraciones, true) . '</pre>';
    
    // 4. Verificar firma del usuario
    echo '<h2>4. Firma del Usuario</h2>';
    if (!empty($form['signature_data'])) {
        echo '<p>✓ El formulario tiene firma del usuario</p>';
    } else {
        echo '<p>✗ El formulario NO tiene firma del usuario</p>';
    }
    
} catch (Exception $e) {
    echo '<h2>ERROR</h2>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
