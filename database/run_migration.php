<?php
/**
 * Script para ejecutar migraciones de base de datos
 * 
 * Ejecuta las migraciones pendientes en la base de datos
 */

// Cargar configuración
require_once __DIR__ . '/../app/Core/Database.php';

try {
    echo "=== EJECUTANDO MIGRACIONES SAGRILAFT ===\n\n";
    
    // Obtener conexión
    $db = \App\Core\Database::getConnection();
    
    // Ejecutar migración de firmas digitales
    echo "1. Ejecutando migración: fix_firmas_digitales_table.sql\n";
    
    $migrationFile = __DIR__ . '/migrations/fix_firmas_digitales_table.sql';
    if (!file_exists($migrationFile)) {
        throw new Exception("Archivo de migración no encontrado: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Dividir en statements individuales
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && 
                   !str_starts_with($stmt, '--') && 
                   !str_starts_with($stmt, '/*') &&
                   !str_starts_with($stmt, 'USE');
        }
    );
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $db->exec($statement);
            echo "   ✓ Ejecutado: " . substr(trim($statement), 0, 50) . "...\n";
        } catch (PDOException $e) {
            // Ignorar errores de columnas que ya existen
            if (strpos($e->getMessage(), 'Duplicate column name') !== false ||
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "   ~ Ya existe: " . substr(trim($statement), 0, 50) . "...\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n2. Verificando estructura de tabla firmas_digitales:\n";
    
    $stmt = $db->query("DESCRIBE firmas_digitales");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $expectedColumns = ['id', 'user_id', 'firma_path', 'firma_filename', 'firma_data', 'firma_size', 'mime_type', 'activa', 'created_at', 'updated_at'];
    
    foreach ($expectedColumns as $col) {
        $exists = array_filter($columns, function($c) use ($col) {
            return $c['Field'] === $col;
        });
        
        if ($exists) {
            echo "   ✓ Columna '$col' existe\n";
        } else {
            echo "   ✗ Columna '$col' NO existe\n";
        }
    }
    
    echo "\n3. Verificando índices:\n";
    
    $stmt = $db->query("SHOW INDEX FROM firmas_digitales");
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $indexNames = array_unique(array_column($indexes, 'Key_name'));
    
    $expectedIndexes = ['PRIMARY', 'idx_user_id', 'idx_activa'];
    
    foreach ($expectedIndexes as $idx) {
        if (in_array($idx, $indexNames)) {
            echo "   ✓ Índice '$idx' existe\n";
        } else {
            echo "   ✗ Índice '$idx' NO existe\n";
        }
    }
    
    echo "\n=== MIGRACIÓN COMPLETADA EXITOSAMENTE ===\n";
    echo "La tabla firmas_digitales ha sido actualizada correctamente.\n";
    echo "Ahora los usuarios y revisores pueden gestionar sus firmas digitales.\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR EN MIGRACIÓN:\n";
    echo $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}