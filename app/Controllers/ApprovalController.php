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
        try {
            if (!$this->validateCsrf()) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
            }

            // Aceptar username (que es el campo 'name' en la tabla)
            $username = $this->input('username') ?? $this->input('email') ?? $this->input('reviewer_email');
            $password = $this->input('password') ?? $this->input('reviewer_password');

            if (!$username || !$password) {
                $this->json(['error' => 'Usuario y contraseña son requeridos'], 400);
            }

            $this->logger->info('Reviewer login attempt', ['username' => $username]);

            // Buscar usuario por name Y que sea revisor (esto filtra los duplicados)
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE name = ? AND role = 'revisor' LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $this->logger->warning('Reviewer login failed - invalid credentials', ['username' => $username]);
                $this->json(['error' => 'Credenciales inválidas o no tiene permisos de revisor'], 401);
            }

            // Establecer sesión de revisor
            $_SESSION['reviewer_id'] = $user['id'];
            $_SESSION['reviewer_name'] = $user['name'];
            $_SESSION['reviewer_email'] = $user['email'];

            $this->logger->info('Reviewer logged in', ['reviewer_id' => $user['id']]);

            $this->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'redirect' => 'index.php?route=/reviewer/dashboard'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Reviewer login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
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
     * Subir firma digital del revisor
     */
    public function uploadFirma(): void
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['reviewer_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }

        if (!isset($_FILES['firma']) || $_FILES['firma']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No se recibió el archivo']);
            exit;
        }

        $file = $_FILES['firma'];
        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PNG o JPG']);
            exit;
        }

        if ($file['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'El archivo no debe superar 2MB']);
            exit;
        }

        try {
            $imageData = file_get_contents($file['tmp_name']);
            $mimeType = $file['type'];

            $db = \App\Core\Database::getConnection();
            
            // Desactivar firmas anteriores del usuario
            $stmt = $db->prepare("UPDATE firmas_digitales SET activa = 0 WHERE user_id = ?");
            $stmt->execute([$_SESSION['reviewer_id']]);
            
            // Insertar nueva firma activa
            $stmt = $db->prepare("
                INSERT INTO firmas_digitales (user_id, firma_data, firma_size, mime_type, activa, created_at) 
                VALUES (?, ?, ?, ?, 1, NOW())
            ");
            $stmt->execute([
                $_SESSION['reviewer_id'],
                $imageData,
                $file['size'],
                $mimeType
            ]);

            if ($this->logger) {
                $this->logger->info('Firma digital actualizada', ['reviewer_id' => $_SESSION['reviewer_id']]);
            }

            echo json_encode(['success' => true, 'message' => 'Firma guardada correctamente']);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error al guardar firma', ['error' => $e->getMessage()]);
            }
            echo json_encode(['success' => false, 'message' => 'Error al guardar la firma: ' . $e->getMessage()]);
        }
        exit;
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

        // Si es empleado, obtener datos de la tabla form_empleados
        if (($form['form_type'] ?? '') === 'empleado') {
            $conn = \App\Core\Database::getConnection();
            $stmt = $conn->prepare("SELECT * FROM form_empleados WHERE form_id = ?");
            $stmt->execute([$form['id']]);  // Usar el ID del formulario, NO el ID de form_empleados
            $empleadoData = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($empleadoData) {
                // Guardar el ID original del formulario
                $formId = $form['id'];
                
                // Agregar datos de empleado al array del formulario
                $form = array_merge($form, $empleadoData);
                
                // Restaurar el ID correcto del formulario (no el de form_empleados)
                $form['id'] = $formId;
            }
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
            // Guardar campos internos "ESPACIO EXCLUSIVO PARA POLLO FIESTA" (solo si NO es empleado)
            $isEmpleado = isset($form['form_type']) && $form['form_type'] === 'empleado';
            if (!$isEmpleado) {
                $this->saveExclusivePolloFiestaFields((int)$form['id']);
            }

            // Determinar el estado final según si hay observaciones
            // IMPORTANTE: Para empleados NO existe "approved_pending", solo "approved" o "rejected"
            $finalStatus = $decision;
            if (!$isEmpleado && $decision === 'approved' && !empty(trim($observations))) {
                $finalStatus = 'approved_pending'; // Aprobado con observaciones (solo para formularios SAGRILAFT)
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
     * Guarda los campos internos de revisión en la tabla form_signatures.
     */
    private function saveExclusivePolloFiestaFields(int $formId): void
    {
        $db = $this->formModel->getConnection();
        
        $reviewerName = $_SESSION['reviewer_name'] ?? $this->input('approved_by') ?? 'Revisor';
        $reviewerId = $_SESSION['reviewer_id'] ?? null;

        // Obtener firmas digitales
        $userSignature = null;
        $officialSignature = null;

        // Obtener firma del usuario dueño del formulario
        $stmt = $db->prepare("SELECT user_id FROM forms WHERE id = ?");
        $stmt->execute([$formId]);
        $form = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($form && !empty($form['user_id'])) {
            $stmt = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
            $stmt->execute([$form['user_id']]);
            $userFirma = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userFirma && !empty($userFirma['firma_data'])) {
                $userSignature = 'data:' . ($userFirma['mime_type'] ?? 'image/png') . ';base64,' . base64_encode($userFirma['firma_data']);
            }
        }

        // Obtener firma del revisor actual
        if ($reviewerId) {
            $stmt = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
            $stmt->execute([$reviewerId]);
            $officialFirma = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($officialFirma && !empty($officialFirma['firma_data'])) {
                $officialSignature = 'data:' . ($officialFirma['mime_type'] ?? 'image/png') . ';base64,' . base64_encode($officialFirma['firma_data']);
            }
        }

        // Insertar o actualizar en form_signatures
        $stmt = $db->prepare("
            INSERT INTO form_signatures (
                form_id, user_signature_data, official_signature_data,
                vinculacion, fecha_vinculacion, actualizacion,
                consulta_ofac, consulta_listas_nacionales, consulta_onu, consulta_interpol,
                recibe, director_cartera, gerencia_comercial, verificado_por, preparo, reviso, nombre_oficial,
                reviewed_at, reviewed_by, reviewed_by_name
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
            ON DUPLICATE KEY UPDATE
                user_signature_data = VALUES(user_signature_data),
                official_signature_data = VALUES(official_signature_data),
                vinculacion = VALUES(vinculacion),
                fecha_vinculacion = VALUES(fecha_vinculacion),
                actualizacion = VALUES(actualizacion),
                consulta_ofac = VALUES(consulta_ofac),
                consulta_listas_nacionales = VALUES(consulta_listas_nacionales),
                consulta_onu = VALUES(consulta_onu),
                consulta_interpol = VALUES(consulta_interpol),
                recibe = VALUES(recibe),
                director_cartera = VALUES(director_cartera),
                gerencia_comercial = VALUES(gerencia_comercial),
                verificado_por = VALUES(verificado_por),
                preparo = VALUES(preparo),
                reviso = VALUES(reviso),
                nombre_oficial = VALUES(nombre_oficial),
                reviewed_at = NOW(),
                reviewed_by = VALUES(reviewed_by),
                reviewed_by_name = VALUES(reviewed_by_name),
                updated_at = NOW()
        ");

        $stmt->execute([
            $formId,
            $userSignature,
            $officialSignature,
            $this->input('vinculacion') ?: null,
            $this->input('fecha_vinculacion') ?: null,
            $this->input('actualizacion') ?: null,
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
            $reviewerId,
            $reviewerName,
        ]);
    }

    /**
     * Send approval notification to form creator and administrators
     */
    /**
     * Genera el mensaje de adjuntos según el tipo de formulario y decisión
     */
    private function getAttachmentMessage(array $form, string $decision, string $finalStatus): string
    {
        // No mostrar mensaje si fue rechazado
        if ($decision !== 'approved') {
            return '';
        }
        
        $isEmpleado = isset($form['form_type']) && $form['form_type'] === 'empleado';
        $hasPendingCorrections = $finalStatus === 'approved_pending';
        
        if ($isEmpleado) {
            // Para empleados: mensaje simple sobre documentos adjuntos
            return "<p style='font-size:12px;color:#475569;margin-top:12px;'>📎 Se adjuntan los documentos del registro de empleado.</p>";
        } else {
            // Para otros formularios: mencionar firma digital solo si está completamente aprobado
            if ($hasPendingCorrections) {
                return "<p style='font-size:12px;color:#475569;margin-top:12px;'>📎 Se adjunta el PDF del formulario aprobado con observaciones.</p>";
            } else {
                return "<p style='font-size:12px;color:#475569;margin-top:12px;'>📎 Se adjunta el PDF del formulario aprobado y firmado digitalmente.</p>";
            }
        }
    }

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
                " . ($this->getAttachmentMessage($form, $decision, $finalStatus)) . "
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

            // Preparar datos del formulario para destinatarios
            $formData = [
                'creator_email' => $creator['email'] ?? null,
                'creator_name' => $creator['name'] ?? 'Usuario',
            ];
            
            // Obtener datos del asesor comercial si existe
            if (!empty($form['asesor_comercial_id'])) {
                $asesorModel = new \App\Models\AsesorComercial();
                $asesor = $asesorModel->findById((int)$form['asesor_comercial_id']);
                
                if ($asesor) {
                    $formData['asesor_email'] = $asesor['email'] ?? null;
                    $formData['asesor_nombre'] = $asesor['nombre_completo'] ?? null;
                    $formData['jefe_email'] = $asesor['jefe_email'] ?? null;
                    $formData['jefe_nombre'] = $asesor['jefe_nombre'] ?? null;
                }
            }
            
            // Obtener destinatarios según el tipo de formulario y decisión
            $formType = $form['form_type'] ?? 'cliente';
            if ($decision === 'approved') {
                $recipients = \App\Config\EmailRecipientsConfig::getApprovedRecipients($formType, $formData);
            } else {
                $recipients = \App\Config\EmailRecipientsConfig::getRejectedRecipients($formType, $formData);
            }

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
            
            // IMPORTANTE: Recargar el formulario desde la BD para obtener los campos actualizados
            // (consulta_ofac, consulta_listas_nacionales, recibe, director_cartera, etc.)
            // Solo el formulario principal necesita ser recargado ya que es donde se guardan estos campos
            $stmt = $db->prepare("SELECT * FROM forms WHERE id = ? LIMIT 1");
            $stmt->execute([$formId]);
            $mainForm = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$mainForm) {
                $this->logger->warning('Main form not found for consolidation', ['form_id' => $formId]);
                return null;
            }
            
            // Obtener datos de firmas y campos del revisor desde form_signatures
            $stmt = $db->prepare("SELECT * FROM form_signatures WHERE form_id = ? LIMIT 1");
            $stmt->execute([$formId]);
            $signatureData = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($signatureData) {
                // Agregar firmas al formulario principal
                if (!empty($signatureData['user_signature_data'])) {
                    $mainForm['signature_data'] = $signatureData['user_signature_data'];
                }
                if (!empty($signatureData['official_signature_data'])) {
                    $mainForm['firma_oficial_data'] = $signatureData['official_signature_data'];
                    $mainForm['firma_oficial_cumplimiento_data'] = $signatureData['official_signature_data'];
                }
                
                // Agregar campos del revisor
                $mainForm = array_merge($mainForm, [
                    'vinculacion' => $signatureData['vinculacion'],
                    'fecha_vinculacion' => $signatureData['fecha_vinculacion'],
                    'actualizacion' => $signatureData['actualizacion'],
                    'consulta_ofac' => $signatureData['consulta_ofac'],
                    'consulta_listas_nacionales' => $signatureData['consulta_listas_nacionales'],
                    'consulta_onu' => $signatureData['consulta_onu'],
                    'consulta_interpol' => $signatureData['consulta_interpol'],
                    'recibe' => $signatureData['recibe'],
                    'director_cartera' => $signatureData['director_cartera'],
                    'gerencia_comercial' => $signatureData['gerencia_comercial'],
                    'verificado_por' => $signatureData['verificado_por'],
                    'preparo' => $signatureData['preparo'],
                    'reviso' => $signatureData['reviso'],
                    'nombre_oficial' => $signatureData['nombre_oficial'],
                ]);
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
                // Agregar firmas del revisor y usuario a las declaraciones
                if (!empty($mainForm['firma_oficial_data'])) {
                    $rf['firma_oficial_data'] = $mainForm['firma_oficial_data'];
                    $rf['firma_oficial_cumplimiento_data'] = $mainForm['firma_oficial_data'];
                }
                if (!empty($mainForm['signature_data'])) {
                    $rf['signature_data'] = $mainForm['signature_data'];
                    $rf['firma_declarante_data'] = $mainForm['signature_data'];
                    $rf['firma_representante_data'] = $mainForm['signature_data'];
                    $rf['firma_data'] = $mainForm['signature_data'];
                }
                
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
            
            // 4) Hoja del revisor al final (si el formulario ya fue revisado)
            if (!empty($mainForm['reviewed_at']) || !empty($mainForm['status']) && in_array($mainForm['status'], ['approved', 'rejected', 'approved_pending'])) {
                $reviewerPagePdf = $this->generateReviewerPage($mainForm);
                if ($reviewerPagePdf !== null) {
                    $reviewerPath = $tempDir . '/99_hoja_revisor.pdf';
                    file_put_contents($reviewerPath, $reviewerPagePdf);
                    $pdfPaths[] = $reviewerPath;
                    $tempFiles[] = $reviewerPath;
                }
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
            // IMPORTANTE: NO usar el PDF cacheado cuando se está consolidando después de aprobación
            // porque necesitamos los campos actualizados (consulta_ofac, recibe, director_cartera, etc.)
            // Solo usar cache si el formulario NO ha sido aprobado aún
            $isApproved = !empty($form['approval_status']) && in_array($form['approval_status'], ['approved', 'rejected', 'approved_pending']);
            
            if (!$isApproved && $relatedForm === null && !empty($form['generated_pdf_content'])) {
                return $form['generated_pdf_content'];
            }

            // Obtener campos del revisor desde la tabla form_signatures
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM form_signatures WHERE form_id = ? LIMIT 1");
            $stmt->execute([$form['id']]);
            $signatureData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($signatureData) {
                // Agregar campos del revisor al formulario
                $form = array_merge($form, [
                    'vinculacion' => $signatureData['vinculacion'],
                    'fecha_vinculacion' => $signatureData['fecha_vinculacion'],
                    'actualizacion' => $signatureData['actualizacion'],
                    'consulta_ofac' => $signatureData['consulta_ofac'],
                    'consulta_listas_nacionales' => $signatureData['consulta_listas_nacionales'],
                    'consulta_onu' => $signatureData['consulta_onu'],
                    'consulta_interpol' => $signatureData['consulta_interpol'],
                    'recibe' => $signatureData['recibe'],
                    'director_cartera' => $signatureData['director_cartera'],
                    'gerencia_comercial' => $signatureData['gerencia_comercial'],
                    'verificado_por' => $signatureData['verificado_por'],
                    'preparo' => $signatureData['preparo'],
                    'reviso' => $signatureData['reviso'],
                    'nombre_oficial' => $signatureData['nombre_oficial'],
                    'reviewed_at' => $signatureData['reviewed_at'],
                    'reviewed_by' => $signatureData['reviewed_by'],
                    'reviewed_by_name' => $signatureData['reviewed_by_name'],
                ]);
                
                // Agregar firmas digitales
                if (!empty($signatureData['user_signature_data'])) {
                    $form['signature_data'] = $signatureData['user_signature_data'];
                }
                if (!empty($signatureData['official_signature_data'])) {
                    $form['firma_oficial_data'] = $signatureData['official_signature_data'];
                    $form['firma_oficial_cumplimiento_data'] = $signatureData['official_signature_data'];
                }
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

    /**
     * Genera una página PDF con la información de revisión del formulario
     */
    private function generateReviewerPage(array $form): ?string
    {
        try {
            require_once __DIR__ . '/../Libraries/fpdf.php';
            
            $pdf = new \FPDF('P', 'mm', 'Letter');
            $pdf->AddPage();
            $pdf->SetMargins(15, 15, 15);
            
            // Encabezado
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 12, 'HOJA DE REVISION', 0, 1, 'C', true);
            $pdf->Ln(5);
            
            // Información del formulario
            $pdf->SetFillColor(241, 245, 249);
            $pdf->SetTextColor(15, 23, 42);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, 'INFORMACION DEL FORMULARIO', 0, 1, 'L', true);
            $pdf->Ln(2);
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 6, 'ID Formulario:', 0, 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, '#' . $form['id'], 0, 1);
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 6, 'Titulo:', 0, 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 6, $form['title'] ?? 'N/A');
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 6, 'Empresa/Nombre:', 0, 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, $form['company_name'] ?? 'N/A', 0, 1);
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 6, 'NIT/Cedula:', 0, 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, $form['nit'] ?? 'N/A', 0, 1);
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 6, 'Fecha de Envio:', 0, 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($form['created_at'])), 0, 1);
            
            $pdf->Ln(5);
            
            // Estado de revisión
            $pdf->SetFillColor(241, 245, 249);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, 'RESULTADO DE LA REVISION', 0, 1, 'L', true);
            $pdf->Ln(2);
            
            $status = $form['status'] ?? 'pending';
            $statusText = [
                'approved' => 'APROBADO',
                'rejected' => 'RECHAZADO',
                'approved_pending' => 'APROBADO CON OBSERVACIONES',
                'pending' => 'PENDIENTE'
            ][$status] ?? 'DESCONOCIDO';
            
            $statusColor = [
                'approved' => [16, 185, 129],
                'rejected' => [239, 68, 68],
                'approved_pending' => [251, 191, 36],
                'pending' => [148, 163, 184]
            ][$status] ?? [148, 163, 184];
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 6, 'Estado:', 0, 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetTextColor($statusColor[0], $statusColor[1], $statusColor[2]);
            $pdf->Cell(0, 6, $statusText, 0, 1);
            $pdf->SetTextColor(15, 23, 42);
            
            if (!empty($form['reviewed_at'])) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(50, 6, 'Fecha de Revision:', 0, 0);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($form['reviewed_at'])), 0, 1);
            }
            
            if (!empty($form['reviewed_by_name'])) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(50, 6, 'Revisado por:', 0, 0);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 6, $form['reviewed_by_name'], 0, 1);
            }
            
            $pdf->Ln(5);
            
            // Consultas realizadas
            $pdf->SetFillColor(241, 245, 249);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, 'CONSULTAS REALIZADAS', 0, 1, 'L', true);
            $pdf->Ln(2);
            
            $consultas = [
                'OFAC' => $form['consulta_ofac'] ?? 'N/A',
                'Listas Nacionales' => $form['consulta_listas_nacionales'] ?? 'N/A',
                'ONU' => $form['consulta_onu'] ?? 'N/A',
                'INTERPOL' => $form['consulta_interpol'] ?? 'N/A'
            ];
            
            $pdf->SetFont('Arial', '', 10);
            foreach ($consultas as $nombre => $resultado) {
                $pdf->Cell(60, 6, $nombre . ':', 0, 0);
                $color = strtolower($resultado) === 'negativa' ? [16, 185, 129] : [239, 68, 68];
                $pdf->SetTextColor($color[0], $color[1], $color[2]);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 6, strtoupper($resultado), 0, 1);
                $pdf->SetTextColor(15, 23, 42);
                $pdf->SetFont('Arial', '', 10);
            }
            
            // Observaciones
            if (!empty($form['observations'])) {
                $pdf->Ln(5);
                $pdf->SetFillColor(241, 245, 249);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 8, 'OBSERVACIONES', 0, 1, 'L', true);
                $pdf->Ln(2);
                
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(0, 5, $form['observations']);
            }
            
            // Pie de página
            $pdf->SetY(-30);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetTextColor(148, 163, 184);
            $pdf->Cell(0, 5, 'Documento generado automaticamente por el Sistema SAGRILAFT', 0, 1, 'C');
            $pdf->Cell(0, 5, 'Pollo Fiesta S.A. - NIT 860.032.450-9', 0, 1, 'C');
            $pdf->Cell(0, 5, date('d/m/Y H:i:s'), 0, 1, 'C');
            
            return $pdf->Output('S');
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate reviewer page', [
                'form_id' => $form['id'] ?? null,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
