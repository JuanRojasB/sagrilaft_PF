<?php

namespace App\Middlewares;

/**
 * Middleware de Protección CSRF
 * 
 * Protege contra ataques Cross-Site Request Forgery (CSRF).
 * Genera y valida tokens únicos para cada sesión.
 * 
 * Funcionamiento:
 * - Genera token único al inicio de sesión
 * - Valida token en peticiones POST
 * - Rechaza peticiones sin token válido
 * 
 * Uso:
 * - Incluir token en formularios: <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
 * - El middleware valida automáticamente en peticiones POST
 * 
 * @package App\Middlewares
 */
class CsrfMiddleware
{
    /**
     * Generar token CSRF
     * 
     * Crea un token único de 64 caracteres si no existe.
     * 
     * @return string Token CSRF
     */
    public static function generateToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validar token CSRF
     * 
     * Compara el token recibido con el almacenado en sesión.
     * Usa hash_equals para prevenir ataques de timing.
     * 
     * @param string $token Token a validar
     * @return bool true si el token es válido
     */
    public static function validateToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Manejar validación CSRF
     * 
     * Valida automáticamente el token en peticiones POST.
     * Rechaza la petición si el token es inválido.
     * 
     * @return bool true si la validación pasa
     */
    public static function handle(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!self::validateToken($token)) {
                http_response_code(403);
                echo json_encode(['error' => 'Token CSRF inválido']);
                exit;
            }
        }
        
        return true;
    }
}
