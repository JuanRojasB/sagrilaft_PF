<?php
/**
 * Script de migración: Crear usuario para Angie Paola Martínez Paredes
 * 
 * Este script:
 * 1. Crea el usuario revisor para Angie
 * 2. Configura su firma digital (si existe el archivo)
 * 
 * Ejecutar desde la raíz del proyecto:
 * php database/migrations/setup_angie_user.php
 */

// Cargar autoloader
require_once __DIR__ . '/../../app/Core/Database.php';

// Cargar variables de entorno
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$name, $value] = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
}

try {
    $db = \App\Core\Database::getConnection();
    
    echo "=================================================================\n";
    echo "CREANDO USUARIO PARA ANGIE PAOLA MARTÍNEZ PAREDES\n";
    echo "=================================================================\n\n";
    
    // 1. Verificar si el usuario ya existe
    echo "1. Verificando si el usuario ya existe...\n";
    $stmt = $db->prepare("SELECT id, name, email, role FROM users WHERE email = ?");
    $stmt->execute(['oficialdecumplimiento@pollo-fiesta.com']);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
        echo "   ✓ Usuario ya existe:\n";
        echo "     - ID: {$existingUser['id']}\n";
        echo "     - Nombre: {$existingUser['name']}\n";
        echo "     - Email: {$existingUser['email']}\n";
        echo "     - Role: {$existingUser['role']}\n\n";
        $userId = $existingUser['id'];
    } else {
        // 2. Crear el usuario
        echo "2. Creando usuario...\n";
        
        // Password: "angie1404*"
        $password = password_hash('angie1404*', PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            'Angie Paola Martínez Paredes',
            'oficialdecumplimiento@pollo-fiesta.com',
            $password,
            'revisor'
        ]);
        
        $userId = $db->lastInsertId();
        
        echo "   ✓ Usuario creado exitosamente:\n";
        echo "     - ID: {$userId}\n";
        echo "     - Nombre: Angie Paola Martínez Paredes\n";
        echo "     - Email: oficialdecumplimiento@pollo-fiesta.com\n";
        echo "     - Usuario: a.martinez\n";
        echo "     - Password: angie1404*\n";
        echo "     - Role: revisor\n\n";
    }
    
    // 3. Verificar si tiene firma digital
    echo "3. Verificando firma digital...\n";
    $stmt = $db->prepare("SELECT firma_digital, firma_mime_type FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $firma = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($firma && !empty($firma['firma_digital'])) {
        echo "   ✓ Usuario ya tiene firma digital configurada\n";
        echo "     - Tipo MIME: {$firma['firma_mime_type']}\n";
        echo "     - Tamaño: " . strlen($firma['firma_digital']) . " bytes (base64)\n\n";
    } else {
        echo "   ⚠ Usuario NO tiene firma digital configurada\n";
        echo "     Para agregar la firma:\n";
        echo "     1. Ir al panel de administración\n";
        echo "     2. Editar usuario 'Angie Paola Martínez Paredes'\n";
        echo "     3. Subir imagen de firma digital\n\n";
    }
    
    echo "=================================================================\n";
    echo "MIGRACIÓN COMPLETADA\n";
    echo "=================================================================\n\n";
    
    echo "CREDENCIALES DE ACCESO:\n";
    echo "-----------------------\n";
    echo "URL: " . ($_ENV['APP_URL'] ?? 'http://localhost') . "/login\n";
    echo "Email: oficialdecumplimiento@pollo-fiesta.com\n";
    echo "Password: angie1404*\n\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
