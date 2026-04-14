<?php
/**
 * Visor de PDF para revisores
 * Archivo directo que no pasa por el router para evitar conflictos con WordPress
 */

// Iniciar sesión
session_start();

// Verificar que sea un revisor
if (empty($_SESSION['reviewer_id'])) {
    http_response_code(403);
    die('Acceso denegado - Sesión de revisor requerida');
}

// Obtener el ID del formulario
$formId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($formId <= 0) {
    http_response_code(400);
    die('ID de formulario inválido');
}

// Autoloader PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Cargar variables de entorno
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$name, $value] = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Verificar si es raw o viewer
$isRaw = isset($_GET['raw']) && $_GET['raw'] === '1';

try {
    $db = \App\Core\Database::getConnection();
    
    // Obtener el formulario
    $stmt = $db->prepare("SELECT * FROM forms WHERE id = ?");
    $stmt->execute([$formId]);
    $form = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$form) {
        http_response_code(404);
        die('Formulario no encontrado');
    }
    
    // Si no es raw, mostrar el visor
    if (!$isRaw) {
        $appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        $title = 'Formulario #' . $formId;
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= htmlspecialchars($title) ?></title>
            <link rel="icon" type="image/png" href="<?= $appUrl ?>/assets/img/orb-logo.png">
            <style>
                html, body { margin: 0; padding: 0; height: 100%; background: #ffffff; }
                .pdf-frame { width: 100%; height: 100%; border: 0; display: block; }
            </style>
        </head>
        <body>
            <iframe class="pdf-frame" src="view-pdf.php?id=<?= $formId ?>&raw=1" title="Visor PDF SAGRILAFT"></iframe>
        </body>
        </html>
        <?php
        exit;
    }
    
    // Generar el PDF
    $filler = new \App\Services\FormPdfFiller();
    
    // Obtener la firma del revisor ACTUAL (el que está en sesión)
    $reviewerId = $_SESSION['reviewer_id'];
    $stmtRevisor = $db->prepare("SELECT firma_digital, firma_mime_type FROM users WHERE id = ? AND role = 'revisor' LIMIT 1");
    $stmtRevisor->execute([$reviewerId]);
    $firmaRevisor = $stmtRevisor->fetch(PDO::FETCH_ASSOC);
    
    // Si el revisor actual tiene firma, usarla en ambos campos
    if ($firmaRevisor && !empty($firmaRevisor['firma_digital'])) {
        $form['firma_oficial_data'] = 'data:' . ($firmaRevisor['firma_mime_type'] ?? 'image/png') . ';base64,' . $firmaRevisor['firma_digital'];
        $form['firma_oficial_cumplimiento_data'] = $form['firma_oficial_data'];
    }
    
    // Asegurar que la firma del usuario esté presente (signature_data del formulario)
    // Este campo ya debería estar en $form desde la base de datos
    
    // Preparar datos temporales
    $tempData = [
        'role' => $form['form_type'] ?? 'cliente',
        'user_type' => $form['form_type'] ?? 'cliente',
        'person_type' => $form['person_type'] ?? 'natural',
    ];
    
    // Generar PDF principal
    $pdfContent = $filler->generate($form, $tempData);
    
    // Buscar declaraciones relacionadas
    $stmtDecl = $db->prepare("SELECT * FROM forms WHERE related_form_id = ? AND form_type LIKE 'declaracion%' ORDER BY id ASC");
    $stmtDecl->execute([$formId]);
    $declaraciones = $stmtDecl->fetchAll(PDO::FETCH_ASSOC);
    
    // Si hay declaraciones, consolidar
    if (!empty($declaraciones)) {
        try {
            require_once __DIR__ . '/../app/Libraries/FPDI-2.6.0/src/autoload.php';
            
            $pdf = new \setasign\Fpdi\Fpdi();
            
            // Agregar páginas del PDF principal
            $pageCount1 = $pdf->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($pdfContent));
            for ($i = 1; $i <= $pageCount1; $i++) {
                $tplId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }
            
            // Agregar cada declaración
            foreach ($declaraciones as $declaracion) {
                try {
                    // Agregar firma del revisor a la declaración (ambos campos)
                    if (!empty($form['firma_oficial_data'])) {
                        $declaracion['firma_oficial_data'] = $form['firma_oficial_data'];
                        $declaracion['firma_oficial_cumplimiento_data'] = $form['firma_oficial_data'];
                    }
                    
                    // Agregar firma del usuario del formulario principal a la declaración
                    // La declaración debe usar la firma del usuario que llenó el formulario principal
                    if (!empty($form['signature_data'])) {
                        $declaracion['signature_data'] = $form['signature_data'];
                        $declaracion['firma_declarante_data'] = $form['signature_data'];
                        $declaracion['firma_representante_data'] = $form['signature_data'];
                        $declaracion['firma_data'] = $form['signature_data'];
                    }
                    
                    $declTempData = [
                        'role' => $declaracion['form_type'] ?? 'cliente',
                        'user_type' => $declaracion['form_type'] ?? 'cliente',
                        'person_type' => 'declaracion',
                        'related_form' => $form,
                    ];
                    
                    $declPdf = $filler->generate($declaracion, $declTempData);
                    
                    // Agregar páginas de la declaración
                    $pageCount2 = $pdf->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($declPdf));
                    for ($i = 1; $i <= $pageCount2; $i++) {
                        $tplId = $pdf->importPage($i);
                        $size = $pdf->getTemplateSize($tplId);
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $pdf->useTemplate($tplId);
                    }
                    
                } catch (Exception $e) {
                    // Log error pero continuar
                    error_log('Error consolidando declaración ID ' . ($declaracion['id'] ?? 'unknown') . ': ' . $e->getMessage());
                    // Mostrar error en el PDF si estamos en desarrollo
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        throw $e;
                    }
                }
            }
            
            $pdfContent = $pdf->Output('S');
            
        } catch (Exception $e) {
            // Si falla toda la consolidación, mostrar error
            error_log('Error en consolidación completa: ' . $e->getMessage());
            // En desarrollo, mostrar el error
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                http_response_code(500);
                die('<h1>Error consolidando PDF</h1><p>' . htmlspecialchars($e->getMessage()) . '</p><pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>');
            }
            // En producción, continuar con el PDF principal sin consolidar
        }
    }
    
    // Nombre del archivo
    $filename = 'Formulario_' . $formId . '_' . date('Ymd') . '.pdf';
    
    // Servir el PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($pdfContent));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    echo $pdfContent;
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    echo '<h1>Error al generar PDF</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
