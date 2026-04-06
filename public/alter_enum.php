<?php
$pdo = new PDO('mysql:host=localhost;dbname=sagrilaft;charset=utf8mb4', 'root', '');

echo "═════════════════════════════════════════════════════════════════\n";
echo "ALTERACIÓN: Agregar valores ENUM a form_type\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

// Alterar la columna para agregar nuevos valores ENUM
$alter = "ALTER TABLE forms MODIFY COLUMN form_type ENUM('cliente','proveedor','transportista','declaracion_cliente','declaracion_proveedor') DEFAULT 'cliente'";

echo "Ejecutando ALTER TABLE...\n";
try {
    $pdo->exec($alter);
    echo "✓ Columna form_type alterada exitosamente\n\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Ahora actualizar form 81
echo "Actualizando formulario 81...\n";
$update = "UPDATE forms SET form_type = 'declaracion_cliente' WHERE id = 81";
$rows = $pdo->exec($update);
echo "✓ Rows modificadas: $rows\n\n";

// Verificar
$form81 = $pdo->query("SELECT id, form_type FROM forms WHERE id = 81")->fetch(PDO::FETCH_ASSOC);
echo "Verificación:\n";
echo "  Form 81 form_type: '{$form81['form_type']}'\n\n";

echo "═════════════════════════════════════════════════════════════════\n";
echo "✓ CONFIGURACIÓN COMPLETADA\n\n";
echo "Ahora el PDF del form 80 debe mostrar:\n";
echo "  1. FGF-08 (Cliente Natural)\n";
echo "  2. FGF-17 (Declaración Origen de Fondos)\n";
echo "═════════════════════════════════════════════════════════════════\n";
