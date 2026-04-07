<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Form;
use App\Models\Reviewer;
use App\Services\MailService;
use App\Services\Logger;

/**
 * Controlador de Aprobaciones
 * 
 * Gestiona el proceso de aprobación/rechazo de formularios por revisores.
 * Incluye autenticación de revisores y envío de notificaciones.
 * 
 * Funciones principales:
 * - login/processLogin: Autenticación de revisores
 * - dashboard: Panel de control con todos los formularios
 * - show: Mostrar formulario para aprobar/rechazar
 * - process: Procesar decisión y enviar notificaciones
 * 
 * @package App\Controllers
 */
class ApprovalController extends Controller
{
    private Form $formModel;
    private Reviewer $reviewerModel;
    private MailService $mailService;
    private Logger $logger;

    public function __construct()
    {
        $this->formModel = new Form();
        $this->reviewerModel = new Reviewer();
        $this->mailService = new MailService();
        $this->logger = new Logger();
    }

    /**
     * Show reviewer login
     */
    public function login(): void
    {
        // Check if already logged in as reviewer
        if (isset($_SESSION['reviewer_id'])) {
            $this->redirect('/reviewer/dashboard');
        }

        $this->view('approval/login', [
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Process reviewer login
     */
    public function processLogin(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $email = $this->input('email') ?? $this->input('reviewer_email');
        $password = $this->input('password') ?? $this->input('reviewer_password');

        if (!$email || !$password) {
            $this->json(['error' => 'Email y contraseña son requeridos'], 400);
        }

        $this->logger->info('Reviewer login attempt', ['email' => $email]);

        // Usar AuthService para autenticar
        $authService = new \App\Services\AuthService();
        $result = $authService->login($email, $password, null);

        if (!$result) {
            $this->logger->warning('Reviewer login failed - invalid credentials', ['email' => $email]);
            $this->json(['error' => 'Credenciales inválidas'], 401);
        }

        if ($result['user']['role'] !== 'revisor') {
            $this->logger->warning('Reviewer login failed - not a reviewer', [
                'email' => $email,
                'role' => $result['user']['role']
            ]);
            $this->json(['error' => 'No tienes permisos de revisor'], 403);
        }

        $this->logger->info('Reviewer logged in', ['reviewer_id' => $result['user']['id']]);

        $this->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'redirect' => '/reviewer/dashboard'
        ]);
    }

    /**
     * Reviewer dashboard
     */
    public function dashboard(): void
    {
        // Check if logged in
        if (!isset($_SESSION['reviewer_id'])) {
            $this->redirect('/reviewer/login');
        }

        $forms = $this->formModel->getAllForms();

        $this->view('approval/dashboard', [
            'forms' => $forms,
            'reviewer_name' => $_SESSION['reviewer_name'],
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Reviewer logout
     */
    public function logout(): void
    {
        $this->logger->info('Reviewer logged out', ['reviewer_id' => $_SESSION['reviewer_id'] ?? null]);

        unset($_SESSION['reviewer_id']);
        unset($_SESSION['reviewer_name']);
        unset($_SESSION['reviewer_email']);

        $this->redirect('/reviewer/login');
    }

    /**
     * Show approval form
     */
    public function show(string $token): void
    {
        $form = $this->formModel->findByApprovalToken($token);
        
        if (!$form) {
            http_response_code(404);
            echo 'Token de aprobación inválido o expirado';
            return;
        }

        // Las declaraciones adjuntas no se revisan de forma independiente.
        // Se revisa siempre el formulario principal.
        $isDeclaration = str_starts_with((string)($form['form_type'] ?? ''), 'declaracion');
        if ($isDeclaration && !empty($form['related_form_id'])) {
            $parent = $this->formModel->findById((int)$form['related_form_id']);
            if ($parent && !empty($parent['approval_token'])) {
                $this->redirect('/approval/' . $parent['approval_token']);
                return;
            }
        }

        // Check if already approved/rejected
        if ($form['approval_status'] !== 'pending') {
            $this->view('approval/already_processed', [
                'form' => $form
            ]);
            return;
        }

        // Buscar formularios relacionados con observaciones pendientes
        $relatedForms = [];
        if (!empty($form['user_id']) && !empty($form['nit'])) {
            $relatedForms = $this->formModel->findRelatedFormsWithObservations(
                $form['user_id'], 
                $form['nit'],
                $form['id'] // Excluir el formulario actual
            );
        }

        // Adjuntos del formulario principal (evidencias del usuario)
        $attachmentModel = new \App\Models\Attachment();
        $attachments = $attachmentModel->getByFormId((int)$form['id']);

        // Check if reviewer is logged in
        $isLoggedIn = isset($_SESSION['reviewer_id']);
        $reviewerName = $_SESSION['reviewer_name'] ?? '';

        $this->view('approval/form', [
            'form' => $form,
            'token' => $token,
            'csrf_token' => $this->generateCsrfToken(),
            'is_logged_in' => $isLoggedIn,
            'reviewer_name' => $reviewerName,
            'related_forms' => $relatedForms,
            'attachments' => $attachments
        ]);
    }

    /**
     * Process approval decision
     */
    public function process(string $token): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $form = $this->formModel->findByApprovalToken($token);
        
        if (!$form) {
            $this->json(['error' => 'Token de aprobación inválido'], 404);
        }

        $isDeclaration = str_starts_with((string)($form['form_type'] ?? ''), 'declaracion');
        if ($isDeclaration) {
            $this->json(['error' => 'La declaración adjunta se procesa junto al formulario principal'], 400);
        }

        if ($form['approval_status'] !== 'pending') {
            $this->json(['error' => 'Este formulario ya fue procesado'], 400);
        }

        $decision = $this->input('decision'); // 'approved' or 'rejected'
        $approvedBy = $this->input('approved_by');
        $observations = $this->input('observations', '');
        $markAsCorrectedId = $this->input('mark_as_corrected_id'); // ID del formulario anterior a marcar como corregido

        if (!in_array($decision, ['approved', 'rejected'])) {
            $this->json(['error' => 'Decisión inválida'], 400);
        }

        if (empty($approvedBy)) {
            $this->json(['error' => 'El nombre es requerido'], 400);
        }

        try {
            // Guardar campos internos "ESPACIO EXCLUSIVO PARA POLLO FIESTA"
            $this->saveExclusivePolloFiestaFields((int)$form['id']);

            // Determinar el estado final según si hay observaciones
            $finalStatus = $decision;
            if ($decision === 'approved' && !empty(trim($observations))) {
                $finalStatus = 'approved_pending'; // Aprobado con observaciones
            }
            
            // Update form approval status
            $this->formModel->updateApprovalStatus(
                $form['id'],
                $finalStatus,
                $approvedBy,
                $observations
            );

            // Si se aprueba completamente Y se marcó un formulario anterior para corregir
            if ($decision === 'approved' && empty(trim($observations)) && !empty($markAsCorrectedId)) {
                $this->formModel->markAsCorrected((int)$markAsCorrectedId, $form['id']);
                $this->logger->info('Previous form marked as corrected', [
                    'old_form_id' => $markAsCorrectedId,
                    'new_form_id' => $form['id']
                ]);
            }

            $downloadUrl = null;

            // Si se aprueba (con o sin observaciones), consolidar y firmar PDFs automáticamente
            if ($decision === 'approved') {
                $downloadUrl = $this->autoConsolidateAndSignPDFs($form['id']);
            }

            // Send notification email to form creator
            $this->sendApprovalNotification($form, $decision, $approvedBy, $observations, $finalStatus);

            $this->logger->info('Form approval processed', [
                'form_id' => $form['id'],
                'decision' => $decision,
                'final_status' => $finalStatus,
                'approved_by' => $approvedBy
            ]);

            // Determinar mensaje según el estado final
            $message = 'Formulario rechazado';
            if ($decision === 'approved') {
                $message = $finalStatus === 'approved_pending' 
                    ? 'Formulario aprobado con observaciones (pendiente de correcciones)' 
                    : 'Formulario aprobado exitosamente';
            }

            $response = [
                'success' => true,
                'message' => $message,
                'decision' => $decision,
                'final_status' => $finalStatus
            ];

            // Agregar URL de descarga si está disponible
            if ($downloadUrl) {
                $response['download_url'] = $downloadUrl;
            }

            $this->json($response);

        } catch (\Exception $e) {
            $this->logger->error('Approval processing failed', [
                'form_id' => $form['id'],
                'error' => $e->getMessage()
            ]);

            $this->json(['error' => 'Error al procesar la aprobación'], 500);
        }
    }

    /**
     * Guarda los campos internos de revisión en la tabla forms.
     */
    private function saveExclusivePolloFiestaFields(int $formId): void
    {
        $db = $this->formModel->getConnection();
        $stmt = $db->prepare("
            UPDATE forms
            SET consulta_ofac = ?,
                consulta_listas_nacionales = ?,
                consulta_onu = ?,
                consulta_interpol = ?,
                recibe = ?,
                director_cartera = ?,
                gerencia_comercial = ?,
                verificado_por = ?,
                preparo = ?,
                reviso = ?,
                nombre_oficial = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $this->input('consulta_ofac') ?: null,
            $this->input('consulta_listas_nacionales') ?: null,
            $this->input('consulta_onu') ?: null,
            $this->input('consulta_interpol') ?: null,
            $this->input('recibe') ?: null,
            $this->input('director_cartera') ?: null,
            $this->input('gerencia_comercial') ?: null,
            $this->input('verificado_por') ?: null,
            $this->input('preparo') ?: null,
            $this->input('reviso') ?: null,
            $this->input('nombre_oficial') ?: null,
            $formId,
        ]);
    }

    /**
     * Send approval notification to form creator and administrators
     */
    private function sendApprovalNotification(array $form, string $decision, string $approvedBy, string $observations, string $finalStatus): void
    {
        try {
            // Get form creator email
            $creator = $this->formModel->getFormCreator($form['id']);
            
            if (!$creator || !$creator['email']) {
                return;
            }

            $isApproved = $decision === 'approved';
            $hasPendingCorrections = $finalStatus === 'approved_pending';
            
            // Determinar clase y texto del estado
            if ($hasPendingCorrections) {
                $statusClass = 'status-pending';
                $statusText = 'APROBADO CON OBSERVACIONES';
            } else {
                $statusClass = $isApproved ? 'status-approved' : 'status-rejected';
                $statusText = $isApproved ? 'APROBADO' : 'RECHAZADO';
            }
            
            // Obtener imagen de firma embebida
            $signatureImage = \App\Helpers\EmailHelper::getSignatureImage();
            $statusIcon = $isApproved ? '✓' : '✗';

            $titleEmail = 'Formulario SAGRILAFT ' . ($isApproved ? 'Aprobado' : 'Rechazado');
            $badgeClass = $hasPendingCorrections ? 'badge-yellow' : ($isApproved ? 'badge-green' : 'badge-red');

            $body = \App\Helpers\EmailHelper::emailHeader($titleEmail) . "
                <div class='badge {$badgeClass}'>{$statusIcon} {$statusText}</div>
                <p class='msg'>Hola <strong style='color:#e2e8f0;'>" . htmlspecialchars($creator['name'] ?? 'Usuario') . "</strong>, tu formulario ha sido " . ($isApproved ? 'aprobado' : 'rechazado') . " por Pollo Fiesta S.A.</p>
                <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                    <tr>
                        <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
                            <div class='info-item'><span class='info-label'>ID Formulario</span><span class='info-value'>#" . $form['id'] . "</span></div>
                        </td>
                        <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
                            <div class='info-item'><span class='info-label'>" . (!empty($form['nit']) ? 'NIT/Cédula' : 'Documento') . "</span><span class='info-value'>" . htmlspecialchars($form['nit'] ?? 'N/A') . "</span></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' style='padding:0 0 6px 0;'>
                            <div class='info-item'><span class='info-label'>Revisado por</span><span class='info-value'>Oficial de Cumplimiento — Pollo Fiesta S.A.</span></div>
                        </td>
                    </tr>
                </table>
                " . (!empty($observations) ? "
                <div class='obs-box" . (!$isApproved ? ' red' : '') . "'>
                    <strong>Observaciones</strong>
                    <p>" . nl2br(htmlspecialchars($observations)) . "</p>
                </div>" : "") . "
                " . ($isApproved ? "<p style='font-size:12px;color:#475569;margin-top:12px;'>📎 Se adjunta el PDF del formulario aprobado y firmado digitalmente.</p>" : "") . "
            " . \App\Helpers\EmailHelper::emailFooter();

            $subject = 'SAGRILAFT - Formulario ' . ($decision === 'approved' ? 'Aprobado' : 'Rechazado');

            // Preparar imágenes embebidas
            $embeddedImages = [];
            $signaturePath = \App\Helpers\EmailHelper::getSignatureImagePath();
            if ($signaturePath !== null) {
                $embeddedImages['signature'] = $signaturePath;
            }
            $logoPath = \App\Helpers\EmailHelper::getLogoImagePath();
            if ($logoPath !== null) {
                $embeddedImages['logo'] = $logoPath;
            }

            // Preparar archivos adjuntos (solo si está aprobado)
            $attachments = [];
            if ($decision === 'approved') {
                // 1. Obtener el PDF consolidado y firmado
                $db = \App\Core\Database::getConnection();
                $stmt = $db->prepare("SELECT file_data FROM form_consolidated_pdfs WHERE form_id = ? ORDER BY signed DESC, id DESC LIMIT 1");
                $stmt->execute([$form['id']]);
                $consolidatedPdf = $stmt->fetch();
                
                if ($consolidatedPdf && !empty($consolidatedPdf['file_data'])) {
                    // Crear archivo temporal con el PDF firmado
                    $tempPath = sys_get_temp_dir() . '/' . uniqid('pdf_') . '_Formulario_Aprobado_' . $form['id'] . '.pdf';
                    file_put_contents($tempPath, $consolidatedPdf['file_data']);
                    
                    $attachments[] = [
                        'path' => $tempPath,
                        'name' => 'Formulario_Aprobado_' . $form['id'] . '.pdf',
                        'temp' => true
                    ];
                }
                
                // 2. Obtener todos los documentos adjuntos por el usuario
                $stmt = $db->prepare("SELECT filename, file_data FROM form_attachments WHERE form_id = ? ORDER BY id");
                $stmt->execute([$form['id']]);
                $userAttachments = $stmt->fetchAll();
                
                foreach ($userAttachments as $attachment) {
                    // Crear archivo temporal con los datos
                    $tempPath = sys_get_temp_dir() . '/' . uniqid('attach_') . '_' . $attachment['filename'];
                    file_put_contents($tempPath, $attachment['file_data']);
                    
                    $attachments[] = [
                        'path' => $tempPath,
                        'name' => $attachment['filename'],
                        'temp' => true // Marcar para eliminar después
                    ];
                }
            }

            // Lista de destinatarios
            $recipients = [];
            
            // MODO PRUEBA: Solo enviar a juan.david.rojas.burbano0@gmail.com
            // Cuando esté listo para producción, descomentar las otras secciones
            
            $recipients[] = [
                'email' => 'juan.david.rojas.burbano0@gmail.com',
                'name' => 'Admin Prueba',
                'type' => 'admin'
            ];
            
            /* DESACTIVADO TEMPORALMENTE - Descomentar cuando esté listo
            
            // 1. Creador del formulario
            $recipients[] = [
                'email' => $creator['email'],
                'name' => $creator['name'] ?? 'Usuario',
                'type' => 'creator'
            ];
            
            // 2. Asesor comercial y su jefe (si existe)
            if (!empty($form['asesor_comercial_id'])) {
                $asesorModel = new \App\Models\AsesorComercial();
                $asesor = $asesorModel->findById((int)$form['asesor_comercial_id']);
                
                if ($asesor && !empty($asesor['email'])) {
                    $recipients[] = [
                        'email' => $asesor['email'],
                        'name' => $asesor['nombre_completo'],
                        'type' => 'asesor'
                    ];
                    
                    // Agregar jefe si existe
                    if (!empty($asesor['jefe_email'])) {
                        $recipients[] = [
                            'email' => $asesor['jefe_email'],
                            'name' => $asesor['jefe_nombre'] ?? 'Jefe',
                            'type' => 'jefe'
                        ];
                    }
                }
            }
            
            */

            // Enviar correos a todos los destinatarios
            foreach ($recipients as $recipient) {
                try {
                    $this->mailService->sendViaSMTPWithImages(
                        $recipient['email'], 
                        $subject, 
                        $body, 
                        $embeddedImages, 
                        $attachments
                    );
                    
                    $this->logger->info('Email sent', [
                        'to' => $recipient['email'],
                        'type' => $recipient['type'],
                        'form_id' => $form['id'],
                        'decision' => $decision
                    ]);
                } catch (\Exception $e) {
                    $this->logger->error('Failed to send email', [
                        'to' => $recipient['email'],
                        'type' => $recipient['type'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Limpiar archivos temporales
            foreach ($attachments as $attachment) {
                if (isset($attachment['temp']) && $attachment['temp'] && file_exists($attachment['path'])) {
                    unlink($attachment['path']);
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('Approval notification failed', [
                'form_id' => $form['id'],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Consolidar y firmar PDFs automáticamente al aprobar
     * 
     * @param int $formId ID del formulario aprobado
     * @return string|null URL de descarga del PDF firmado
     */
    private function autoConsolidateAndSignPDFs(int $formId): ?string
    {
        try {
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM forms WHERE id = ? LIMIT 1");
            $stmt->execute([$formId]);
            $mainForm = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$mainForm) {
                $this->logger->warning('Main form not found for consolidation', ['form_id' => $formId]);
                return null;
            }

            // Formularios: principal + relacionados (declaraciones u otros vinculados)
            $stmt = $db->prepare("SELECT * FROM forms WHERE related_form_id = ? ORDER BY id ASC");
            $stmt->execute([$formId]);
            $relatedForms = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Adjuntos: incluir solo PDFs en el consolidado
            $stmt = $db->prepare("SELECT id, filename, mime_type, file_data FROM form_attachments WHERE form_id = ? ORDER BY id ASC");
            $stmt->execute([$formId]);
            $attachments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $tempDir = sys_get_temp_dir() . '/sagrilaft_' . uniqid();
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $pdfPaths = [];
            $tempFiles = [];
            $filler = new \App\Services\FormPdfFiller();

            // 1) PDF del formulario principal
            $mainPdf = $this->buildFormPdfBinary($mainForm, null, $filler);
            if ($mainPdf !== null) {
                $mainPath = $tempDir . '/01_formulario_principal.pdf';
                file_put_contents($mainPath, $mainPdf);
                $pdfPaths[] = $mainPath;
                $tempFiles[] = $mainPath;
            }

            // 2) PDFs de formularios relacionados (declaraciones)
            $relatedIndex = 2;
            foreach ($relatedForms as $rf) {
                $rfPdf = $this->buildFormPdfBinary($rf, $mainForm, $filler);
                if ($rfPdf === null) {
                    continue;
                }
                $rfPath = $tempDir . '/0' . $relatedIndex . '_declaracion_' . (int)$rf['id'] . '.pdf';
                file_put_contents($rfPath, $rfPdf);
                $pdfPaths[] = $rfPath;
                $tempFiles[] = $rfPath;
                $relatedIndex++;
            }

            // 3) PDFs adjuntos del usuario (evidencias, documentos adicionales)
            // NOTA: Solo incluir PDFs que NO sean el formulario generado automáticamente
            $attachmentIndex = 10; // Empezar desde 10 para dejar espacio
            foreach ($attachments as $attachment) {
                if (empty($attachment['file_data'])) {
                    continue;
                }
                
                // Verificar que sea PDF
                $mime = strtolower((string)($attachment['mime_type'] ?? ''));
                $name = strtolower((string)($attachment['filename'] ?? ''));
                $isPdf = ($mime === 'application/pdf') || str_ends_with($name, '.pdf');
                if (!$isPdf) {
                    continue;
                }
                
                // Saltar si es el PDF generado automáticamente (evitar duplicados)
                $generatedFilename = $mainForm['generated_pdf_filename'] ?? '';
                if (!empty($generatedFilename) && $attachment['filename'] === $generatedFilename) {
                    continue;
                }
                
                $attPath = $tempDir . '/' . $attachmentIndex . '_adjunto_' . (int)$attachment['id'] . '.pdf';
                file_put_contents($attPath, $attachment['file_data']);
                $pdfPaths[] = $attPath;
                $tempFiles[] = $attPath;
                $attachmentIndex++;
            }

            if (empty($pdfPaths)) {
                @rmdir($tempDir);
                $this->logger->warning('No PDF files available for consolidation', ['form_id' => $formId]);
                return null;
            }

            // Consolidar PDFs
            $pdfService = new \App\Services\PdfConsolidatorService();
            $consolidatedPath = $tempDir . '/consolidated.pdf';
            $result = $pdfService->consolidatePDFs($pdfPaths, $consolidatedPath);
            $consolidatedData = file_get_contents($consolidatedPath);

            // Firma: priorizar la del usuario dueño del formulario; fallback a revisor
            $signatureOwnerId = (int)($mainForm['user_id'] ?? 0);
            $stmtSig = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
            $stmtSig->execute([$signatureOwnerId]);
            $firma = $stmtSig->fetch(\PDO::FETCH_ASSOC);

            if (!$firma || empty($firma['firma_data'])) {
                $stmtSig->execute([$_SESSION['user_id'] ?? $_SESSION['reviewer_id'] ?? null]);
                $firma = $stmtSig->fetch(\PDO::FETCH_ASSOC);
            }

            $signedData = $consolidatedData;
            $signed = 0;

            if ($firma && !empty($firma['firma_data'])) {
                $signaturePath = $tempDir . '/signature.' . (str_contains((string)($firma['mime_type'] ?? ''), 'jpeg') ? 'jpg' : 'png');
                $signedPath = $tempDir . '/signed.pdf';
                file_put_contents($signaturePath, $firma['firma_data']);
                $pdfService->signPDF($consolidatedPath, $signaturePath, $signedPath);
                if (file_exists($signedPath) && filesize($signedPath) > 0) {
                    $signedData = file_get_contents($signedPath);
                    $signed = 1;
                }
                @unlink($signaturePath);
                @unlink($signedPath);
            }

            // Upsert del consolidado final
            $stmt = $db->prepare("SELECT id FROM form_consolidated_pdfs WHERE form_id = ? LIMIT 1");
            $stmt->execute([$formId]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($existing) {
                $stmt = $db->prepare("
                    UPDATE form_consolidated_pdfs
                    SET file_data = ?,
                        total_pages = ?,
                        signed = ?,
                        signed_by = ?,
                        signed_at = " . ($signed ? "NOW()" : "NULL") . ",
                        created_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([
                    $signedData,
                    (int)($result['total_pages'] ?? 0),
                    $signed,
                    $signed ? ($signatureOwnerId ?: ($_SESSION['user_id'] ?? $_SESSION['reviewer_id'] ?? null)) : null,
                    (int)$existing['id']
                ]);
                $consolidatedId = (int)$existing['id'];
            } else {
                $stmt = $db->prepare("
                    INSERT INTO form_consolidated_pdfs (form_id, filepath, file_data, total_pages, signed, signed_by, signed_at, created_at)
                    VALUES (?, '', ?, ?, ?, ?, " . ($signed ? "NOW()" : "NULL") . ", NOW())
                ");
                $stmt->execute([
                    $formId,
                    $signedData,
                    (int)($result['total_pages'] ?? 0),
                    $signed,
                    $signed ? ($signatureOwnerId ?: ($_SESSION['user_id'] ?? $_SESSION['reviewer_id'] ?? null)) : null
                ]);
                $consolidatedId = (int)$db->lastInsertId();
            }

            // Limpieza
            @unlink($consolidatedPath);
            foreach ($tempFiles as $tmp) {
                @unlink($tmp);
            }
            @rmdir($tempDir);

            return '/forms/consolidated/' . $consolidatedId . '/download';

        } catch (\Exception $e) {
            // No fallar la aprobación si falla la firma
            $this->logger->error('Auto-signing failed', [
                'form_id' => $formId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Genera (o recupera) el PDF binario de un formulario para consolidación.
     */
    private function buildFormPdfBinary(array $form, ?array $relatedForm, \App\Services\FormPdfFiller $filler): ?string
    {
        try {
            if (!empty($form['generated_pdf_content'])) {
                return $form['generated_pdf_content'];
            }

            // Normalizar accionistas para FormPdfFiller
            if (!empty($form['accionistas']) && is_string($form['accionistas'])) {
                $accionistas = json_decode($form['accionistas'], true);
                if (is_array($accionistas)) {
                    $form['accionista_nombre'] = array_column($accionistas, 'nombre');
                    $form['accionista_documento'] = array_column($accionistas, 'documento');
                    $form['accionista_participacion'] = array_column($accionistas, 'participacion');
                    $form['accionista_nacionalidad'] = array_column($accionistas, 'nacionalidad');
                    $form['accionista_cc'] = array_column($accionistas, 'cc');
                    $form['accionista_ce'] = array_column($accionistas, 'ce');
                }
            }

            // Determinar el tipo de formulario correctamente
            $formType = $form['form_type'] ?? 'cliente';
            $personType = $form['person_type'] ?? 'natural';
            
            // Para declaraciones, mantener el form_type original
            if (str_starts_with($formType, 'declaracion')) {
                $tempData = [
                    'role' => $formType,
                    'user_type' => $formType,
                    'person_type' => 'declaracion',
                ];
            } else {
                $tempData = [
                    'role' => $formType,
                    'user_type' => $formType,
                    'person_type' => $personType,
                ];
            }

            if ($relatedForm) {
                $tempData['related_form'] = $relatedForm;
            }

            return $filler->generate($form, $tempData);
        } catch (\Throwable $e) {
            $this->logger->warning('Failed building form PDF for consolidation', [
                'form_id' => $form['id'] ?? null,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
