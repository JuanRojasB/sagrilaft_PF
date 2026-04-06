<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'sagrilaft';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

echo "═════════════════════════════════════════════════════════════════\n";
echo "COMPARACIÓN DE FORMULARIOS 80 vs 81\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

echo "=== FORMULARIO 80 (Cliente - Paso 1) ===\n";
$form80 = $pdo->query("SELECT * FROM forms WHERE id = 80")->fetch(PDO::FETCH_ASSOC);
echo "ID: {$form80['id']}\n";
echo "Título: {$form80['title']}\n";
echo "Tipo: {$form80['form_type']}\n";
echo "Empresa: {$form80['company_name']}\n";
echo "NIT: {$form80['nit']}\n";
echo "Origen Fondos: " . (empty($form80['origen_fondos']) ? "VACÍO" : substr($form80['origen_fondos'], 0, 50) . "...") . "\n";
echo "Es PEP: {$form80['es_pep']}\n";
echo "Related ID: {$form80['related_form_id']}\n";

echo "\n=== FORMULARIO 81 (Declaración - Paso 2) ===\n";
$form81 = $pdo->query("SELECT * FROM forms WHERE id = 81")->fetch(PDO::FETCH_ASSOC);
echo "ID: {$form81['id']}\n";
echo "Título: {$form81['title']}\n";
echo "Tipo: {$form81['form_type']}\n";
echo "Empresa: {$form81['company_name']}\n";
echo "NIT: {$form81['nit']}\n";
echo "Origen Fondos: " . (empty($form81['origen_fondos']) ? "VACÍO" : substr($form81['origen_fondos'], 0, 50) . "...") . "\n";
echo "Es PEP: {$form81['es_pep']}\n";
echo "Related ID (vinculado a): {$form81['related_form_id']}\n";

echo "\n═════════════════════════════════════════════════════════════════\n";
echo "CONCLUSIÓN:\n";
echo "- Formulario 80: Datos generales del cliente\n";
echo "- Formulario 81: Datos de declaración (related_form_id = 80)\n";
echo "- Ambos tienen muchos campos compartidos (empresa, nit, etc.)\n";
echo "═════════════════════════════════════════════════════════════════\n";
