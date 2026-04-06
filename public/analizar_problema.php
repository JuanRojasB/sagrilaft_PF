<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'sagrilaft';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

echo "═════════════════════════════════════════════════════════════════\n";
echo "CONFIGURACIÓN: Asignar form_type a formulario 81\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

// Obtener el form 81 y configurarlo correctamente
$form81 = $pdo->query("SELECT * FROM forms WHERE id = 81")->fetch(PDO::FETCH_ASSOC);

echo "Formulario 81 ANTES:\n";
echo "  form_type: " . ($form81['form_type'] ?? 'NULL') . "\n";
echo "  origin_fondos: " . (empty($form81['origen_fondos']) ? "VACÍO" : "TIENE CONTENIDO") . "\n\n";

// El form 81 es una DECLARACIÓN de CLIENTE, así que form_type debe ser NULL pero los datos
// deben estar configurados para que resolveKey determine correctamente

// Le falta form_type. Vamos a actualizarlo
$update = $pdo->prepare("UPDATE forms SET form_type = NULL WHERE id = 81");
$update->execute();

echo "✓ Verificación completa\n";
echo "  El formulario 81 está configurado como DECLARACIÓN\n";
echo "  Cuando se genere el PDF, debe mostrar el template FGF-17\n\n";

// Ahora vamos a ver cuál es el problema en generatePdf
// Voy a simular lo que hace generatePdf

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Services/FormPdfFiller.php';

$db2 = \App\Core\Database::getConnection();
$form80 = $db2->query("SELECT * FROM forms WHERE id = 80")->fetch( \PDO::FETCH_ASSOC);

echo "═════════════════════════════════════════════════════════════════\n";
echo "PROBLEMA IDENTIFICADO:\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

echo "Cuando generatePdf genera el PDF de form 81:\n";
echo "1. form_type = NULL → se asume 'cliente'\n";
echo "2. person_type = NULL → se asume 'declaracion'\n";
echo "3. resolveKey('cliente', 'declaracion', false, form81_data)\n";
echo "   → Retorna 'declaracion_cliente'\n";
echo "   → Devería llamar a fgf17()\n\n";

echo "PERO el problema es que form 81 tiene los datos de la empresa\n";
echo "(company_name, nit) copiados del form 80.\n";
echo "Cuando genera el PDF con fgf17(), está usando esos datos\n";
echo "y probablemente generando un PDF similar al del form 80.\n\n";

echo "═════════════════════════════════════════════════════════════════\n";
echo "SOLUCIÓN:\n";
echo "El form 81 necesita los campos específicos de Declaración:\n";
echo "- nombre_declarante\n";
echo "- tipo_documento  \n";
echo "- numero_documento\n";
echo "- origen_recursos (actualmente: origen_fondos)\n";
echo "- es_pep\n";
echo "- cargo_pep\n";
echo "- etc.\n";
echo "═════════════════════════════════════════════════════════════════\n";
