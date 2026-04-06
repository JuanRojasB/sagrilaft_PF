<?php
$db = new PDO('mysql:host=localhost;dbname=gestion_sagrilaft', 'root', '');
$stmt = $db->query('SELECT id, email, role FROM users LIMIT 10');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    echo "ID: {$u['id']} | Email: {$u['email']} | Role: {$u['role']}\n";
}
