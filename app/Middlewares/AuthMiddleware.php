<?php

namespace App\Middlewares;

use App\Services\AuthService;

/**
 * Middleware de Autenticación
 * 
 * Verifica que el usuario esté autenticado antes de acceder a rutas protegidas.
 * Si no está autenticado, redirige al login o devuelve error JSON.
 * 
 * Uso:
 * - Se ejecuta antes de controladores que requieren autenticación
 * - Verifica la existencia de sesión activa
 * - Maneja respuestas AJAX y navegación normal
 * 
 * @package App\Middlewares
 */
class AuthMiddleware
{
    /**
     * Verificar autenticación
     * 
     * Comprueba si hay sesión activa. Si no, redirige o devuelve error.
     * 
     * @return bool true si está autenticado
     */
    public static function handle(): bool
    {
        $authService = new AuthService();
        
        if (!$authService->check()) {
            // Limpiar output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
            http_response_code(401);
            
            if (self::isJsonRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'No autenticado']);
                exit;
            } else {
                header('Location: /login');
                exit;
            }
        }
        
        return true;
    }

    /**
     * Verificar si la petición espera JSON
     * 
     * @return bool true si es petición AJAX o espera JSON
     */
    private static function isJsonRequest(): bool
    {
        // Verificar si es AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        // Verificar si la URL contiene /api/ o endpoints conocidos de JSON
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/api/') !== false || 
            strpos($uri, '/excel/') !== false ||
            strpos($uri, '.json') !== false) {
            return true;
        }
        
        // Verificar Accept header
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }
        
        return false;
    }
}
