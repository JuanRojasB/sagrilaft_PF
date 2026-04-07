<?php

namespace App\Core;

/**
 * Base Controller Class
 * 
 * Clase base para todos los controladores de la aplicación.
 * Proporciona métodos útiles para renderizar vistas, devolver JSON,
 * redirigir, obtener datos POST y manejar tokens CSRF.
 * 
 * @package App\Core
 */
abstract class Controller
{
    /**
     * Renderiza una vista con datos
     * 
     * @param string $view Nombre de la vista (ej: 'forms/index')
     * @param array $data Datos a pasar a la vista
     * @throws \Exception Si la vista no existe
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data); // Convierte array keys en variables
        $viewPath = __DIR__ . "/../Views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }
        
        require_once $viewPath;
    }

    /**
     * Devuelve una respuesta JSON
     * 
     * @param array $data Datos a devolver en JSON
     * @param int $statusCode Código HTTP (200, 400, 500, etc)
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        // Limpiar cualquier salida previa (errores, warnings, etc)
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirige a una URL
     * 
     * @param string $url URL de destino (puede ser relativa o absoluta)
     */
    protected function redirect(string $url): void
    {
        // Si la URL es relativa (empieza con /), agregar APP_URL
        if (strpos($url, '/') === 0 && strpos($url, 'http') !== 0) {
            $url = ($_ENV['APP_URL'] ?? '') . $url;
        }
        header("Location: {$url}");
        exit;
    }

    /**
     * Obtiene un valor de $_POST
     * 
     * @param string $key Nombre del campo
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor del campo o default
     */
    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Valida el token CSRF
     * 
     * Compara el token enviado en el formulario con el de la sesión
     * para prevenir ataques CSRF.
     * 
     * @return bool True si el token es válido
     */
    protected function validateCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Genera un token CSRF
     * 
     * Crea un token único por sesión para proteger formularios.
     * 
     * @return string Token CSRF
     */
    protected function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
