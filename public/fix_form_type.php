<?php
$pdo =  new PDO('mysql:host=localhost;dbname=sagrilaft;charset=utf8mb4', 'root', '');

echo "═════════════════════════════════════════════════════════════════\n";
echo "ACTUALIZAR form_type DE FORM 81\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

// ANTES
$form_before = $pdo->query("SELECT form_type FROM forms WHERE id = 81")->fetch(PDO::FETCH_ASSOC);
echo "ANTES: form_type = '" . $form_before['form_type'] . "'\n\n";

// UPDATE - sin usar prepared statement para simplificar
$sql = "UPDATE forms SET form_type = 'declaracion_cliente' WHERE id = 81";
echo "Ejecutando: $sql\n";
$result = $pdo->exec($sql);
echo "Rows affected: $result\n\n";

// DESPUÉS
$form_after = $pdo->query("SELECT form_type FROM forms WHERE id = 81")->fetch(PDO::FETCH_ASSOC);
echo "DESPUÉS: form_type = '" . $form_after['form_type'] . "'\n\n";

if ($form_after['form_type'] === 'declaracion_cliente') {
    echo "✓ ACTUALIZACIÓN EXITOSA\n";
} else {
    echo "✗ LA ACTUALIZACIÓN NO FUNCIONÓ\n";
    echo "Probando otra forma...\n\n";
    
    // Probar con explicit cast
    $sql2 = "UPDATE forms SET form_type = CAST('declaracion_cliente' AS CHAR) WHERE id = 81";
    $pdo->exec($sql2);
    
    $form_after2 = $pdo->query("SELECT form_type FROM forms WHERE id = 81")->fetch(PDO::FETCH_ASSOC);
    echo "DESPUÉS del segundo intento: form_type = '" . $form_after2['form_type'] . "'\n";
}

echo "\n═════════════════════════════════════════════════════════════════\n";
