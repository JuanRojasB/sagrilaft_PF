<?php

namespace App\Middlewares;

/**
 * Middleware de Autorización por Rol
 * 
 * Verifica que el usuario tenga el rol requerido para acceder a una ruta.
 * 
 * Roles del sistema:
 * - admin: Acceso completo al sistema
 * - revisor: Puede aprobar/rechazar formularios
 * - cliente: Usuario regular
 * - proveedor: Usuario proveedor
 * 
 * Uso:
 * - Se ejecuta después del AuthMiddleware
 * - Verifica el rol del usuario en sesión
 * - Rechaza acceso si el rol no coincide
 * 
 * @package App\Middlewares
 */
class RoleMiddleware
{
    /**
     * Verificar rol del usuario
     * 
     * Comprueba que el usuario tenga el rol requerido.
     * Si no, devuelve error 403 (Acceso denegado).
     * 
     * @param string $requiredRole Rol requerido (admin, revisor, cliente, proveedor)
     * @return bool true si el usuario tiene el rol
     */
    public static function handle(string $requiredRole): bool
    {
        $userRole = $_SESSION['user_role'] ?? null;
        
        if ($userRole !== $requiredRole) {
            http_response_code(403);
            
            if (self::isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Acceso denegado']);
            } else {
                echo 'Acceso denegado';
            }
            
            exit;
        }
        
        return true;
    }

    /**
     * Verificar si la petición es AJAX
     * 
     * @return bool true si es petición AJAX
     */
    private static function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
