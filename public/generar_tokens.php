<?php
// Generar token de aprobación para formularios de prueba

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$db = $_ENV['DB_NAME'] ?? 'sagrilaft';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    echo "═════════════════════════════════════════════════════════════════\n";
    echo "   GENERADOR DE TOKENS DE APROBACIÓN\n";
    echo "═════════════════════════════════════════════════════════════════\n\n";
    
    // Obtener formularios sin token
    $stmt = $pdo->prepare("SELECT id, title, company_name FROM forms WHERE approval_token IS NULL LIMIT 5");
    $stmt->execute();
    $forms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$forms) {
        echo "No hay formularios sin token de aprobación.\n";
        exit(0);
    }
    
    foreach ($forms as $form) {
        $token = bin2hex(random_bytes(32));
        
        $update = $pdo->prepare("UPDATE forms SET approval_token = ?, approval_status = 'pending' WHERE id = ?");
        $update->execute([$token, $form['id']]);
        
        echo "Formulario ID: {$form['id']}\n";
        echo "  Empresa: {$form['company_name']}\n";
        echo "  Título: {$form['title']}\n";
        echo "  Token: $token\n";
        echo "  URL: http://localhost/gestion-sagrilaft/public/approval/$token\n";
        echo "\n";
    }
    
    echo "═════════════════════════════════════════════════════════════════\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
