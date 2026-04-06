<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'sagrilaft';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    echo "═════════════════════════════════════════════════════════════════\n";
    echo "ACTUALIZACIÓN: Configurar form_type de Formulario 81\n";
    echo "═════════════════════════════════════════════════════════════════\n\n";

    // Actualizar form 81 para que sea una DECLARACIÓN de Cliente
    $update = $pdo->prepare("UPDATE forms SET form_type = 'declaracion_cliente' WHERE id = 81");
    $update->execute();
    
    echo "✓ Actualizado:\n";
    echo "  Formulario 81: form_type = 'declaracion_cliente'\n\n";

    // Verificar
    $form81 = $pdo->query("SELECT id, title, form_type FROM forms WHERE id = 81")->fetch(PDO::FETCH_ASSOC);
    echo "Verificación:\n";
    echo "  ID: {$form81['id']}\n";
    echo "  Title: {$form81['title']}\n";
    echo "  Form Type: {$form81['form_type']}\n\n";

    echo "═════════════════════════════════════════════════════════════════\n";
    echo "✓ LISTO!\n\n";
    echo "Ahora cuando generes el PDF del formulario 80,\n";
    echo "debería mostrar:\n";
    echo "  1. PDF del Formulario 80 (FGF-08 - Cliente Natural)\n";
    echo "  2. PDF del Formulario 81 (FGF-17 - Declaración Origen Fondos)\n";
    echo "\nNO los mismos dos PDFs juntos.\n";
    echo "═════════════════════════════════════════════════════════════════\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
