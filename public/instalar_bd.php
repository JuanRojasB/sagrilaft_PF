<?php
/**
 * Script de Instalación de Base de Datos
 * Ejecuta el SQL de instalación completa
 */

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
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

try {
    // Conectar sin seleccionar BD
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';
    
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    
    echo "═════════════════════════════════════════════════════════════════\n";
    echo "   INSTALADOR DE BASE DE DATOS - SISTEMA SAGRILAFT\n";
    echo "═════════════════════════════════════════════════════════════════\n\n";
    
    // Leer y ejecutar SQL
    $sqlFile = __DIR__ . '/../database/INSTALACION_COMPLETA.sql';
    $sql = file_get_contents($sqlFile);
    
    // Dividir por '; --' o similar
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "Ejecutando instrucciones SQL...\n";
    $count = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            $count++;
        } catch (Exception $e) {
            // Algunos errores son esperados (ON DUPLICATE KEY)
            if (strpos($e->getMessage(), 'DUPLICATE') === false) {
                echo "Nota: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "✓ Base de datos instalada exitosamente\n";
    echo "  Instrucciones ejecutadas: $count\n\n";
    
    // Verificar
    $pdo->exec("USE sagrilaft");
    $result = $pdo->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sagrilaft'");
    $tables = $result->fetch()['count'];
    
    echo "✓ Tablas creadas: $tables\n\n";
    
    echo "═════════════════════════════════════════════════════════════════\n";
    echo "Ahora ejecuta: php public/generar_datos_prueba.php\n";
    echo "═════════════════════════════════════════════════════════════════\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
