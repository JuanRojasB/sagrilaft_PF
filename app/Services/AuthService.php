<?php

namespace App\Services;

use App\Models\User;
ttvvrrttvvrrbloodhoneyandbloodpsychosocial
/**
 * Servicio de Autenticación
 * 
 * Gestiona la autenticación y autorización de usuarios.
 * Genera tokens JWT, valida credenciales y maneja sesiones.
 * 
 * Funciones principales:
 * - login(): Autenticar usuario y crear sesión
 * - logout(): Cerrar sesión
 * - check(): Verificar si usuario está autenticado
 * - user(): Obtener datos del usuario actual
 * 
 * Roles soportados:
 * - admin: Administrador con acceso completo
 * - revisor: Revisor de formularios
 * - cliente: Usuario regular
 * - proveedor: Usuario proveedor
 * 
 * @package App\Services
 */
class AuthService
{
    private JwtService $jwtService;
    private Logger $logger;

    public function __construct()
    {
        $this->jwtService = new JwtService();
        $this->logger = new Logger();
    }

    /**
     * Autenticar usuario
     * 
     * Valida credenciales del usuario y crea sesión si son correctas.
     * Genera token JWT y guarda información en sesión.
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @param string|null $userType No se usa, se mantiene por compatibilidad
     * @return array|null Datos del usuario con token o null si falla
     */
    public function login(string $email, string $password, ?string $userType = null): ?array
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->logger->warning('Failed login attempt', ['email' => $email]);
            return null;
        }

        // Generate JWT token
        $token = $this->jwtService->generate([
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        // Store in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['token'] = $token;

        // For backwards compatibility, set user_type based on role
        if ($user['role'] === 'cliente' || $user['role'] === 'proveedor') {
            $_SESSION['user_type'] = $user['role'];
        } else {
            $_SESSION['user_type'] = 'cliente'; // Default for admin/revisor
        }

        // For reviewers, also save in reviewer session
        if ($user['role'] === 'revisor') {
            $_SESSION['reviewer_id'] = $user['id'];
            $_SESSION['reviewer_name'] = $user['name'];
            $_SESSION['reviewer_email'] = $user['email'];
        }

        $this->logger->info('User logged in', ['user_id' => $user['id'], 'role' => $user['role']]);

        return [
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role']
            ],
            'token' => $token
        ];
    }

    /**
     * Cerrar sesión del usuario
     * 
     * Destruye la sesión actual y registra el evento en logs.
     */
    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        session_destroy();
        session_start();
        
        if ($userId) {
            $this->logger->info('User logged out', ['user_id' => $userId]);
        }
    }

    /**
     * Verificar si el usuario está autenticado
     * 
     * @return bool true si hay sesión activa (usuario normal o revisor)
     */
    public function check(): bool
    {
        return isset($_SESSION['user_id']) || isset($_SESSION['reviewer_id']);
    }

    /**
     * Obtener datos del usuario actual
     * 
     * @return array|null Datos del usuario o null si no está autenticado
     */
    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
}
