<?php
$dbConn = new mysqli('localhost', 'root', '', 'sagrilaft');
$result = $dbConn->query('SELECT * FROM forms WHERE id = 81');
$row = $result->fetch_assoc();
echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
