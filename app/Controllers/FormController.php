<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Form;
use App\Services\PdfService;
use App\Services\Logger;

/**
 * Controlador de Formularios
 * 
 * Gestiona todas las operaciones relacionadas con formularios:
 * - Listar, crear, ver y generar PDFs de formularios
 * - Enviar notificaciones por email a revisores
 * - Gestionar tokens de aprobación
 * 
 * @package App\Controllers
 */
class FormController extends Controller
{
    private Form $formModel;
    private PdfService $pdfService;
    private Logger $logger;

    public function __construct()
    {
        $this->formModel = new Form();
        $this->pdfService = new PdfService();
        $this->logger = new Logger();
    }

    /**
     * Listar todos los formularios del usuario actual
     * 
     * Muestra una lista de formularios creados por el usuario logueado.
     */
    public function index(): void
    {
        $forms = $this->formModel->getByUserId($_SESSION['user_id']);
        
        $this->view('forms/index', [
            'forms' => $forms
        ]);
    }

    /**
     * Mostrar formulario de creación
     * 
     * Renderiza la vista para crear un nuevo formulario.
     */
    public function create(): void
    {
        $this->view('forms/create', [
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Guardar nuevo formulario
     * 
     * Crea un formulario en la base de datos con los nuevos campos,
     * sube los archivos PDF adjuntos y envía notificación por email al revisor.
     * 
     * @return void Respuesta JSON con resultado
     */
    public function store(): void
    {
        // CRÍTICO: Desactivar TODOS los errores visibles para respuestas JSON limpias
        error_reporting(0);
        ini_set('display_errors', '0');
        
        // Limpiar cualquier salida previa
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Iniciar nuevo buffer limpio
        ob_start();
        
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $data = [
            'user_id' => $_SESSION['user_id'],
            'title' => $this->input('title'),
            'content' => $this->input('content'),
            'company_name' => $this->input('company_name'),
            'nit' => $this->input('nit'),
            'activity' => $this->input('activity'),
            'address' => $this->input('address'),
            'phone' => $this->input('phone'),
            'status' => $this->input('status', 'draft')
        ];

        try {
            $formId = $this->formModel->create($data);
            
            // Procesar archivos PDF adjuntos
            $uploadedFiles = [];
            if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
                $uploadedFiles = $this->handleFileUploads($formId);
            }
            
            $this->logger->info('Form created', [
                'form_id' => $formId,
                'user_id' => $_SESSION['user_id'],
                'attachments_count' => count($uploadedFiles)
            ]);
            
            // Send email notification
            $this->sendFormNotification($formId, $data, $uploadedFiles);
            
            $this->json([
                'success' => true,
                'message' => 'Formulario creado exitosamente',
                'form_id' => $formId
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Form creation failed', [
                'error' => $e->getMessage()
            ]);
            
            $this->json(['error' => 'Error al crear formulario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Manejar subida de archivos PDF
     * Guarda los archivos directamente en la base de datos como BLOB
     * 
     * @param int $formId ID del formulario
     * @return array Lista de archivos subidos
     */
    private function handleFileUploads(int $formId): array
    {
        $uploadedFiles = [];
        $files = $_FILES['documents'];
        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            // Validar tipo de archivo
            if ($files['type'][$i] !== 'application/pdf') {
                throw new \Exception('Solo se permiten archivos PDF');
            }

            // Validar tamaño (10MB máximo)
            $maxSize = 10 * 1024 * 1024;
            if ($files['size'][$i] > $maxSize) {
                throw new \Exception('Los archivos no deben superar 10MB');
            }

            // Leer contenido del archivo
            $fileData = file_get_contents($files['tmp_name'][$i]);
            if ($fileData === false) {
                throw new \Exception('Error al leer archivo: ' . $files['name'][$i]);
            }

            $originalName = $files['name'][$i];

            // Guardar en base de datos como BLOB
            $db = $this->getConnection();
            $stmt = $db->prepare("
                INSERT INTO form_attachments (form_id, filename, filepath, filesize, file_data, mime_type)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $formId,
                $originalName,
                '', // filepath vacío (ya no se usa)
                $files['size'][$i],
                $fileData,
                'application/pdf'
            ]);
            
            $attachmentId = $db->lastInsertId();

            $uploadedFiles[] = [
                'id' => $attachmentId,
                'filename' => $originalName,
                'size' => $files['size'][$i]
            ];
        }

        return $uploadedFiles;
    }

    /**
     * Enviar notificación por email para nuevo formulario
     * 
     * Genera un token único de aprobación y envía email al revisor
     * con un enlace para aprobar o rechazar el formulario.
     * 
     * @param int $formId ID del formulario creado
     * @param array $data Datos del formulario
     * @param array $uploadedFiles Lista de archivos adjuntos
     */
    private function sendFormNotification(int $formId, array $data, array $uploadedFiles = []): void
    {
        try {
            $mailService = new \App\Services\MailService();
            
            // Generate unique approval token
            $approvalToken = bin2hex(random_bytes(32));
            
            // Save token to database
            $this->formModel->updateApprovalToken($formId, $approvalToken);
            
            $to = $_ENV['MAIL_ALERT_TO'] ?? 'juan.david.rojas.burbano0@gmail.com';
            $subject = 'SAGRILAFT - Nuevo Formulario para Aprobar';
            
            $approvalUrl = $_ENV['APP_URL'] . "/approval/{$approvalToken}";
            
            // Generar lista de archivos adjuntos
            $attachmentsHtml = '';
            if (!empty($uploadedFiles)) {
                $attachmentsHtml = "
                <div class='info-item full'>
                    <span class='label'>Documentos Adjuntos (" . count($uploadedFiles) . ")</span>
                    <ul style='margin: 8px 0; padding-left: 20px; color: #cbd5e1;'>";
                foreach ($uploadedFiles as $file) {
                    $size = number_format($file['size'] / 1024 / 1024, 2);
                    $attachmentsHtml .= "<li style='margin: 4px 0; font-size: 13px;'>{$file['filename']} ({$size} MB)</li>";
                }
                $attachmentsHtml .= "</ul>
                </div>";
            }
            
            $body = \App\Helpers\EmailHelper::emailHeader('Nuevo Formulario SAGRILAFT #' . $formId) . "
            <p class='msg'>Se ha recibido un nuevo formulario que requiere revisión y aprobación.</p>
            <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                <tr>
                    <td style='padding:0 6px 6px 0;width:33%;vertical-align:top;'>
                        <div class='info-item'><span class='info-label'>Empresa/Persona</span><span class='info-value'>" . ($data['company_name'] ?? 'N/A') . "</span></div>
                    </td>
                    <td style='padding:0 6px 6px 0;width:33%;vertical-align:top;'>
                        <div class='info-item'><span class='info-label'>NIT/Documento</span><span class='info-value'>" . ($data['nit'] ?? 'N/A') . "</span></div>
                    </td>
                    <td style='padding:0 0 6px 0;width:33%;vertical-align:top;'>
                        <div class='info-item'><span class='info-label'>Teléfono</span><span class='info-value'>" . ($data['phone'] ?? 'N/A') . "</span></div>
                    </td>
                </tr>
                <tr>
                    <td colspan='3' style='padding:0 0 6px 0;'>
                        <div class='info-item'>
                            <span class='info-label'>Dirección</span>
                            <span class='info-value'>" . ($data['address'] ?? 'N/A') . "</span>
                            " . \App\Helpers\EmailHelper::getGoogleMapsLink($data['address'] ?? '') . "
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan='3' style='padding:0 0 6px 0;'>
                        <div class='info-item'>
                            <span class='info-label'>Actividad Económica</span>
                            <span class='info-value'>" . ($data['activity'] ?? 'N/A') . "</span>
                        </div>
                    </td>
                </tr>
            </table>
            {$attachmentsHtml}
            <div style='text-align:center;margin-top:24px;'>
                <a href='{$approvalUrl}' class='btn' style='display:inline-block;padding:10px 22px;background:#1d4ed8;color:#ffffff;text-decoration:none;border-radius:5px;font-weight:600;font-size:13px;'>Revisar Formulario</a>
            </div>
            " . \App\Helpers\EmailHelper::emailFooter();
            
            // Enviar con logo y firma embebidos
            $embeddedImages = [];
            $logoPath = \App\Helpers\EmailHelper::getLogoImagePath();
            if ($logoPath) $embeddedImages['logo'] = $logoPath;
            $sigPath = \App\Helpers\EmailHelper::getSignatureImagePath();
            if ($sigPath) $embeddedImages['signature'] = $sigPath;
            $mailService->sendViaSMTPWithImages($to, $subject, $body, $embeddedImages);
            
        } catch (\Exception $e) {
            // Log error but don't fail the form creation
            $this->logger->error('Email notification failed', [
                'form_id' => $formId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mostrar detalles de un formulario
     * 
     * Renderiza la vista con toda la información del formulario.
     * 
     * @param string $id ID del formulario
     */
    public function show(string $id): void
    {
        $form = $this->formModel->findById((int)$id);
        
        if (!$form) {
            http_response_code(404);
            echo 'Formulario no encontrado';
            return;
        }

        // Obtener archivos adjuntos
        $attachmentModel = new \App\Models\Attachment();
        $attachments = $attachmentModel->getByFormId((int)$id);

        // Obtener información del revisor si existe
        $reviewerInfo = null;
        if (!empty($form['reviewed_by'] ?? null)) {
            $db = $this->getConnection();
            $stmt = $db->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmt->execute([$form['reviewed_by']]);
            $reviewerInfo = $stmt->fetch();
        }

        $this->view('forms/show', [
            'form' => $form,
            'attachments' => $attachments,
            'reviewer_info' => $reviewerInfo
        ]);
    }

    /**
     * Ver formulario completo (para revisión)
     * Muestra TODOS los formularios y datos relacionados
     */
    public function viewComplete(string $id): void
    {
        $form = $this->formModel->findById((int)$id);
        
        if (!$form) {
            http_response_code(404);
            echo 'Formulario no encontrado';
            return;
        }

        
        // Obtener archivos adjuntos
        $attachmentModel = new \App\Models\Attachment();
        $attachments = $attachmentModel->getByFormId((int)$id);

        // Buscar formularios relacionados (mismo usuario, mismo NIT, creados cerca en el tiempo)
        $db = \App\Core\Database::getConnection();
        $relatedForms = [];
        
        // Buscar por mismo NIT y usuario en un rango de 24 horas
        if (!empty($form['nit'])) {
            $stmt = $db->prepare("
                SELECT * FROM forms 
                WHERE nit = ? 
                AND user_id = ? 
                AND id != ?
                AND ABS(TIMESTAMPDIFF(HOUR, created_at, ?)) <= 24
                ORDER BY created_at ASC
            ");
            $stmt->execute([$form['nit'], $form['user_id'], $id, $form['created_at']]);
            $relatedForms = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Renderizar la vista con todos los datos
        $this->view('forms/view_complete', [
            'form' => $form,
            'relatedForms' => $relatedForms,
            'attachments' => $attachments
        ]);
    }

    /**
     * Generar PDF del formulario
     * 
     * Crea un archivo PDF con los datos del formulario usando
     * PHP puro (sin librerías externas) y lo descarga automáticamente.
     * 
     * @param string $id ID del formulario
     */
    public function generatePdf(string $id): void
    {
        $form = $this->formModel->findById((int)$id);
        
        if (!$form) {
            http_response_code(404);
            echo 'Formulario no encontrado';
            return;
        }

        $codeMap = [
            'cliente_natural' => 'FGF-08',
            'cliente_juridica' => 'FGF-16',
            'declaracion_fondos_clientes' => 'FGF-17',
            'declaracion_cliente' => 'FGF-17',
            'proveedor_natural' => 'FCO-05',
            'proveedor_juridica' => 'FCO-02',
            'proveedor_internacional' => 'FCO-04',
            'declaracion_fondos_proveedores' => 'FCO-03',
            'declaracion_proveedor' => 'FCO-03',
        ];
        $formType = (string)($form['form_type'] ?? '');
        $code = $codeMap[$formType] ?? 'SAGRILAFT';
        $title = trim($code . ' - ' . ($form['title'] ?? ('Formulario #' . $id)));

        // Mostrar visor HTML (con favicon/título) y cargar el PDF real en iframe.
        // El binario del PDF se sirve al pedir ?raw=1.
        $isRaw = isset($_GET['raw']) && (string)$_GET['raw'] === '1';
        if (!$isRaw) {
            $path = strtok($_SERVER['REQUEST_URI'] ?? '', '?') ?: ('/forms/' . $id . '/pdf');
            $this->view('forms/pdf_viewer', [
                'title' => $title,
                'pdf_url' => $path . '?raw=1',
            ]);
            return;
        }

        try {
            $tempData = [
                'role'        => $form['form_type'] ?? $form['user_type'] ?? 'cliente',
                'user_type'   => $form['form_type'] ?? $form['user_type'] ?? 'cliente',
                'person_type' => $form['person_type'] ?? 'natural',
            ];

            // Si es una declaración, obtener el formulario padre relacionado
            $db = \App\Core\Database::getConnection();
            if (strpos($form['form_type'], 'declaracion_') === 0 && !empty($form['related_form_id'])) {
                $stmt = $db->prepare("SELECT * FROM forms WHERE id = ?");
                $stmt->execute([(int)$form['related_form_id']]);
                $parentForm = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($parentForm) {
                    $tempData['related_form'] = $parentForm;
                }
            }

            // Deserializar accionistas
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

            // Inyectar firma del oficial si el formulario está aprobado
            if (in_array($form['approval_status'] ?? '', ['approved', 'approved_pending'])) {
                // Buscar la firma activa del revisor por nombre, o la primera activa disponible
                $approvedBy = $form['approved_by'] ?? '';
                if ($approvedBy) {
                    $stmtF = $db->prepare("
                        SELECT fd.firma_data, fd.mime_type 
                        FROM firmas_digitales fd
                        INNER JOIN users u ON fd.user_id = u.id
                        WHERE fd.activa = 1 AND u.name LIKE ? LIMIT 1
                    ");
                    $stmtF->execute(['%' . $approvedBy . '%']);
                    $firma = $stmtF->fetch();
                    if (!$firma) {
                        // Fallback: primera firma activa
                        $stmtF = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE activa = 1 LIMIT 1");
                        $stmtF->execute();
                        $firma = $stmtF->fetch();
                    }
                } else {
                    $stmtF = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE activa = 1 LIMIT 1");
                    $stmtF->execute();
                    $firma = $stmtF->fetch();
                }
                if ($firma && !empty($firma['firma_data'])) {
                    $form['firma_oficial_data'] = 'data:' . $firma['mime_type'] . ';base64,' . base64_encode($firma['firma_data']);
                    $form['firma_oficial_cumplimiento_data'] = $form['firma_oficial_data'];
                }
            }

            $filler = new \App\Services\FormPdfFiller();
            $pdfContent = $filler->generate($form, $tempData);

            // Buscar formulario relacionado (declaración) y concatenar — solo el primero
            $stmt = $db->prepare("SELECT * FROM forms WHERE related_form_id = ? AND form_type LIKE 'declaracion%' ORDER BY id DESC LIMIT 1");
            $stmt->execute([(int)$id]);
            $relatedForms = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($relatedForms as $related) {
                try {
                    // Propagar firma al formulario relacionado también
                    if (!empty($form['firma_oficial_data'])) {
                        $related['firma_oficial_data'] = $form['firma_oficial_data'];
                        $related['firma_oficial_cumplimiento_data'] = $form['firma_oficial_data'];
                    }
                    $relTempData = [
                        'role'        => $related['form_type'] ?? 'cliente',
                        'user_type'   => $related['form_type'] ?? 'cliente',
                        'person_type' => $related['person_type'] ?? 'declaracion',
                        'related_form' => $form,
                    ];
                    $relPdf = $filler->generate($related, $relTempData);
                    // Concatenar PDFs usando FPDI si está disponible, si no simplemente adjuntar
                    $pdfContent = $this->mergePdfs($pdfContent, $relPdf);
                } catch (\Exception $e) {
                    $this->logger->warning('No se pudo incluir formulario relacionado en PDF', [
                        'related_id' => $related['id'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $filename = $form['generated_pdf_filename']
                ?? ($code . "_Formulario_{$id}.pdf");

            header('Content-Type: application/pdf');
            header('Link: </assets/img/orb-logo.png>; rel="icon"; type="image/png"');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($pdfContent));
            echo $pdfContent;
            exit;
            
        } catch (\Exception $e) {
            $this->logger->error('PDF generation failed', [
                'form_id' => $id,
                'error' => $e->getMessage()
            ]);
            http_response_code(500);
            echo 'Error al generar PDF: ' . $e->getMessage();
        }
    }
    
    /**
     * Concatenar dos PDFs usando FPDI
     */
    private function mergePdfs(string $pdf1, string $pdf2): string
    {
        try {
            require_once __DIR__ . '/../Libraries/fpdf.php';
            require_once __DIR__ . '/../Libraries/FPDI-2.6.0/src/autoload.php';

            $fpdi = new \setasign\Fpdi\Fpdi();
            $fpdi->SetAutoPageBreak(false);
            $fpdi->SetMargins(0, 0, 0);

            // Importar páginas del primer PDF
            $count1 = $fpdi->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($pdf1));
            for ($i = 1; $i <= $count1; $i++) {
                $tpl = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($size['width'] > $size['height'] ? 'L' : 'P', [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            // Importar páginas del segundo PDF
            $count2 = $fpdi->setSourceFile(\setasign\Fpdi\PdfParser\StreamReader::createByString($pdf2));
            for ($i = 1; $i <= $count2; $i++) {
                $tpl = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($size['width'] > $size['height'] ? 'L' : 'P', [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            return $fpdi->Output('S');
        } catch (\Exception $e) {
            // Si falla el merge, devolver solo el primero
            $this->logger->warning('mergePdfs falló, devolviendo solo el primero', ['error' => $e->getMessage()]);
            return $pdf1;
        }
    }

    /**
     * Servir PDF desde la base de datos
     * 
     * @param array $form Datos del formulario con el PDF
     * @return void
     */
    private function servePdfFromDatabase(array $form): void
    {
        $filename = $form['generated_pdf_filename'] ?? 'formulario_' . $form['id'] . '.pdf';
        $mimeType = $form['pdf_mime_type'] ?? 'application/pdf';
        $size = $form['generated_pdf_size'] ?? strlen($form['generated_pdf_content']);
        
        header('Content-Type: ' . $mimeType);
        header('Link: </assets/img/orb-logo.png>; rel="icon"; type="image/png"');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . $size);
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $form['generated_pdf_content'];
        exit;
    }

    /**
     * Descargar archivo adjunto desde la base de datos
     * 
     * @param string $id ID del adjunto
     */
    /**
     * Descarga un adjunto para revisores (sesión de revisor, sin auth de usuario)
     */
    public function downloadAttachmentReviewer(string $id): void
        {
            if (empty($_SESSION['reviewer_id'])) {
                http_response_code(403);
                echo 'Acceso denegado';
                return;
            }

            $db = $this->getConnection();
            $stmt = $db->prepare("SELECT filename, file_data, mime_type FROM form_attachments WHERE id = ?");
            $stmt->execute([$id]);
            $attachment = $stmt->fetch();

            if (!$attachment || empty($attachment['file_data'])) {
                http_response_code(404);
                echo 'Archivo no encontrado';
                return;
            }

            $mime     = $attachment['mime_type'] ?? 'application/octet-stream';
            $filename = $attachment['filename'] ?? 'archivo';
            $isPdf    = str_contains($mime, 'pdf');
            $isImage  = str_starts_with($mime, 'image/');

            // PDFs e imágenes sin ?raw: mostrar en visor HTML con favicon y título correcto
            if (($isPdf || $isImage) && !isset($_GET['raw'])) {
                $rawUrl = '/reviewer/attachment/' . (int)$id . '?raw=1';
                $title  = pathinfo($filename, PATHINFO_FILENAME);
                $this->view('forms/pdf_viewer', [
                    'title'   => $title,
                    'pdf_url' => $rawUrl,
                ]);
                return;
            }

            // Servir binario directamente (?raw=1 o archivos no visualizables)
            header('Content-Type: ' . $mime);
            header('Content-Disposition: ' . ($isPdf || $isImage ? 'inline' : 'attachment') . '; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($attachment['file_data']));
            echo $attachment['file_data'];
            exit;
        }


    public function downloadAttachment(string $id): void
    {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT filename, file_data, mime_type, filesize FROM form_attachments WHERE id = ?");
        $stmt->execute([$id]);
        $attachment = $stmt->fetch();

        if (!$attachment || empty($attachment['file_data'])) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        header('Content-Type: ' . ($attachment['mime_type'] ?? 'application/pdf'));
        header('Content-Disposition: attachment; filename="' . $attachment['filename'] . '"');
        header('Content-Length: ' . strlen($attachment['file_data']));
        echo $attachment['file_data'];
        exit;
    }

    /**
     * Consolidar todos los PDFs adjuntos en uno solo
     */
    public function consolidatePDFs(string $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $csrfToken = $input['csrf_token'] ?? '';

        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        try {
            $db = $this->getConnection();
            $stmt = $db->prepare("SELECT id, filename, file_data FROM form_attachments WHERE form_id = ? ORDER BY id");
            $stmt->execute([$id]);
            $attachments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $tempDir = sys_get_temp_dir() . '/sagrilaft_' . uniqid();
            mkdir($tempDir, 0777, true);

            $pdfPaths = [];
            foreach ($attachments as $attachment) {
                if (empty($attachment['file_data'])) {
                    continue;
                }
                
                $tempFile = $tempDir . '/' . $attachment['id'] . '.pdf';
                file_put_contents($tempFile, $attachment['file_data']);
                $pdfPaths[] = $tempFile;
            }

            if (empty($pdfPaths)) {
                rmdir($tempDir);
                $this->json(['error' => 'No se encontraron archivos PDF válidos'], 400);
            }

            // Consolidar PDFs
            $pdfService = new \App\Services\PdfConsolidatorService();
            $outputPath = $tempDir . '/consolidated.pdf';
            $result = $pdfService->consolidatePDFs($pdfPaths, $outputPath);

            // Leer PDF consolidado
            $consolidatedData = file_get_contents($outputPath);

            // Limpiar archivos temporales
            foreach ($pdfPaths as $path) {
                @unlink($path);
            }
            @unlink($outputPath);
            @rmdir($tempDir);

            // Guardar PDF consolidado en la base de datos
            $stmt = $db->prepare("
                INSERT INTO form_consolidated_pdfs (form_id, filepath, file_data, total_pages, signed)
                VALUES (?, ?, ?, ?, 0)
            ");
            $stmt->execute([
                $id,
                '', // filepath vacío (ya no se usa)
                $consolidatedData,
                $result['total_pages']
            ]);
            $consolidatedId = $db->lastInsertId();

            $this->logger->info('PDFs consolidated in database', [
                'form_id' => $id,
                'consolidated_id' => $consolidatedId,
                'total_attachments' => count($attachments),
                'size_bytes' => strlen($consolidatedData)
            ]);

            $this->json([
                'success' => true,
                'message' => 'PDFs consolidados exitosamente. Se firmará al aprobar el formulario.',
                'consolidated_id' => $consolidatedId,
                'total_pages' => $result['total_pages'],
                'download_url' => '/forms/consolidated/' . $consolidatedId . '/download'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('PDF consolidation failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al consolidar PDFs: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Descargar PDF consolidado desde la base de datos
     * 
     * @param string $id ID del PDF consolidado
     */
    public function downloadConsolidatedPDF(string $id): void
    {
        try {
            $db = $this->formModel->getConnection();
            
            $stmt = $db->prepare("SELECT form_id, file_data, signed FROM form_consolidated_pdfs WHERE id = ?");
            $stmt->execute([$id]);
            $consolidated = $stmt->fetch();

            if (!$consolidated || empty($consolidated['file_data'])) {
                http_response_code(404);
                echo 'PDF consolidado no encontrado';
                return;
            }

            $filename = 'SAGRILAFT_Consolidado_Form_' . $consolidated['form_id'] . '.pdf';
            if ($consolidated['signed']) {
                $filename = 'SAGRILAFT_Firmado_Form_' . $consolidated['form_id'] . '.pdf';
            }

            header('Content-Type: application/pdf');
            header('Link: </assets/img/orb-logo.png>; rel="icon"; type="image/png"');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($consolidated['file_data']));
            echo $consolidated['file_data'];
            exit;

        } catch (\Exception $e) {
            $this->logger->error('Download consolidated PDF failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            echo 'Error al descargar PDF';
        }
    }

    /**
     * Obtener conexión a la base de datos (helper)
     */
    private function getConnection(): \PDO
    {
        return \App\Core\Database::getConnection();
    }

    /**
     * Mostrar formulario de creación (sin autenticación)
     * Usa datos de sesión temporal del registro inicial
     */
    public function createDirect(): void
    {
        // Verificar que existan datos temporales
        if (!isset($_SESSION['temp_user_data'])) {
            header('Location: ' . $_ENV['APP_URL']);
            exit;
        }

        $tempData = $_SESSION['temp_user_data'];
        
        // Cargar asesores comerciales
        $asesorModel = new \App\Models\AsesorComercial();
        $asesoresGrouped = $asesorModel->getAllGroupedBySede();

        // Obtener nombre del asesor seleccionado
        $asesorNombre = '';
        if (!empty($tempData['asesor_comercial_id'])) {
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("SELECT nombre_completo FROM asesores_comerciales WHERE id = ?");
            $stmt->execute([$tempData['asesor_comercial_id']]);
            $asesor = $stmt->fetch(\PDO::FETCH_ASSOC);
            $asesorNombre = $asesor['nombre_completo'] ?? '';
        }
        
        // Determinar qué formulario mostrar según tipo de usuario, persona y ubicación
        $userType = $tempData['role'] ?? 'cliente';
        $personType = $tempData['person_type'] ?? 'natural';
        $ubicacion = $tempData['ubicacion'] ?? 'nacional';
        
        // Mapeo de formularios
        $formMap = [
            'cliente_natural' => [
                'template' => 'cliente_natural',
                'headerTitle' => 'DIRECCIONAMIENTO ESTRATEGICO',
                'headerSubtitle' => 'CREACION DE CLIENTES-PERSONA NATURAL',
                'fechaEmision' => '29/04/16',
                'fechaActualizacion' => '10/12/2025',
                'version' => '08',
                'codigo' => 'FGF-08'
            ],
            'cliente_juridica' => [
                'template' => 'cliente_juridica',
                'headerTitle' => 'FINANCIERO',
                'headerSubtitle' => 'FORMATO CREACIÓN DE CLIENTES-PERSONA JURÍDICA',
                'fechaEmision' => '29/04/16',
                'fechaActualizacion' => '10/12/2025',
                'version' => '08',
                'codigo' => 'FGF-16'
            ],
            'proveedor_natural' => [
                'template' => 'proveedor_natural',
                'headerTitle' => 'GESTION DE COMPRAS',
                'headerSubtitle' => 'CONOCIMIENTO DE PROVEEDOR NACIONAL PERSONA NATURAL',
                'fechaEmision' => '11/02/16',
                'fechaActualizacion' => '14/11/19',
                'version' => '3',
                'codigo' => 'FCO-05'
            ],
            'proveedor_juridica' => [
                'template' => 'proveedor_juridica',
                'headerTitle' => 'GESTION DE COMPRAS',
                'headerSubtitle' => 'CONOCIMIENTO DE PROVEEDORES NACIONAL PERSONA JURIDICA',
                'fechaEmision' => '09/02/16',
                'fechaActualizacion' => '14/11/19',
                'version' => '3',
                'codigo' => 'FCO-02'
            ],
            'proveedor_internacional' => [
                'template' => 'proveedor_internacional',
                'headerTitle' => 'GESTION DE COMPRAS',
                'headerSubtitle' => 'CONOCIMIENTO DE PROVEEDOR INTERNACIONAL',
                'fechaEmision' => '15/01/20',
                'fechaActualizacion' => '10/12/2025',
                'version' => '2',
                'codigo' => 'FCO-04'
            ],
            'declaracion_fondos_proveedores' => [
                'template' => 'declaracion_fondos_proveedores',
                'headerTitle' => 'GESTION DE COMPRAS',
                'headerSubtitle' => 'DECLARACIÓN ORIGEN DE FONDOS - PROVEEDORES',
                'fechaEmision' => '20/03/18',
                'fechaActualizacion' => '10/12/2025',
                'version' => '2',
                'codigo' => 'FCO-03'
            ],
            'declaracion_fondos_clientes' => [
                'template' => 'declaracion_fondos_clientes',
                'headerTitle' => 'DIRECCIONAMIENTO ESTRATEGICO',
                'headerSubtitle' => 'DECLARACIÓN ORIGEN DE FONDOS - CLIENTES',
                'fechaEmision' => '20/03/18',
                'fechaActualizacion' => '10/12/2025',
                'version' => '2',
                'codigo' => 'FGF-17'
            ]
        ];
        
        // Determinar formulario principal
        $formKey = $userType . '_' . $personType;
        
        // Si es proveedor internacional, usar ese formulario
        if ($userType === 'proveedor' && $ubicacion === 'internacional') {
            $formKey = 'proveedor_internacional';
        }
        
        $formConfig = $formMap[$formKey] ?? $formMap['cliente_natural'];

        $this->view('forms/pdf_style_form', [
            'csrf_token' => $this->generateCsrfToken(),
            'temp_data' => $tempData,
            'formType' => $formKey,
            'formTemplate' => $formConfig['template'],
            'headerTitle' => $formConfig['headerTitle'],
            'headerSubtitle' => $formConfig['headerSubtitle'],
            'fechaEmision' => $formConfig['fechaEmision'],
            'fechaActualizacion' => $formConfig['fechaActualizacion'],
            'version' => $formConfig['version'],
            'codigo' => $formConfig['codigo'],
            'asesores_grouped' => $asesoresGrouped,
            'asesor_nombre' => $asesorNombre
        ]);
    }

    /**
     * Guardar formulario (sin autenticación)
     * Usa datos de sesión temporal
     */
    public function storeDirect(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
            return;
        }

        // Verificar datos temporales
        if (!isset($_SESSION['temp_user_data'])) {
            $this->json(['error' => 'Sesión expirada. Por favor inicia de nuevo.'], 403);
            return;
        }

        $tempData = $_SESSION['temp_user_data'];

        $data = [
            'user_id' => $tempData['user_id'],
            'title' => 'Formulario SAGRILAFT - ' . $tempData['company_name'],
            'content' => $this->input('actividad_economica'),
            'company_name' => $this->input('razon_social'),
            'nit' => $this->input('nit'),
            'activity' => $this->input('actividad_economica'),
            'address' => $this->input('direccion'),
            'phone' => $this->input('telefono'),
            'status' => 'submitted',
            
            // Nuevos campos - Datos generales
            'celular' => $this->input('celular'),
            'ciudad' => $this->input('ciudad'),
            'barrio' => $this->input('barrio'),
            'pais' => $this->input('pais'),
            
            // Actividad económica
            'codigo_ciiu' => $this->input('codigo_ciiu'),
            
            // Información financiera
            'activos' => $this->input('activos') ? (float)$this->input('activos') : null,
            'pasivos' => $this->input('pasivos') ? (float)$this->input('pasivos') : null,
            'patrimonio' => $this->input('patrimonio') ? (float)$this->input('patrimonio') : null,
            'ingresos' => $this->input('ingresos') ? (float)$this->input('ingresos') : null,
            'gastos' => $this->input('gastos') ? (float)$this->input('gastos') : null,
            
            // Representante legal (solo para jurídicas)
            'representante_nombre' => $this->input('representante_nombre'),
            'representante_documento' => $this->input('representante_documento'),
            'representante_tipo_doc' => $this->input('representante_tipo_doc'),
            'representante_profesion' => $this->input('representante_profesion'),
            'representante_nacimiento' => $this->input('representante_nacimiento'),
            
            // Declaración origen de fondos
            'origen_fondos' => $this->input('origen_fondos'),
            'es_pep' => $this->input('es_pep'),
            'cargo_pep' => $this->input('cargo_pep'),
            'tiene_cuentas_exterior' => $this->input('tiene_cuentas_exterior'),
            'pais_cuentas_exterior' => $this->input('pais_cuentas_exterior'),
            
            // Autorizaciones (por defecto sí)
            'autoriza_centrales_riesgo' => 'si'
        ];

        try {
            // Crear formulario
            $formId = $this->formModel->create($data);

            // Procesar archivos PDF adjuntos
            $uploadedFiles = [];
            
            // Nuevo formato: document_0, document_1, etc.
            $fileCount = (int)($this->input('file_count') ?? 0);
            if ($fileCount > 0) {
                $tempFiles = [];
                for ($i = 0; $i < $fileCount; $i++) {
                    $key = "document_{$i}";
                    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                        $tempFiles['name'][] = $_FILES[$key]['name'];
                        $tempFiles['type'][] = $_FILES[$key]['type'];
                        $tempFiles['tmp_name'][] = $_FILES[$key]['tmp_name'];
                        $tempFiles['error'][] = $_FILES[$key]['error'];
                        $tempFiles['size'][] = $_FILES[$key]['size'];
                    }
                }
                if (!empty($tempFiles['name'])) {
                    $_FILES['documents'] = $tempFiles;
                    $uploadedFiles = $this->handleFileUploads($formId);
                }
            }
            // Formato antiguo: documents[]
            elseif (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
                $uploadedFiles = $this->handleFileUploads($formId);
            }

            $this->logger->info('Formulario creado (sin auth)', [
                'form_id' => $formId,
                'user_id' => $tempData['user_id'],
                'attachments_count' => count($uploadedFiles)
            ]);

            // Enviar notificación por email
            $this->sendFormNotification($formId, $data, $uploadedFiles);

            // Limpiar datos temporales
            unset($_SESSION['temp_user_data']);

            $this->json([
                'success' => true,
                'message' => 'Formulario enviado correctamente',
                'form_id' => $formId
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Error al crear formulario (sin auth)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->json(['error' => 'Error al guardar el formulario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generar PDF preview sin guardar en BD
     * Solo para el botón "Descargar PDF" — no guarda nada
     */
    public function pdfPreview(): void
    {
        error_reporting(0);
        ini_set('display_errors', '0');
        while (ob_get_level()) ob_end_clean();

        if (!$this->validateCsrf()) {
            http_response_code(403);
            echo 'Token CSRF inválido';
            return;
        }

        $tempData = $_SESSION['temp_user_data'] ?? [];
        $formType = $this->input('form_type') ?? ($tempData['role'] ?? 'cliente');

        // Construir array de datos del formulario desde POST (sin guardar)
        $formData = $_POST;
        $formData['form_type'] = $formType;
        $formData['person_type'] = $tempData['person_type'] ?? 'natural';
        $formData['company_name'] = $this->input('nombre_cliente') ?: $this->input('razon_social') ?: ($tempData['company_name'] ?? '');
        $formData['nit'] = $this->input('cc') ?: $this->input('nit') ?: ($tempData['document_number'] ?? '');

        // Procesar accionistas si existen
        $accionistas = $this->processAccionistas();
        if ($accionistas) {
            $decoded = json_decode($accionistas, true);
            if (is_array($decoded)) {
                $formData['accionista_nombre']        = array_column($decoded, 'nombre');
                $formData['accionista_documento']     = array_column($decoded, 'documento');
                $formData['accionista_participacion'] = array_column($decoded, 'participacion');
                $formData['accionista_nacionalidad']  = array_column($decoded, 'nacionalidad');
            }
        }

        try {
            $filler = new \App\Services\FormPdfFiller();
            $pdfContent = $filler->generate($formData, $tempData);

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="preview.pdf"');
            header('Content-Length: ' . strlen($pdfContent));
            echo $pdfContent;
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Error al generar PDF: ' . $e->getMessage();
        }
    }

    /**
     * Guardar formulario PDF (desde formulario estilo PDF)
     */
    public function storePdf(): void
    {
        // Capturar TODOS los errores y convertirlos a JSON
        try {
            // Asegurar que siempre se devuelva JSON
            header('Content-Type: application/json');
            
            // Limpiar cualquier output buffer previo
            if (ob_get_level()) {
                ob_clean();
            }
            
            if (!$this->validateCsrf()) {
                echo json_encode(['error' => 'Token CSRF inválido']);
                exit;
            }

            // Verificar datos temporales
            if (!isset($_SESSION['temp_user_data'])) {
                echo json_encode(['error' => 'Sesión expirada. Por favor inicia de nuevo.']);
                exit;
            }

            $tempData = $_SESSION['temp_user_data'];
            
            // VALIDAR CAMPOS REQUERIDOS
            $formType = $tempData['role'] ?? $tempData['user_type'] ?? 'cliente';
            $isDeclaracion = in_array($this->input('form_type'), [
                'declaracion_cliente', 'declaracion_proveedor',
                'declaracion_fondos_clientes', 'declaracion_fondos_proveedores'
            ]);

            $missingFields = [];

            if (!$isDeclaracion) {
                // Ciudad
                if (empty($this->input('ciudad'))) {
                    $missingFields[] = 'Ciudad';
                }
                // Teléfono: puede venir como telefono, telefono_fijo o celular
                $telVal = $this->input('telefono') ?: $this->input('telefono_fijo') ?: $this->input('celular');
                if (empty($telVal)) {
                    $missingFields[] = 'Teléfono';
                }
                // Email
                if (empty($this->input('email'))) {
                    $missingFields[] = 'Email';
                }
                // Dirección: puede venir como 'direccion'
                if (empty($this->input('direccion'))) {
                    $missingFields[] = 'Dirección';
                }
            }

            // Nombre: puede venir como nombre_cliente, razon_social, nombre_proveedor, empresa
            $companyName = $this->input('nombre_cliente')
                ?: $this->input('razon_social')
                ?: $this->input('nombre_proveedor')
                ?: $this->input('empresa');
            if (empty($companyName)) {
                $missingFields[] = 'Nombre del cliente / Razón social';
            }

            // Documento: puede venir como cc, nit, numero_documento, numero_registro, nit_empresa
            $nit = $this->input('cc')
                ?: $this->input('nit')
                ?: $this->input('numero_documento')
                ?: $this->input('numero_registro')
                ?: $this->input('nit_empresa');
            if (empty($nit)) {
                $missingFields[] = 'NIT o Documento de identidad';
            }

            if (!empty($missingFields)) {
                echo json_encode([
                    'error' => 'Por favor completa los siguientes campos requeridos: ' . implode(', ', $missingFields)
                ]);
                exit;
            }

            // Recopilar todos los datos del formulario
            $data = [
            'user_id' => $tempData['user_id'],
            'title' => 'Formulario SAGRILAFT - ' . $companyName,
            'content' => $this->input('actividad_economica') ?: $this->input('objeto_social') ?: $this->input('codigo_ciiu') ?: 'N/A',
            'company_name' => $companyName,
            'nit' => $nit,
            'address' => $this->input('direccion'),
            'phone' => $this->input('telefono') ?: $this->input('telefono_fijo') ?: $this->input('celular'),
            'status' => 'submitted',
            'form_type'   => (function() use ($tempData) {
                if (($tempData['ubicacion'] ?? '') === 'internacional') return 'proveedor_internacional';
                $u = $tempData['user_type'] ?? $tempData['role'] ?? 'cliente';
                $p = $tempData['person_type'] ?? 'natural';
                return "{$u}_{$p}";
            })(),
            'person_type' => $tempData['person_type'] ?? 'natural',            
            // Asesor comercial (desde sesión temporal o formulario)
            'asesor_comercial_id' => $this->input('asesor_comercial_id') 
                ? (int)$this->input('asesor_comercial_id') 
                : (isset($tempData['asesor_comercial_id']) ? (int)$tempData['asesor_comercial_id'] : null),
            
            // Capturar TODOS los campos del formulario dinámicamente
            'vinculacion' => $this->input('vinculacion'),
            'fecha_vinculacion' => $this->input('fecha_vinculacion'),
            'actualizacion' => $this->input('actualizacion'),
            'nombre_establecimiento' => $this->input('nombre_establecimiento'),
            'ciudad' => $this->input('ciudad'),
            'barrio' => $this->input('barrio'),
            'localidad' => $this->input('localidad'),
            'celular' => $this->input('celular'),
            'telefono_fijo' => $this->input('telefono_fijo') ?: $this->input('telefono'),
            'telefono' => $this->input('telefono') ?: $this->input('telefono_fijo'),
            'email' => $this->input('email'),
            'lista_precios' => $this->input('lista_precios'),
            'codigo_vendedor' => $this->input('codigo_vendedor'),
            'codigo_ciiu' => $this->input('codigo_ciiu'),
            'tipo_pago' => $this->input('tipo_pago'),
            'contado' => $this->input('contado'),
            'credito' => $this->input('credito'),
            'mixto' => $this->input('mixto'),
            'fecha_nacimiento' => $this->input('fecha_nacimiento'),
            
            // Información financiera
            'activos' => $this->input('activos') ? (float)$this->input('activos') : null,
            'pasivos' => $this->input('pasivos') ? (float)$this->input('pasivos') : null,
            'patrimonio' => $this->input('patrimonio') ? (float)$this->input('patrimonio') : null,
            'ingresos' => $this->input('ingresos') ? (float)$this->input('ingresos') : null,
            'gastos' => $this->input('gastos') ? (float)$this->input('gastos') : null,
            'otros_ingresos' => $this->input('otros_ingresos') ? (float)$this->input('otros_ingresos') : null,
            'detalle_otros_ingresos' => $this->input('detalle_otros_ingresos'),
            
            // Representante legal
            'representante_nombre' => $this->input('representante_nombre'),
            'representante_documento' => $this->input('representante_documento'),
            'representante_tipo_doc' => $this->input('representante_tipo_doc')
                ?: ($this->input('representante_tipo_doc_cc') ? 'CC' : '')
                ?: ($this->input('representante_tipo_doc_ce') ? 'CE' : ''),
            'representante_profesion' => $this->input('representante_profesion'),
            'representante_nacimiento' => $this->input('representante_nacimiento'),
            'representante_telefono' => $this->input('representante_telefono'),
            'representante_email' => $this->input('representante_email'),
            'representante_cargo' => $this->input('representante_cargo'),
            'representante_residencia' => $this->input('representante_residencia'),
            'representante_lugar_nacimiento' => $this->input('representante_lugar_nacimiento'),
            
            // Datos tributarios
            'tipo_contribuyente' => $this->input('tipo_contribuyente'),
            'regimen_tributario' => $this->input('regimen_tributario'),
            'responsable_iva' => $this->input('responsable_iva'),
            
            // Autorizaciones
            'autoriza_centrales_riesgo' => $this->input('autoriza_centrales_riesgo', 'si'),
            'autoriza_centrales' => $this->input('autoriza_centrales', 'si'),
            
            // Consultas
            'consulta_ofac' => $this->input('consulta_ofac'),
            'consulta_listas_nacionales' => $this->input('consulta_listas_nacionales'),
            'consulta_onu' => $this->input('consulta_onu'),
            
            // Otros campos
            'objeto_social' => $this->input('objeto_social'),
            'nombre_comercial' => $this->input('nombre_comercial'),
            'pais' => $this->input('pais'),
            'departamento' => $this->input('departamento'),
            'pagina_web' => $this->input('pagina_web'),
            'fecha_constitucion' => $this->input('fecha_constitucion'),
            'numero_empleados' => $this->input('numero_empleados') ? (int)$this->input('numero_empleados') : null,
            'productos_servicios' => $this->input('productos_servicios'),
            'tiempo_mercado' => $this->input('tiempo_mercado') ? (int)$this->input('tiempo_mercado') : null,
            'forma_pago' => $this->input('forma_pago'),
            'certificaciones' => $this->input('certificaciones'),
            'rut' => $this->input('rut'),
            'nombre_oficial' => $this->input('nombre_oficial'),
            'mes_nacimiento' => $this->input('mes_nacimiento'),
            'dia_nacimiento' => $this->input('dia_nacimiento'),
            
            // Información bancaria
            'banco' => $this->input('banco'),
            'tipo_cuenta' => $this->input('tipo_cuenta'),
            'numero_cuenta' => $this->input('numero_cuenta'),
            
            // PEP
            'es_pep' => $this->input('es_pep'),
            'cargo_pep' => $this->input('cargo_pep'),
            'tiene_cuentas_exterior' => $this->input('tiene_cuentas_exterior'),
            'pais_cuentas_exterior' => $this->input('pais_cuentas_exterior'),
            
            // Firmas y aprobaciones
            'recibe' => $this->input('recibe'),
            'firma_oficial' => $this->input('firma_oficial'),
            'director_cartera' => $this->input('director_cartera'),
            'gerencia_comercial' => $this->input('gerencia_comercial'),
            'director_compras' => $this->input('director_compras'),
            'oficial_cumplimiento' => $this->input('oficial_cumplimiento'),
            
            // Observaciones
            'observaciones' => $this->input('observaciones'),
            
            // Vendedor
            'nombre_vendedor' => $this->input('nombre_vendedor'),
            'clase_cliente' => $this->input('clase_cliente'),
            'firma_vendedor' => $this->input('firma_vendedor'),
            'descripcion_firma' => $this->input('descripcion_firma'),
            
            // Accionistas (JSON)
            'accionistas' => $this->processAccionistas(),
            
            // Referencias comerciales (JSON)
            'referencias_comerciales' => $this->processReferencias(),

            // Empresa / proveedor
            'dv'                       => $this->input('dv'),
            'tipo_compania'            => $this->input('tipo_compania'),
            'certificacion'            => $this->input('certificacion'),
            'numero_registro'          => $this->input('numero_registro'),
            'sitio_web'                => $this->input('sitio_web'),

            // Representante (campos nuevos)
            'representante_nacionalidad' => $this->input('representante_nacionalidad'),

            // Importacion internacional
            'incoterm'                 => $this->input('incoterm'),
            'forma_pago_internacional' => $this->input('forma_pago_internacional'),
            'tiempo_entrega'           => $this->input('tiempo_entrega'),
            'puerto_origen'            => $this->input('puerto_origen'),
            'agente_aduanal'           => $this->input('agente_aduanal'),
            'certificado_origen'       => $this->input('certificado_origen'),
            'consulta_interpol'        => $this->input('consulta_interpol'),

            // Firmas internas
            'preparo'                  => $this->input('preparo'),
            'reviso'                   => $this->input('reviso'),

            // Campos de declaración de fondos (cuando se envía como paso 1 o formulario único)
            'nombre_declarante'        => $this->input('nombre_declarante'),
            'tipo_documento'           => $this->input('tipo_documento'),
            'numero_documento'         => $this->input('numero_documento'),
            'calidad'                  => $this->input('calidad'),
            'empresa'                  => $this->input('empresa'),
            'nit_empresa'              => $this->input('nit_empresa'),
            'origen_recursos'          => $this->input('origen_recursos'),
            'periodo_pep'              => $this->input('periodo_pep'),
            'familiar_pep'             => $this->input('familiar_pep'),
            'familiar_pep_detalle'     => $this->input('familiar_pep_detalle'),
            'vinculo_pep'              => $this->input('vinculo_pep'),
            'vinculo_pep_detalle'      => $this->input('vinculo_pep_detalle'),
            'ingresos_mensuales'       => $this->input('ingresos_mensuales') ? (float)$this->input('ingresos_mensuales') : null,
            'egresos_mensuales'        => $this->input('egresos_mensuales') ? (float)$this->input('egresos_mensuales') : null,
            'total_activos'            => $this->input('total_activos') ? (float)$this->input('total_activos') : null,
            'total_pasivos'            => $this->input('total_pasivos') ? (float)$this->input('total_pasivos') : null,
            'patrimonio_neto'          => $this->input('patrimonio_neto') ? (float)$this->input('patrimonio_neto') : null,
            'opera_moneda_extranjera'  => $this->input('opera_moneda_extranjera'),
            'paises_operacion'         => $this->input('paises_operacion'),
            'cuentas_exterior'         => $this->input('cuentas_exterior'),
            'cuentas_exterior_detalle' => $this->input('cuentas_exterior_detalle'),
            'verificado_por'           => $this->input('verificado_por'),
            'fecha_verificacion'       => $this->input('fecha_verificacion'),
            'fecha_declaracion'        => $this->input('fecha_declaracion'),
            'ciudad_declaracion'       => $this->input('ciudad_declaracion'),
            'nombre_firma_final'       => $this->input('nombre_firma_final'),
            'documento_firma'          => $this->input('documento_firma'),
        ];

            // Crear formulario
            $formId = $this->formModel->create($data);

            // Procesar archivos PDF adjuntos si existen
            $uploadedFiles = [];
            $fileCount = (int)($this->input('file_count') ?? 0);
            if ($fileCount > 0) {
                $tempFiles = [];
                for ($i = 0; $i < $fileCount; $i++) {
                    $key = "document_{$i}";
                    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                        $tempFiles['name'][] = $_FILES[$key]['name'];
                        $tempFiles['type'][] = $_FILES[$key]['type'];
                        $tempFiles['tmp_name'][] = $_FILES[$key]['tmp_name'];
                        $tempFiles['error'][] = $_FILES[$key]['error'];
                        $tempFiles['size'][] = $_FILES[$key]['size'];
                    }
                }
                if (!empty($tempFiles['name'])) {
                    $_FILES['documents'] = $tempFiles;
                    $uploadedFiles = $this->handleFileUploads($formId);
                }
            }
            
            // Generar PDF del formulario con el formato oficial
            try {
                $this->generateAndSaveFormPdf($formId, $data, $tempData);
            } catch (\Exception $e) {
                $this->logger->error('Error al generar PDF del formulario', [
                    'form_id' => $formId,
                    'error' => $e->getMessage()
                ]);
            }

            $this->logger->info('Formulario PDF creado', [
                'form_id' => $formId,
                'user_id' => $tempData['user_id'],
                'attachments_count' => count($uploadedFiles)
            ]);

            // Enviar notificación por email
            $this->sendFormNotification($formId, $data, $uploadedFiles);

            // Determinar si necesita llenar declaración de fondos
            $userType = $tempData['role'] ?? $tempData['user_type'] ?? 'cliente';
            $needsDeclaracion = in_array($userType, ['cliente', 'proveedor']);
            
            if ($needsDeclaracion) {
                // Guardar que necesita llenar declaración
                $_SESSION['pending_declaracion'] = [
                    'form_id' => $formId,
                    'user_type' => $userType,
                    'user_data' => $tempData
                ];
                
                // NO limpiar datos temporales aún
                // Enviar respuesta JSON
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Formulario principal enviado. Ahora completa la Declaración de Origen de Fondos.',
                    'form_id' => $formId,
                    'needs_declaracion' => true,
                    'redirect_url' => '/form/declaracion'
                ]);
                exit;
            } else {
                // Limpiar datos temporales
                unset($_SESSION['temp_user_data']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Formulario enviado correctamente',
                    'form_id' => $formId
                ]);
                exit;
            }

        } catch (\Exception $e) {
            $this->logger->error('Error al crear formulario PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Asegurar que siempre se devuelva JSON incluso en errores
            if (ob_get_level()) {
                ob_clean();
            }
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Error al guardar el formulario: ' . $e->getMessage()
            ]);
            exit;
        } catch (\Throwable $e) {
            // Capturar errores fatales también
            if (ob_get_level()) {
                ob_clean();
            }
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Error crítico: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Procesar accionistas del formulario
     * Soporta accionista_documento[], accionista_cc[], accionista_ce[]
     */
    private function processAccionistas(): ?string
    {
        $nombres = $_POST['accionista_nombre'] ?? [];
        
        if (empty($nombres) || !is_array($nombres)) {
            return null;
        }

        // Proveedor jurídica usa cc/ce en lugar de documento
        $documentos    = $_POST['accionista_documento'] ?? [];
        $ccs           = $_POST['accionista_cc'] ?? [];
        $ces           = $_POST['accionista_ce'] ?? [];
        $participaciones = $_POST['accionista_participacion'] ?? [];
        $nacionalidades  = $_POST['accionista_nacionalidad'] ?? [];

        $accionistas = [];
        foreach ($nombres as $i => $nombre) {
            if (!empty($nombre)) {
                // Unificar: si hay cc/ce usar esos, si no usar documento
                $doc = $documentos[$i] ?? ($ccs[$i] ?? '');
                $accionistas[] = [
                    'nombre'        => $nombre,
                    'documento'     => $doc,
                    'cc'            => $ccs[$i] ?? $doc,
                    'ce'            => $ces[$i] ?? '',
                    'participacion' => $participaciones[$i] ?? 0,
                    'nacionalidad'  => $nacionalidades[$i] ?? '',
                ];
            }
        }

        return !empty($accionistas) ? json_encode($accionistas) : null;
    }

    /**
     * Procesar referencias comerciales del formulario
     */
    private function processReferencias(): ?string
    {
        $referencias = [];
        for ($i = 1; $i <= 3; $i++) {
            $empresa = $this->input("ref{$i}_empresa");
            if (!empty($empresa)) {
                $referencias[] = [
                    'empresa' => $empresa,
                    'contacto' => $this->input("ref{$i}_contacto"),
                    'telefono' => $this->input("ref{$i}_telefono"),
                    'tiempo' => $this->input("ref{$i}_tiempo")
                ];
            }
        }
        
        return !empty($referencias) ? json_encode($referencias) : null;
    }
    
    /**
     * Generar y guardar PDF del formulario con formato oficial
     * 
     * @param int $formId ID del formulario
     * @param array $formData Datos del formulario
     * @param array $tempData Datos temporales del usuario
     * @return void
     */
    private function generateAndSaveFormPdf(int $formId, array $formData, array $tempData): void
    {
        try {
            // Deserializar accionistas de JSON a arrays para FormPdfFiller
            if (!empty($formData['accionistas']) && is_string($formData['accionistas'])) {
                $accionistas = json_decode($formData['accionistas'], true);
                if (is_array($accionistas)) {
                    $formData['accionista_nombre']        = array_column($accionistas, 'nombre');
                    $formData['accionista_documento']     = array_column($accionistas, 'documento');
                    $formData['accionista_participacion'] = array_column($accionistas, 'participacion');
                    $formData['accionista_nacionalidad']  = array_column($accionistas, 'nacionalidad');
                    $formData['accionista_cc']            = array_column($accionistas, 'cc');
                    $formData['accionista_ce']            = array_column($accionistas, 'ce');
                }
            }

            $filler = new \App\Services\FormPdfFiller();
            $pdfContent = $filler->generate($formData, $tempData);
            $pdfFilename = "form_{$formId}_" . date('Ymd_His') . ".pdf";
            $this->savePdfToDatabase($formId, $pdfFilename, $pdfContent);
            $this->logger->info('PDF generado con plantilla oficial', [
                'form_id' => $formId,
                'size'    => strlen($pdfContent),
            ]);
            return;
        } catch (\Exception $e) {
            $this->logger->error('Error al generar PDF con plantilla', [
                'form_id' => $formId,
                'error'   => $e->getMessage(),
            ]);
            // Continúa con el fallback original
        }

        // --- FALLBACK ORIGINAL ---
        try {
            // Determinar el tipo de formulario
            $userType = $tempData['role'] ?? $tempData['user_type'] ?? 'cliente';
            $personType = $tempData['person_type'] ?? 'natural';
            $isDeclaracion = isset($formData['nombre_declarante']);
            
            // Generar PDF usando FPDF
            require_once __DIR__ . '/../Libraries/fpdf.php';
            
            $pdf = new \FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            
            // Título del formulario
            if ($isDeclaracion) {
                $pdf->Cell(0, 10, utf8_decode('DECLARACIÓN DE ORIGEN DE FONDOS'), 0, 1, 'C');
            } else {
                $title = 'FORMULARIO SAGRILAFT - ';
                $title .= strtoupper($userType) . ' ';
                $title .= strtoupper($personType);
                $pdf->Cell(0, 10, utf8_decode($title), 0, 1, 'C');
            }
                
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 10);
            
            // Información del formulario
            $pdf->Cell(0, 6, utf8_decode('ID Formulario: ' . $formId), 0, 1);
            $pdf->Cell(0, 6, utf8_decode('Fecha: ' . date('d/m/Y H:i')), 0, 1);
            $pdf->Ln(5);
            
            // Datos principales
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, utf8_decode('DATOS GENERALES'), 0, 1);
            $pdf->SetFont('Arial', '', 10);
            
            // Función helper para agregar campos
            $addField = function($label, $value) use ($pdf) {
                if (!empty($value)) {
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell(50, 6, utf8_decode($label . ':'), 0, 0);
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->MultiCell(0, 6, utf8_decode($value), 0);
                }
            };
            
            // Agregar todos los campos disponibles
            $addField('Empresa/Persona', $formData['company_name'] ?? '');
            $addField('NIT/Documento', $formData['nit'] ?? '');
            $addField('Dirección', $formData['address'] ?? $formData['direccion'] ?? '');
            $addField('Ciudad', $formData['ciudad'] ?? '');
            $addField('Teléfono', $formData['phone'] ?? $formData['telefono'] ?? $formData['celular'] ?? '');
            $addField('Email', $formData['email'] ?? '');
            $addField('Actividad Económica', $formData['activity'] ?? $formData['codigo_ciiu'] ?? '');
            
            if (!empty($formData['activos'])) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 8, utf8_decode('INFORMACIÓN FINANCIERA'), 0, 1);
                $pdf->SetFont('Arial', '', 10);
                
                $addField('Activos', '$' . number_format($formData['activos'], 2));
                $addField('Pasivos', '$' . number_format($formData['pasivos'] ?? 0, 2));
                $addField('Patrimonio', '$' . number_format($formData['patrimonio'] ?? 0, 2));
                $addField('Ingresos', '$' . number_format($formData['ingresos'] ?? 0, 2));
                $addField('Gastos', '$' . number_format($formData['gastos'] ?? 0, 2));
            }
            
            if (!empty($formData['representante_nombre'])) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 8, utf8_decode('REPRESENTANTE LEGAL'), 0, 1);
                $pdf->SetFont('Arial', '', 10);
                
                $addField('Nombre', $formData['representante_nombre']);
                $addField('Documento', $formData['representante_documento'] ?? '');
                $addField('Profesión', $formData['representante_profesion'] ?? '');
            }
            
            if ($isDeclaracion) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 8, utf8_decode('DECLARACIÓN'), 0, 1);
                $pdf->SetFont('Arial', '', 10);
                
                $addField('Declarante', $formData['nombre_declarante'] ?? '');
                $addField('Documento', $formData['numero_documento'] ?? '');
                $addField('Origen de Recursos', $formData['origen_recursos'] ?? '');
                $addField('Es PEP', $formData['es_pep'] ?? 'No');
                if (!empty($formData['cargo_pep'])) {
                    $addField('Cargo PEP', $formData['cargo_pep']);
                }
            }
            
            // Generar el PDF como string
            $pdfContent = $pdf->Output('S');
            $pdfFilename = "form_{$formId}_" . date('Ymd_His') . ".pdf";
            
            // Guardar en la base de datos
            $this->savePdfToDatabase($formId, $pdfFilename, $pdfContent);
            
            $this->logger->info('PDF generado y guardado en BD', [
                'form_id' => $formId,
                'size' => strlen($pdfContent)
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Error al generar PDF', [
                'form_id' => $formId,
                'error' => $e->getMessage()
            ]);
        }
    }
    

    
    /**
     * Guardar PDF en la base de datos como BLOB
     * 
     * @param int $formId ID del formulario
     * @param string $filename Nombre del archivo
     * @param string $pdfContent Contenido del PDF
     * @return void
     */
    private function savePdfToDatabase(int $formId, string $filename, string $pdfContent): void
    {
        $db = \App\Core\Database::getConnection();
        
        $pdfSize = strlen($pdfContent);
        
        $stmt = $db->prepare("
            UPDATE forms 
            SET generated_pdf_content = ?, 
                generated_pdf_filename = ?,
                generated_pdf_size = ?,
                pdf_mime_type = 'application/pdf',
                pdf_generated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([$pdfContent, $filename, $pdfSize, $formId]);
    }
    /**
     * Guardar campos internos (Espacio Pollo Fiesta) y regenerar PDF
     */
    public function savePolloFiesta(string $id): void
    {
        header('Content-Type: application/json');

        // Solo revisores/admin
        if (!isset($_SESSION['reviewer_id']) && !isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'No autorizado']); exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            echo json_encode(['error' => 'Datos inválidos']); exit;
        }

        // Validar CSRF
        if (empty($input['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $input['csrf_token'])) {
            echo json_encode(['error' => 'Token CSRF inválido']); exit;
        }

        $form = $this->formModel->findById((int)$id);
        if (!$form) {
            echo json_encode(['error' => 'Formulario no encontrado']); exit;
        }

        try {
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("
                UPDATE forms SET
                    consulta_ofac               = ?,
                    consulta_listas_nacionales  = ?,
                    consulta_onu                = ?,
                    consulta_interpol           = ?,
                    recibe                      = ?,
                    director_cartera            = ?,
                    gerencia_comercial          = ?,
                    verificado_por              = ?,
                    preparo                     = ?,
                    reviso                      = ?,
                    observaciones               = ?,
                    updated_at                  = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $input['consulta_ofac']              ?? null,
                $input['consulta_listas_nacionales'] ?? null,
                $input['consulta_onu']               ?? null,
                $input['consulta_interpol']          ?? null,
                $input['recibe']                     ?? null,
                $input['director_cartera']           ?? null,
                $input['gerencia_comercial']         ?? null,
                $input['verificado_por']             ?? null,
                $input['preparo']                    ?? null,
                $input['reviso']                     ?? null,
                $input['observaciones']              ?? null,
                (int)$id,
            ]);

            // Obtener firma del revisor
            $userId = $_SESSION['reviewer_id'] ?? $_SESSION['user_id'] ?? 0;
            $stmtF = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
            $stmtF->execute([$userId]);
            $firma = $stmtF->fetch();

            // Recargar form con datos actualizados
            $formData = $this->formModel->findById((int)$id);

            // Deserializar accionistas
            if (!empty($formData['accionistas']) && is_string($formData['accionistas'])) {
                $acs = json_decode($formData['accionistas'], true);
                if (is_array($acs)) {
                    $formData['accionista_nombre']        = array_column($acs, 'nombre');
                    $formData['accionista_documento']     = array_column($acs, 'documento');
                    $formData['accionista_participacion'] = array_column($acs, 'participacion');
                    $formData['accionista_nacionalidad']  = array_column($acs, 'nacionalidad');
                    $formData['accionista_cc']            = array_column($acs, 'cc');
                    $formData['accionista_ce']            = array_column($acs, 'ce');
                }
            }

            // Inyectar firma como data URI para el PDF
            if ($firma && !empty($firma['firma_data'])) {
                $ext = str_contains($firma['mime_type'], 'jpeg') ? 'jpeg' : 'png';
                $formData['firma_oficial_data'] = 'data:' . $firma['mime_type'] . ';base64,' . base64_encode($firma['firma_data']);
                $formData['firma_oficial_cumplimiento_data'] = $formData['firma_oficial_data'];
            }

            $tempData = [
                'role'        => $formData['form_type'] ?? 'cliente',
                'user_type'   => $formData['form_type'] ?? 'cliente',
                'person_type' => $formData['person_type'] ?? 'natural',
            ];

            $filler = new \App\Services\FormPdfFiller();
            $pdfContent = $filler->generate($formData, $tempData);
            $filename = "form_{$id}_" . date('Ymd_His') . ".pdf";
            $this->savePdfToDatabase((int)$id, $filename, $pdfContent);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $this->logger->error('savePolloFiesta failed', ['error' => $e->getMessage()]);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Mostrar página de éxito
     */
    public function success(): void
    {
        require __DIR__ . '/../Views/forms/success.php';
    }

    public function showDeclaracion(): void
    {
        // Verificar que haya una declaración pendiente
        if (!isset($_SESSION['pending_declaracion'])) {
            header('Location: ' . $_ENV['APP_URL']);
            exit;
        }

        $pendingData = $_SESSION['pending_declaracion'];
        $userType = $pendingData['user_type'];
        $tempData = $pendingData['user_data'];

        // Determinar qué formulario de declaración mostrar
        $formMap = [
            'cliente' => [
                'template' => 'declaracion_fondos_clientes',
                'headerTitle' => 'DIRECCIONAMIENTO ESTRATEGICO',
                'headerSubtitle' => 'DECLARACIÓN ORIGEN DE FONDOS - CLIENTES',
                'fechaEmision' => '20/03/18',
                'fechaActualizacion' => '10/12/2025',
                'version' => '2',
                'codigo' => 'FGF-17'
            ],
            'proveedor' => [
                'template' => 'declaracion_fondos_proveedores',
                'headerTitle' => 'GESTION DE COMPRAS',
                'headerSubtitle' => 'DECLARACIÓN ORIGEN DE FONDOS - PROVEEDORES',
                'fechaEmision' => '20/03/18',
                'fechaActualizacion' => '10/12/2025',
                'version' => '2',
                'codigo' => 'FCO-03'
            ]
        ];

        $formConfig = $formMap[$userType] ?? $formMap['cliente'];

        // Recuperar datos del formulario principal para pre-llenar la declaración
        $formData = [];
        if (!empty($pendingData['form_id'])) {
            $savedForm = $this->formModel->findById((int)$pendingData['form_id']);
            if ($savedForm) {
                $formData = $savedForm;
            }
        }

        $this->view('forms/pdf_style_form', [
            'csrf_token' => $this->generateCsrfToken(),
            'temp_data' => $tempData,
            'form_data' => $formData,
            'formType' => 'declaracion_' . $userType,
            'formTemplate' => $formConfig['template'],
            'headerTitle' => $formConfig['headerTitle'],
            'headerSubtitle' => $formConfig['headerSubtitle'],
            'fechaEmision' => $formConfig['fechaEmision'],
            'fechaActualizacion' => $formConfig['fechaActualizacion'],
            'version' => $formConfig['version'],
            'codigo' => $formConfig['codigo'],
            'is_step_2' => true
        ]);
    }

    /**
     * Guardar declaración de origen de fondos (Paso 2)
     */
    public function storeDeclaracion(): void
    {
        // Capturar TODOS los errores y convertirlos a JSON
        try {
            // Asegurar que siempre se devuelva JSON
            header('Content-Type: application/json');
            
            // Limpiar cualquier output buffer previo
            if (ob_get_level()) {
                ob_clean();
            }
            
            if (!$this->validateCsrf()) {
                echo json_encode(['error' => 'Token CSRF inválido']);
                exit;
            }

            // Verificar que haya una declaración pendiente
            if (!isset($_SESSION['pending_declaracion'])) {
                echo json_encode(['error' => 'No hay declaración pendiente']);
                exit;
            }

            $pendingData = $_SESSION['pending_declaracion'];
            $originalFormId = $pendingData['form_id'];
            $tempData = $pendingData['user_data'];

            // Recopilar datos de la declaración
            $data = [
            'user_id' => $tempData['user_id'],
            'title' => 'Declaración Origen de Fondos - ' . $tempData['company_name'],
            'content' => $this->input('origen_recursos') ?: 'Declaración de origen de fondos',
            'company_name' => $this->input('empresa') ?: $tempData['company_name'],
            'nit' => $this->input('nit_empresa') ?: $tempData['document_number'],
            'status' => 'submitted',
            'related_form_id' => $originalFormId, // Vincular con el formulario principal
            'form_type' => ($pendingData['user_type'] === 'proveedor') ? 'declaracion_fondos_proveedores' : 'declaracion_fondos_clientes',
            'person_type' => 'declaracion',
            
            // Datos de la declaración
            'nombre_declarante' => $this->input('nombre_declarante'),
            'tipo_documento' => $this->input('tipo_documento'),
            'numero_documento' => $this->input('numero_documento'),
            'calidad' => $this->input('calidad'),
            'origen_recursos' => $this->input('origen_recursos'),
            'es_pep' => $this->input('es_pep'),
            'cargo_pep' => $this->input('cargo_pep'),
            'familiar_pep' => $this->input('familiar_pep'),
            'familiar_pep_detalle' => $this->input('familiar_pep_detalle'),
            'vinculo_pep' => $this->input('vinculo_pep'),
            'vinculo_pep_detalle' => $this->input('vinculo_pep_detalle'),
            'periodo_pep' => $this->input('periodo_pep'),
            'ingresos_mensuales' => $this->input('ingresos_mensuales') ? (float)$this->input('ingresos_mensuales') : null,
            'egresos_mensuales' => $this->input('egresos_mensuales') ? (float)$this->input('egresos_mensuales') : null,
            'total_activos' => $this->input('total_activos') ? (float)$this->input('total_activos') : null,
            'total_pasivos' => $this->input('total_pasivos') ? (float)$this->input('total_pasivos') : null,
            'patrimonio_neto' => $this->input('patrimonio_neto') ? (float)$this->input('patrimonio_neto') : null,
            'opera_moneda_extranjera' => $this->input('opera_moneda_extranjera'),
            'paises_operacion' => $this->input('paises_operacion'),
            'cuentas_exterior' => $this->input('cuentas_exterior'),
            'cuentas_exterior_detalle' => $this->input('cuentas_exterior_detalle'),
            'nombre_firma_final' => $this->input('nombre_firma_final'),
            'firma_declarante' => $this->input('firma_declarante'),
            'documento_firma' => $this->input('documento_firma'),
            'fecha_declaracion' => $this->input('fecha_declaracion'),
            'ciudad_declaracion' => $this->input('ciudad_declaracion'),
            'observaciones' => $this->input('observaciones')
        ];

            // Crear formulario de declaración
            $formId = $this->formModel->create($data);
            
            // Generar PDF de la declaración â€” forzar person_type = 'declaracion'
            try {
                $declaracionTempData = array_merge($tempData, ['person_type' => 'declaracion']);
                $this->generateAndSaveFormPdf($formId, $data, $declaracionTempData);
            } catch (\Exception $e) {
                $this->logger->error('Error al generar PDF de declaración', [
                    'form_id' => $formId,
                    'error' => $e->getMessage()
                ]);
            }

            $this->logger->info('Declaración de fondos creada', [
                'form_id' => $formId,
                'related_form_id' => $originalFormId,
                'user_id' => $tempData['user_id']
            ]);

            // Limpiar sesiones
            unset($_SESSION['pending_declaracion']);
            unset($_SESSION['temp_user_data']);

            echo json_encode([
                'success' => true,
                'message' => 'Declaración de Origen de Fondos enviada correctamente',
                'form_id' => $formId,
                'complete' => true
            ]);
            exit;

        } catch (\Exception $e) {
            $this->logger->error('Error al crear declaración de fondos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Asegurar que siempre se devuelva JSON incluso en errores
            if (ob_get_level()) {
                ob_clean();
            }
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Error al guardar la declaración: ' . $e->getMessage()
            ]);
            exit;
        } catch (\Throwable $e) {
            // Capturar errores fatales también
            if (ob_get_level()) {
                ob_clean();
            }
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Error crítico: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Generar PDF para revisores (sin auth de usuario, valida sesión de revisor)
     */
    public function generatePdfReviewer(string $id): void
    {
        if (empty($_SESSION['reviewer_id'])) {
            http_response_code(403);
            echo 'Acceso denegado';
            return;
        }
        // Reutilizar generatePdf — simular la misma lógica
        $this->generatePdf($id);
    }
}
