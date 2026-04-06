<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'sagrilaft';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

echo "═════════════════════════════════════════════════════════════════\n";
echo "DEPURACIÓN: BÚSQUEDA DE FORMULARIOS RELACIONADOS\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

// Simular la query del FormController.php
$formId = 80;

echo "Query: SELECT * FROM forms WHERE related_form_id = $formId\n\n";

$stmt = $pdo->prepare("SELECT id, title, form_type, related_form_id, company_name, nit, origen_fondos, es_pep FROM forms WHERE related_form_id = ? ORDER BY id ASC");
$stmt->execute([$formId]);
$relatedForms = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Resultados encontrados: " . count($relatedForms) . "\n\n";

foreach ($relatedForms as $index => $form) {
    echo "=== Formulario Relacionado #" . ($index + 1) . " ===\n";
    echo "ID: {$form['id']}\n";
    echo "Título: {$form['title']}\n";
    echo "Form Type: " . ($form['form_type'] ?? 'NULL') . "\n";
    echo "Related to: {$form['related_form_id']}\n";
    echo "Empresa: {$form['company_name']}\n";
    echo "Origen Fondos: " . (empty($form['origen_fondos']) ? "VACÍO" : "TIENE CONTENIDO") . "\n";
    echo "Es PEP: " . ($form['es_pep'] ?? 'VACÍO') . "\n\n";
}

echo "═════════════════════════════════════════════════════════════════\n";
echo "¿Qué debería pasar:\n";
echo "1. Se ejecuta query para form 80\n";
echo "2. Devuelve form 81 (el único dengan related_form_id = 80)\n";
echo "3. Se genera PDF de form 80\n";
echo "4. Se genera PDF de form 81\n";
echo "5. Se concatenan los dos PDFs\n";
echo "═════════════════════════════════════════════════════════════════\n";
