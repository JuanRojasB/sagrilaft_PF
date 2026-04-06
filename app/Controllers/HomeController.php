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
                header('Location: /gestion-sagrilaft/public/');
                exit;
            }

            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email inválido';
                header('Location: /gestion-sagrilaft/public/');
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
                    $userType,
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
                    $userType,
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
                'user_type' => $userType,
                'role' => $userType, // Alias para compatibilidad
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
                'user_id' => $userId
            ]);

            // Redirigir al formulario
            header('Location: /gestion-sagrilaft/public/form/create');
            exit;

        } catch (\Exception $e) {
            $this->logger->error('Error en registro inicial', [
                'error' => $e->getMessage()
            ]);
            
            $_SESSION['error'] = 'Error al procesar el registro. Intenta nuevamente.';
            header('Location: /gestion-sagrilaft/public/');
            exit;
        }
    }
}
