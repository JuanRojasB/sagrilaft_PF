<?php
/**
 * Script para aumentar max_allowed_packet en MySQL
 * Ejecutar una vez para permitir subida de archivos grandes
 */

require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = new \App\Core\Database();
    $pdo = $db->getConnection();
    
    echo "=================================================================\n";
    echo "AUMENTANDO max_allowed_packet EN MYSQL\n";
    echo "=================================================================\n\n";
    
    // Verificar valor actual
    $stmt = $pdo->query("SELECT @@global.max_allowed_packet as current_value");
    $current = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentMB = round($current['current_value'] / 1024 / 1024, 2);
    
    echo "Valor actual: {$current['current_value']} bytes ({$currentMB} MB)\n\n";
    
    // Aumentar a 64MB
    $newValue = 67108864; // 64MB en bytes
    $pdo->exec("SET GLOBAL max_allowed_packet = $newValue");
    
    // Verificar nuevo valor
    $stmt = $pdo->query("SELECT @@global.max_allowed_packet as new_value");
    $new = $stmt->fetch(PDO::FETCH_ASSOC);
    $newMB = round($new['new_value'] / 1024 / 1024, 2);
    
    echo "✓ Nuevo valor: {$new['new_value']} bytes ({$newMB} MB)\n\n";
    
    echo "=================================================================\n";
    echo "IMPORTANTE: Este cambio es TEMPORAL\n";
    echo "=================================================================\n\n";
    echo "Para hacerlo PERMANENTE, edita el archivo de configuración de MySQL:\n\n";
    echo "Windows: C:\\ProgramData\\MySQL\\MySQL Server X.X\\my.ini\n";
    echo "Linux: /etc/mysql/my.cnf o /etc/my.cnf\n\n";
    echo "Agrega en la sección [mysqld]:\n";
    echo "max_allowed_packet = 64M\n\n";
    echo "Luego reinicia el servicio MySQL.\n\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
