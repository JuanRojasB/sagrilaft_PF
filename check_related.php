<?php
$dbConn = new mysqli('localhost', 'root', '', 'sagrilaft');

echo "=== FORM 80 ===\n";
$result = $dbConn->query('SELECT * FROM forms WHERE id = 80');
$row80 = $result->fetch_assoc();
echo json_encode($row80, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "\n\n=== USER 14 ===\n";
$result = $dbConn->query('SELECT * FROM users WHERE id = 14');
$row = $result->fetch_assoc();
echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
