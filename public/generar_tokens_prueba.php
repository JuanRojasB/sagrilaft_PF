<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'sagrilaft';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

$token1 = bin2hex(random_bytes(32));
$token2 = bin2hex(random_bytes(32));

$pdo->prepare("UPDATE forms SET approval_token = ? WHERE id = 80")->execute([$token1]);
$pdo->prepare("UPDATE forms SET approval_token = ? WHERE id = 81")->execute([$token2]);

echo "═════════════════════════════════════════════════════════════════\n";
echo "TOKENS DE APROBACIÓN GENERADOS\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

echo "📋 FORMULARIO 80 (Cliente - Paso 1):\n";
echo "Token: $token1\n";
echo "URL: http://localhost/gestion-sagrilaft/public/approval/$token1\n\n";

echo "📋 FORMULARIO 81 (Declaración - Paso 2):\n";
echo "Token: $token2\n";
echo "URL: http://localhost/gestion-sagrilaft/public/approval/$token2\n\n";

echo "═════════════════════════════════════════════════════════════════\n";
