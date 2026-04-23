<?php
/**
 * Visor de adjuntos para revisores
 */
session_start();

if (empty($_SESSION['reviewer_id'])) {
    http_response_code(403);
    die('Acceso denegado');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    die('ID inválido');
}

// Autoloader
spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len     = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require $file;
});

// Cargar .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$name, $value] = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

try {
    $db   = \App\Core\Database::getConnection();
    $stmt = $db->prepare("SELECT filename, file_data, mime_type, filesize FROM form_attachments WHERE id = ?");
    $stmt->execute([$id]);
    $attachment = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$attachment || empty($attachment['file_data'])) {
        http_response_code(404);
        die('Adjunto no encontrado');
    }

    $mime     = $attachment['mime_type'] ?? 'application/octet-stream';
    $filename = $attachment['filename'] ?? ('adjunto_' . $id);
    $inline   = in_array($mime, ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp']);

    header('Content-Type: ' . $mime);
    header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . addslashes($filename) . '"');
    header('Content-Length: ' . strlen($attachment['file_data']));
    header('Cache-Control: private, max-age=0, must-revalidate');

    echo $attachment['file_data'];
    exit;

} catch (\Exception $e) {
    http_response_code(500);
    die('Error al cargar el adjunto');
}
