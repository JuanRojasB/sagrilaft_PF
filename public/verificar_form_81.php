<?php
$pdo = new PDO('mysql:host=localhost;dbname=sagrilaft;charset=utf8mb4', 'root', '');

echo "Verificando form 81:\n";
$result = $pdo->query("SELECT id, form_type, title FROM forms WHERE id = 81");
$form = $result->fetch(PDO::FETCH_ASSOC);

echo "ID: {$form['id']}\n";
echo "Title: {$form['title']}\n";
echo "Form Type (raw): ";
var_dump($form['form_type']);
echo "\n";
echo "Form Type (is null): " . (is_null($form['form_type']) ? "YES" : "NO") . "\n";
echo "Form Type (is empty): " . (empty($form['form_type']) ? "YES" : "NO") . "\n";
