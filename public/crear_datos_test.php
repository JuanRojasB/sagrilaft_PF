<?php
/**
 * Script para generar datos de prueba
 * Usa la tabla real: forms
 */

declare(strict_types=1);

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
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

use App\Core\Database;

$db = Database::getConnection();
date_default_timezone_set('America/Bogota');

echo "═════════════════════════════════════════════════════════════════\n";
echo "   GENERADOR DE DATOS DE PRUEBA - SAGRILAFT\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

// USUARIO
function crearUsuario($db) {
    $email = 'cliente_prueba@test.com';
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user) {
        echo "✓ Usuario existe: ID {$user['id']}\n";
        return $user['id'];
    }
    
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Cliente Prueba', $email, password_hash('password123', PASSWORD_BCRYPT), 'usuario']);
    $id = (int)$db->lastInsertId();
    echo "✓ Usuario creado: ID $id (email: $email, pass: password123)\n";
    return $id;
}

// FORMULARIO CLIENTE
function crearFormCliente($db, $userId) {
    $stmt = $db->prepare("SELECT id FROM forms WHERE user_id = ? AND form_type = 'cliente' LIMIT 1");
    $stmt->execute([$userId]);
    $form = $stmt->fetch();
    if ($form) {
        echo "✓ Formulario cliente existe: ID {$form['id']}\n";
        return $form['id'];
    }
    
    $stmt = $db->prepare("INSERT INTO forms (
        user_id, title, form_type, status,
        company_name, nit, activity, address, ciudad, 
        phone, email, activos, ingresos, 
        representante_nombre, autoriza_centrales_riesgo,
        created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    $stmt->execute([
        $userId, 'Formulario Vinculación Cliente', 'cliente', 'draft',
        'AGRÍCOLA Y GANADERA DEL NORTE SAS', '800201234',
        'Cultivo y comercialización de productos agrícolas',
        'Carrera 30 No. 45-67, Bogotá', 'Bogotá',
        '3201234567', 'cliente@agricola.com',
        '500000000', '150000000',
        'Juan Carlos Pérez', 'si'
    ]);
    
    $id = (int)$db->lastInsertId();
    echo "✓ Formulario cliente creado: ID $id\n";
    return $id;
}

// FORMULARIO DECLARACIÓN
function crearFormDeclaracion($db, $userId, $formPrincipalId) {
    $stmt = $db->prepare("SELECT id FROM forms WHERE related_form_id = ? LIMIT 1");
    $stmt->execute([$formPrincipalId]);
    $form = $stmt->fetch();
    if ($form) {
        echo "✓ Formulario declaración existe: ID {$form['id']}\n";
        return $form['id'];
    }
    
    $stmt = $db->prepare("INSERT INTO forms (
        user_id, title, form_type, status, related_form_id,
        company_name, nit, origen_fondos, es_pep,
        tiene_cuentas_exterior, autoriza_centrales_riesgo,
        created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    $stmt->execute([
        $userId, 'Declaración Origen de Fondos - Paso 2', NULL, 'draft', $formPrincipalId,
        'AGRÍCOLA Y GANADERA DEL NORTE SAS', '800201234',
        'Los fondos provienen de actividad comercial agrícola documentada durante más de 10 años',
        'no', 'si', 'si'
    ]);
    
    $id = (int)$db->lastInsertId();
    echo "✓ Formulario declaración creado: ID $id (relacionado con $formPrincipalId)\n";
    return $id;
}

// MAIN
try {
    $userId = crearUsuario($db);
    $formClienteId = crearFormCliente($db, $userId);
    $formDeclId = crearFormDeclaracion($db, $userId, $formClienteId);
    
    echo "\n═════════════════════════════════════════════════════════════════\n";
    echo "✓ DATOS GENERADOS EXITOSAMENTE\n";
    echo "═════════════════════════════════════════════════════════════════\n\n";
    
    echo "📋 PARA ACCEDER:\n";
    echo "1. Abre: http://localhost/gestion-sagrilaft/public/login\n";
    echo "   Email: cliente_prueba@test.com\n";
    echo "   Contraseña: password123\n\n";
    
    echo "2. En el dashboard verás:\n";
    echo "   - Formulario $formClienteId: Datos vinculación (Paso 1)\n";
    echo "   - Formulario $formDeclId: Declaración origen fondos (Paso 2)\n\n";
    
    echo "3. Haz clic en los formularios para:\n";
    echo "   - Ver datos completados\n";
    echo "   - Generar PDF\n";
    echo "═════════════════════════════════════════════════════════════════\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
