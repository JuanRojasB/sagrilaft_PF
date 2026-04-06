<?php
$pdo = new PDO('mysql:host=localhost;dbname=sagrilaft;charset=utf8mb4', 'root', '');

echo "Estructura de la columna form_type:\n";
$result = $pdo->query("DESC forms");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    if ($row['Field'] === 'form_type') {
        echo "Field: " . $row['Field'] . "\n";
        echo "Type: " . $row['Type'] . "\n";
        echo "Null: " . $row['Null'] . "\n";
        echo "Key: " . $row['Key'] . "\n";
        echo "Default: " . $row['Default'] . "\n";
        echo "Extra: " . $row['Extra'] . "\n";
        break;
    }
}

echo "\n═════════════════════════════════════════════════════════════════\n";
echo "Valores posibles en ENUM:\n";

// Si es ENUM, extraer los valores
$result2 = $pdo->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='forms' AND COLUMN_NAME='form_type'");
$colInfo = $result2->fetch(PDO::FETCH_ASSOC);
echo "Column Info: " . $colInfo['COLUMN_TYPE'] . "\n";
