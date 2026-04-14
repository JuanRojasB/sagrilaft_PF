<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Services\Logger;

/**
 * Controlador de Página Principal
 * 
 * Maneja el registro simplificado sin login para clientes/proveedores/transportistas
 */
class HomeController extends Controller
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Mostrar formulario de registro inicial
     */
    public function index(): void
    {
        // Cargar asesores comerciales para el selector
        $asesores = [];
        try {
            require_once __DIR__ . '/../Models/AsesorComercial.php';
            $asesorModel = new \App\Models\AsesorComercial();
            $asesores = $asesorModel->getAllGroupedBySede();
        } catch (\Exception $e) {
            $this->logger->error('Error al cargar asesores comerciales', [
                'error' => $e->getMessage()
            ]);
            // Continuar sin asesores si hay error
            $asesores = [];
        }
        
        $this->view('home/index', [
            'csrf_token' => $this->generateCsrfToken(),
            'asesores' => $asesores
        ]);
    }

    /**
     * Procesar registro y redirigir al formulario
     */
    public function register(): void
    {
        try {
            // Validar datos
            $userType = $_POST['user_type'] ?? '';
            
            // Si es empleado, manejar de forma diferente
            if ($userType === 'empleado') {
                $this->registerEmpleado();
                return;
            }
            
            // Si es "otros", usar la categoría seleccionada para determinar el formulario
            $effectiveUserType = $userType;
            if ($userType === 'otros') {
                $otrosCategory = $_POST['otros_category'] ?? '';
                if (empty($otrosCategory)) {
                    $_SESSION['error'] = 'Debe seleccionar una categoría para "Otros"';
                    header('Location: index.php');
                    exit;
                }
                // Usar la categoría como tipo efectivo para el formulario
                $effectiveUserType = $otrosCategory;
            }
            
            // Flujo normal para otros tipos de usuario
            $personType = $_POST['person_type'] ?? '';
            $companyName = $_POST['company_name'] ?? '';
            $documentType = $_POST['document_type'] ?? '';
            $documentNumber = $_POST['document_number'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $asesorComercialId = $_POST['asesor_comercial_id'] ?? null;

            if (empty($userType) || empty($personType) || empty($companyName) || 
                empty($documentType) || empty($documentNumber) || empty($email) || empty($phone)) {
                $_SESSION['error'] = 'Todos los campos son obligatorios';
                header('Location: index.php');
                exit;
            }

            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email inválido';
                header('Location: index.php');
                exit;
            }

            // Buscar o crear usuario
            $conn = Database::getConnection();
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                $userId = $existingUser['id'];
                
                // Actualizar datos del usuario existente
                $stmt = $conn->prepare("
                    UPDATE users SET 
                        name = ?,
                        company_name = ?,
                        nit = ?,
                        phone = ?,
                        role = ?,
                        document_type = ?,
                        document_number = ?,
                        person_type = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([
                    $companyName,
                    $companyName,
                    $documentNumber,
                    $phone,
                    $userType, // Guardar el tipo original (otros)
                    $documentType,
                    $documentNumber,
                    $personType,
                    $userId
                ]);
            } else {
                // Crear nuevo usuario (sin contraseña real - generamos una aleatoria)
                $randomPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("
                    INSERT INTO users (
                        name, email, password, phone, company_name, nit, role, 
                        document_type, document_number, person_type,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                
                $success = $stmt->execute([
                    $companyName,
                    $email,
                    $randomPassword,
                    $phone,
                    $companyName,
                    $documentNumber,
                    $userType, // Guardar el tipo original (otros)
                    $documentType,
                    $documentNumber,
                    $personType
                ]);
                
                if (!$success) {
                    throw new \Exception('Error al crear usuario en la base de datos');
                }
                
                $userId = $conn->lastInsertId();
                
                if (!$userId) {
                    throw new \Exception('No se pudo obtener el ID del usuario creado');
                }
            }

            // Guardar datos en sesión temporal para pre-llenar el formulario
            $_SESSION['temp_user_data'] = [
                'user_id' => $userId,
                'user_type' => $effectiveUserType, // Usar el tipo efectivo para el formulario
                'role' => $effectiveUserType, // Alias para compatibilidad
                'person_type' => $personType,
                'ubicacion' => $_POST['ubicacion'] ?? 'nacional',
                'company_name' => $companyName,
                'document_type' => $documentType,
                'document_number' => $documentNumber,
                'email' => $email,
                'phone' => $phone,
                'asesor_comercial_id' => $asesorComercialId
            ];

            $this->logger->info('Usuario registrado para formulario', [
                'email' => $email,
                'user_id' => $userId,
                'original_type' => $userType,
                'effective_type' => $effectiveUserType
            ]);

            // Redirigir al formulario
            header('Location: index.php?route=/form/create');
            exit;

        } catch (\Exception $e) {
            $this->logger->error('Error en registro inicial', [
                'error' => $e->getMessage()
            ]);
            
            die('<h2>ERROR DE BD:</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
        }
    }
    
    /**
     * Registrar empleado directamente (sin pasar por formulario completo)
     */
    private function registerEmpleado(): void
    {
        try {
            // Debug: ver qué se está recibiendo
            error_log("POST data: " . print_r($_POST, true));
            error_log("FILES data: " . print_r($_FILES, true));
            
            // Validar datos de empleado
            $empleadoNombre = $_POST['empleado_nombre'] ?? '';
            $empleadoCedula = $_POST['empleado_cedula'] ?? '';
            $empleadoCargo = $_POST['empleado_cargo'] ?? '';
            $empleadoCiudadVacante = $_POST['empleado_ciudad_vacante'] ?? '';
            $empleadoCiudadNacimiento = $_POST['empleado_ciudad_nacimiento'] ?? '';
            $empleadoFechaNacimiento = $_POST['empleado_fecha_nacimiento'] ?? '';
            $empleadoCelular = $_POST['empleado_celular'] ?? '';
            
            if (empty($empleadoNombre) || empty($empleadoCedula) || empty($empleadoCargo) || 
                empty($empleadoCiudadVacante) || empty($empleadoCiudadNacimiento) || 
                empty($empleadoFechaNacimiento) || empty($empleadoCelular)) {
                $_SESSION['error'] = 'Todos los campos del empleado son obligatorios';
                header('Location: index.php');
                exit;
            }
            
            // Crear usuario temporal para el empleado
            $conn = Database::getConnection();
            error_log("Conexión DB: " . $conn->getAttribute(\PDO::ATTR_CONNECTION_STATUS));
            
            $randomPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $email = 'empleado_' . $empleadoCedula . '_' . time() . '@temp.local'; // Email temporal único
            
            $stmt = $conn->prepare("
                INSERT INTO users (
                    name, email, password, role, created_at
                ) VALUES (?, ?, ?, 'empleado', NOW())
            ");
            
            $stmt->execute([
                $empleadoNombre,
                $email,
                $randomPassword
            ]);
            
            $userId = $conn->lastInsertId();
            
            // Generar token de aprobación
            $approvalToken = bin2hex(random_bytes(32));
            
            // Crear formulario de empleado (sin campos de empleado en forms)
            $formModel = new \App\Models\Form();
            error_log("Modelo Form usando conexión: " . get_class($formModel));
            
            $formData = [
                'user_id' => $userId,
                'title' => 'Registro de Empleado - ' . $empleadoNombre,
                'content' => 'Empleado',
                'form_type' => 'empleado',
                'status' => 'submitted',
                'approval_status' => 'pending',
                'approval_token' => $approvalToken
            ];
            
            $formId = $formModel->create($formData);
            
            // Debug: verificar el ID del formulario creado
            error_log("Formulario creado con ID: " . $formId);
            error_log("User ID: " . $userId);
            error_log("Approval Token: " . $approvalToken);
            
            // Guardar datos de empleado en tabla separada
            $stmt = $conn->prepare("
                INSERT INTO form_empleados (
                    form_id, empleado_nombre, empleado_cedula, empleado_cargo, 
                    empleado_ciudad_vacante, empleado_ciudad_nacimiento, 
                    empleado_fecha_nacimiento, empleado_celular
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $formId,
                $empleadoNombre,
                $empleadoCedula,
                $empleadoCargo,
                $empleadoCiudadVacante,
                $empleadoCiudadNacimiento,
                $empleadoFechaNacimiento,
                $empleadoCelular
            ]);
            
            // Manejar archivo PDF si se adjuntó
            $uploadedFiles = [];
            if (isset($_FILES['cedula_pdf']) && $_FILES['cedula_pdf']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = 'cedula_' . $empleadoCedula . '_' . time() . '.pdf';
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['cedula_pdf']['tmp_name'], $filePath)) {
                    // Leer el contenido del archivo para guardarlo en la BD como BLOB
                    $fileData = file_get_contents($filePath);
                    
                    // Guardar referencia del archivo en la base de datos
                    $stmt = $conn->prepare("
                        INSERT INTO form_attachments (
                            form_id, filename, filepath, file_data, filesize, mime_type, uploaded_at
                        ) VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ");
                    
                    $stmt->execute([
                        $formId,
                        $_FILES['cedula_pdf']['name'],  // Nombre original del archivo
                        $fileName,  // Nombre único en el servidor
                        $fileData,  // Contenido binario del archivo (BLOB) - máximo 2MB
                        $_FILES['cedula_pdf']['size'],
                        $_FILES['cedula_pdf']['type']
                    ]);
                    
                    error_log("PDF guardado con form_id: " . $formId . ", attachment_id: " . $conn->lastInsertId());
                    
                    $uploadedFiles[] = [
                        'filename' => $_FILES['cedula_pdf']['name'],
                        'size' => $_FILES['cedula_pdf']['size']
                    ];
                    
                    error_log("PDF guardado: form_id=$formId, filename=$fileName");
                } else {
                    error_log("Error al mover archivo PDF");
                }
            } else {
                error_log("No se recibió archivo PDF o hubo error: " . ($_FILES['cedula_pdf']['error'] ?? 'no isset'));
            }
            
            // Mantener el tipo de usuario en sesión para el próximo registro
            $_SESSION['last_user_type'] = 'empleado';
            
            // Redirigir inmediatamente con mensaje de éxito
            $_SESSION['success'] = 'Registro de empleado enviado correctamente.';
            header('Location: index.php');
            
            // Enviar notificación después de la redirección (en segundo plano)
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }
            
            try {
                $empleadoData = [
                    'empleado_nombre' => $empleadoNombre,
                    'empleado_cedula' => $empleadoCedula,
                    'empleado_cargo' => $empleadoCargo,
                    'empleado_fecha_nacimiento' => $empleadoFechaNacimiento
                ];
                $this->sendEmpleadoNotification($formId, $empleadoData, $uploadedFiles);
            } catch (\Exception $e) {
                error_log('Error enviando notificación: ' . $e->getMessage());
            }
            
            exit;

        } catch (\Exception $e) {
            error_log('Error en registerEmpleado: ' . $e->getMessage());
            $_SESSION['error'] = 'Error al registrar empleado: ' . $e->getMessage();
            header('Location: index.php');
            exit;
        }
    }

    /**
     * Enviar notificación de empleado a Angie
     */
    private function sendEmpleadoNotification(int $formId, array $data, array $uploadedFiles = []): void
    {
        try {
            $mailService = new \App\Services\MailService();
            
            // Generate unique approval token
            $approvalToken = bin2hex(random_bytes(32));
            
            // Save token to database
            $formModel = new \App\Models\Form();
            $formModel->updateApprovalToken($formId, $approvalToken);
            
            // Obtener configuración de revisor para empleados
            $reviewer = \App\Config\NotificationConfig::getReviewerByFormType('empleado');
            
            $to = $reviewer['email'];
            $subject = 'SAGRILAFT - Nuevo Registro de Empleado para Aprobar';
            
            $approvalUrl = $_ENV['APP_URL'] . "/index.php?route=/approval/{$approvalToken}";
            
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
            
            $body = \App\Helpers\EmailHelper::emailHeader('Nuevo Registro de Empleado #' . $formId) . "
            <p class='msg'>Se ha recibido un nuevo registro de empleado que requiere revisión y aprobación.</p>
            <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                <tr>
                    <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
                        <div class='info-item'><span class='info-label'>Nombre Completo</span><span class='info-value'>" . ($data['empleado_nombre'] ?? 'N/A') . "</span></div>
                    </td>
                    <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
                        <div class='info-item'><span class='info-label'>Cédula</span><span class='info-value'>" . ($data['empleado_cedula'] ?? 'N/A') . "</span></div>
                    </td>
                </tr>
                <tr>
                    <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
                        <div class='info-item'><span class='info-label'>Cargo</span><span class='info-value'>" . ($data['empleado_cargo'] ?? 'N/A') . "</span></div>
                    </td>
                    <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
                        <div class='info-item'><span class='info-label'>Fecha de Nacimiento</span><span class='info-value'>" . ($data['empleado_fecha_nacimiento'] ?? 'N/A') . "</span></div>
                    </td>
                </tr>
            </table>
            {$attachmentsHtml}
            <div style='text-align:center;margin-top:24px;'>
                <a href='{$approvalUrl}' class='btn' style='display:inline-block;padding:10px 22px;background:#1d4ed8;color:#ffffff;text-decoration:none;border-radius:5px;font-weight:600;font-size:13px;'>Revisar Registro</a>
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
            error_log('Email notification failed: ' . $e->getMessage());
        }
    }
}
