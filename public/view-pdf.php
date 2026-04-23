<?php
/**
 * Visor de PDF para revisores
 * Archivo directo que no pasa por el router para evitar conflictos con WordPress
 */

session_start();

// Verificar que sea un revisor
if (empty($_SESSION['reviewer_id'])) {
    http_response_code(403);
    die('Acceso denegado - Sesión de revisor requerida');
}

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
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
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

$isRaw = isset($_GET['raw']) && $_GET['raw'] === '1';

try {
    $db = \App\Core\Database::getConnection();

    // Obtener el formulario principal
    $stmt = $db->prepare("SELECT * FROM forms WHERE id = ?");
    $stmt->execute([$formId]);
    $form = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$form) {
        http_response_code(404);
        die('Formulario no encontrado');
    }

    // Mostrar visor iframe si no es raw
    if (!$isRaw) {
        $appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Formulario #<?= $formId ?></title>
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

    // -------------------------------------------------------------------------
    // Cargar campos del revisor y firmas desde form_signatures
    // -------------------------------------------------------------------------
    $stmt = $db->prepare("SELECT * FROM form_signatures WHERE form_id = ? LIMIT 1");
    $stmt->execute([$formId]);
    $sigData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sigData) {
        // Campos del revisor (espacio exclusivo Pollo Fiesta)
        $reviewerFields = [
            'vinculacion', 'fecha_vinculacion', 'actualizacion',
            'consulta_ofac', 'consulta_listas_nacionales', 'consulta_onu', 'consulta_interpol',
            'recibe', 'director_cartera', 'gerencia_comercial',
            'verificado_por', 'preparo', 'reviso', 'nombre_oficial',
            'reviewed_at', 'reviewed_by', 'reviewed_by_name',
        ];
        foreach ($reviewerFields as $field) {
            if (!empty($sigData[$field])) {
                $form[$field] = $sigData[$field];
            }
        }

        // Firma del usuario (representante legal / declarante)
        if (!empty($sigData['user_signature_data'])) {
            $form['signature_data']          = $sigData['user_signature_data'];
            $form['firma_declarante_data']   = $sigData['user_signature_data'];
            $form['firma_representante_data'] = $sigData['user_signature_data'];
            $form['firma_data']              = $sigData['user_signature_data'];
        }

        // Firma del oficial de cumplimiento
        if (!empty($sigData['official_signature_data'])) {
            $form['firma_oficial_data']              = $sigData['official_signature_data'];
            $form['firma_oficial_cumplimiento_data'] = $sigData['official_signature_data'];
        }
    }

    // -------------------------------------------------------------------------
    // Fallback: si no hay firma en form_signatures, buscar en firmas_digitales
    // -------------------------------------------------------------------------

    // Firma del usuario (si aún no está cargada)
    if (empty($form['signature_data']) && !empty($form['user_id'])) {
        $stmt = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
        $stmt->execute([$form['user_id']]);
        $userFirma = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userFirma && !empty($userFirma['firma_data'])) {
            $uri = 'data:' . ($userFirma['mime_type'] ?? 'image/png') . ';base64,' . base64_encode($userFirma['firma_data']);
            $form['signature_data']           = $uri;
            $form['firma_declarante_data']    = $uri;
            $form['firma_representante_data'] = $uri;
            $form['firma_data']               = $uri;
        }
    }

    // Firma del revisor actual (si aún no está cargada)
    if (empty($form['firma_oficial_data'])) {
        $reviewerId = $_SESSION['reviewer_id'];
        $stmt = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
        $stmt->execute([$reviewerId]);
        $firmaRevisor = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($firmaRevisor && !empty($firmaRevisor['firma_data'])) {
            $uri = 'data:' . ($firmaRevisor['mime_type'] ?? 'image/png') . ';base64,' . base64_encode($firmaRevisor['firma_data']);
            $form['firma_oficial_data']              = $uri;
            $form['firma_oficial_cumplimiento_data'] = $uri;
        }
    }

    // -------------------------------------------------------------------------
    // Normalizar accionistas (JSON → arrays)
    // -------------------------------------------------------------------------
    if (!empty($form['accionistas']) && is_string($form['accionistas'])) {
        $accionistas = json_decode($form['accionistas'], true);
        if (is_array($accionistas)) {
            $form['accionista_nombre']        = array_column($accionistas, 'nombre');
            $form['accionista_documento']     = array_column($accionistas, 'documento');
            $form['accionista_participacion'] = array_column($accionistas, 'participacion');
            $form['accionista_nacionalidad']  = array_column($accionistas, 'nacionalidad');
            $form['accionista_cc']            = array_column($accionistas, 'cc');
            $form['accionista_ce']            = array_column($accionistas, 'ce');
        }
    }

    // -------------------------------------------------------------------------
    // Generar PDF principal
    // -------------------------------------------------------------------------
    $filler = new \App\Services\FormPdfFiller();

    $tempData = [
        'role'        => $form['form_type'] ?? 'cliente',
        'user_type'   => $form['form_type'] ?? 'cliente',
        'person_type' => $form['person_type'] ?? 'natural',
    ];

    $pdfContent = $filler->generate($form, $tempData);

    // -------------------------------------------------------------------------
    // Consolidar con declaraciones relacionadas (si existen)
    // -------------------------------------------------------------------------
    $stmtDecl = $db->prepare("SELECT * FROM forms WHERE related_form_id = ? AND form_type LIKE 'declaracion%' ORDER BY id ASC");
    $stmtDecl->execute([$formId]);
    $declaraciones = $stmtDecl->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($declaraciones)) {
        try {
            require_once __DIR__ . '/../app/Libraries/FPDI-2.6.0/src/autoload.php';

            $pdf = new \setasign\Fpdi\Fpdi();

            // Agregar páginas del formulario principal
            $pageCount = $pdf->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($pdfContent));
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $size  = $pdf->getTemplateSize($tplId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }

            // Agregar cada declaración
            foreach ($declaraciones as $declaracion) {
                // Propagar firmas del formulario principal a la declaración
                if (!empty($form['firma_oficial_data'])) {
                    $declaracion['firma_oficial_data']              = $form['firma_oficial_data'];
                    $declaracion['firma_oficial_cumplimiento_data'] = $form['firma_oficial_data'];
                }
                if (!empty($form['signature_data'])) {
                    $declaracion['signature_data']            = $form['signature_data'];
                    $declaracion['firma_declarante_data']     = $form['signature_data'];
                    $declaracion['firma_representante_data']  = $form['signature_data'];
                    $declaracion['firma_data']                = $form['signature_data'];
                }

                $declTempData = [
                    'role'         => $declaracion['form_type'] ?? 'declaracion_cliente',
                    'user_type'    => $declaracion['form_type'] ?? 'declaracion_cliente',
                    'person_type'  => 'declaracion',
                    'related_form' => $form,
                ];

                $declPdf   = $filler->generate($declaracion, $declTempData);
                $pageCount = $pdf->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($declPdf));
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplId = $pdf->importPage($i);
                    $size  = $pdf->getTemplateSize($tplId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplId);
                }
            }

            $pdfContent = $pdf->Output('S');

        } catch (\Exception $e) {
            // En producción continuar con el PDF principal sin consolidar
        }
    }

    // Servir el PDF
    $filename = 'Formulario_' . $formId . '_' . date('Ymd') . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($pdfContent));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $pdfContent;
    exit;

} catch (\Exception $e) {
    http_response_code(500);
    echo '<h1>Error al generar PDF</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
