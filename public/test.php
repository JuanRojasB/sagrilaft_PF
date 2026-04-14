<?php
echo '<h1>PHP funciona correctamente</h1>';
echo '<p>Versión de PHP: ' . phpversion() . '</p>';
echo '<p>Directorio actual: ' . __DIR__ . '</p>';
echo '<p>REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'No definido') . '</p>';
echo '<p>SESSION: ' . (isset($_SESSION['reviewer_id']) ? 'Sí (ID: ' . $_SESSION['reviewer_id'] . ')' : 'No') . '</p>';

// Probar autoloader
spl_autoload_register(function ($class) {
    echo '<p>Intentando cargar clase: ' . $class . '</p>';
});

echo '<hr>';
echo '<h2>Variables de entorno (.env)</h2>';
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo '<p>Archivo .env encontrado</p>';
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$name, $value] = explode('=', $line, 2);
        echo '<p>' . htmlspecialchars(trim($name)) . ' = ' . htmlspecialchars(substr(trim($value), 0, 20)) . '...</p>';
    }
} else {
    echo '<p style="color:red">Archivo .env NO encontrado</p>';
}
