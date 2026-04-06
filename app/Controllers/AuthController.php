<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

/**
 * Controlador de Autenticación
 * 
 * Gestiona el inicio y cierre de sesión de usuarios.
 * Valida credenciales, genera tokens JWT y maneja sesiones.
 * 
 * Funciones principales:
 * - showLogin(): Muestra formulario de login
 * - login(): Procesa credenciales y crea sesión
 * - logout(): Cierra sesión y limpia cookies
 * 
 * @package App\Controllers
 */
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Mostrar formulario de login
     * 
     * Si el usuario ya está autenticado, redirige según su rol:
     * - Revisor: Dashboard de revisor
     * - Otros: Lista de formularios
     */
    public function showLogin(): void
    {
        if ($this->authService->check()) {
            // Redirigir según el rol
            $role = $_SESSION['user_role'] ?? 'user';
            if ($role === 'revisor') {
                $this->redirect('/gestion-sagrilaft/public/reviewer/dashboard');
            } else {
                $this->redirect('/gestion-sagrilaft/public/forms');
            }
        }
        
        $this->view('approval/login', [
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Procesar inicio de sesión
     * 
     * Valida credenciales del usuario y crea sesión.
     * Genera token JWT y lo guarda en cookie segura.
     * Redirige según el rol del usuario.
     * 
     * @return void Respuesta JSON con resultado
     */
    public function login(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $email = $this->input('email');
        $password = $this->input('password');

        if (!$email || !$password) {
            $this->json(['error' => 'Email y contraseña son requeridos'], 400);
        }

        // Login sin requerir tipo de usuario
        $result = $this->authService->login($email, $password, null);

        if (!$result) {
            $this->json(['error' => 'Credenciales inválidas'], 401);
        }

        // Set secure cookie with JWT token
        setcookie('token', $result['token'], [
            'expires' => time() + (int)$_ENV['JWT_EXPIRATION'],
            'path' => '/',
            'httponly' => true,
            'secure' => ($_ENV['APP_ENV'] === 'production'),
            'samesite' => 'Strict'
        ]);

        // Redirigir según el rol
        $redirect = '/gestion-sagrilaft/public/admin';
        if ($result['user']['role'] === 'revisor') {
            $redirect = '/gestion-sagrilaft/public/reviewer/dashboard';
        }

        $this->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'redirect' => $redirect,
            'data' => $result
        ]);
    }

    /**
     * Procesar cierre de sesión
     * 
     * Destruye la sesión del usuario y limpia cookies.
     * Redirige a la página de login.
     */
    public function logout(): void
    {
        $this->authService->logout();
        
        // Clear JWT cookie
        setcookie('token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => false,
            'samesite' => 'Strict'
        ]);
        
        header('Location: /gestion-sagrilaft/public/login');
        exit;
    }

    /**
     * Mostrar formulario de registro
     * 
     * Renderiza la vista de registro público para nuevos usuarios.
     */
    public function showRegister(): void
    {
        if ($this->authService->check()) {
            $this->redirect('/gestion-sagrilaft/public/forms');
        }
        
        $this->view('auth/register', [
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Procesar registro de nuevo usuario
     * 
     * Valida datos, crea usuario en la base de datos y envía email de bienvenida.
     * 
     * @return void Respuesta JSON con resultado
     */
    public function register(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        // Validar campos requeridos
        $name = $this->input('name');
        $email = $this->input('email');
        $password = $this->input('password');
        $role = $this->input('role');
        $document_type = $this->input('document_type');
        $document_number = $this->input('document_number');
        $person_type = $this->input('person_type', 'natural');

        if (!$name || !$email || !$password || !$role || !$document_type || !$document_number) {
            $this->json(['error' => 'Todos los campos obligatorios son requeridos'], 400);
        }

        // Validar longitud de contraseña
        if (strlen($password) < 6) {
            $this->json(['error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
        }

        // Validar rol permitido para registro público
        $allowedRoles = ['cliente', 'proveedor', 'transportista', 'otros'];
        if (!in_array($role, $allowedRoles)) {
            $this->json(['error' => 'Rol no válido'], 400);
        }

        $userModel = new \App\Models\User();
        
        // Verificar si el email ya existe
        if ($userModel->findByEmail($email)) {
            $this->json(['error' => 'El email ya está registrado'], 400);
        }

        // Verificar si el documento ya existe
        $existingDoc = $userModel->findByDocument($document_type, $document_number);
        if ($existingDoc) {
            $this->json(['error' => 'El documento ya está registrado'], 400);
        }

        try {
            // Crear usuario
            $userId = $userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => $role,
                'document_type' => $document_type,
                'document_number' => $document_number,
                'person_type' => $person_type,
                'phone' => $this->input('phone'),
                'address' => $this->input('address'),
                'city' => $this->input('city'),
                'company_name' => $this->input('company_name'),
                'logistics_status' => 'pending'
            ]);

            $logger = new \App\Services\Logger();
            $logger->info('User registered', [
                'user_id' => $userId,
                'email' => $email,
                'role' => $role
            ]);

            // Enviar email de bienvenida (opcional)
            try {
                $mailService = new \App\Services\MailService();
                $mailService->sendFromTemplate($email, 'Bienvenido a SAGRILAFT', 'welcome', [
                    'name' => $name,
                    'email' => $email
                ]);
            } catch (\Exception $e) {
                // No fallar el registro si el email falla
                $logger->warning('Welcome email failed', ['error' => $e->getMessage()]);
            }

            $this->json([
                'success' => true,
                'message' => 'Registro exitoso. Ya puedes iniciar sesión.'
            ]);
        } catch (\Exception $e) {
            $logger = new \App\Services\Logger();
            $logger->error('User registration failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al crear usuario'], 500);
        }
    }
}
