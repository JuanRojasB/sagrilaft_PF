<?php
echo '<h1>Test PDF Route</h1>';
echo '<p>Si ves esto, PHP funciona correctamente</p>';
echo '<p>REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'No definido') . '</p>';
echo '<p>SCRIPT_NAME: ' . ($_SERVER['SCRIPT_NAME'] ?? 'No definido') . '</p>';
echo '<p>PHP_SELF: ' . ($_SERVER['PHP_SELF'] ?? 'No definido') . '</p>';

echo '<hr>';
echo '<h2>Prueba de ruta del revisor</h2>';

// Simular la sesión del revisor
session_start();
if (empty($_SESSION['reviewer_id'])) {
    echo '<p style="color:red">No hay sesión de revisor activa</p>';
    echo '<p>Sesión actual: <pre>' . print_r($_SESSION, true) . '</pre></p>';
} else {
    echo '<p style="color:green">Sesión de revisor activa: ID ' . $_SESSION['reviewer_id'] . '</p>';
}

echo '<hr>';
echo '<h2>Prueba directa de URL</h2>';
echo '<a href="/gestion-sagrilaft/public/reviewer/form/66/pdf" target="_blank">Probar URL limpia</a><br>';
echo '<a href="/gestion-sagrilaft/public/index.php?route=/reviewer/form/66/pdf" target="_blank">Probar URL con index.php</a>';
