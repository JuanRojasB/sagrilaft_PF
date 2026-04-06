<?php

namespace App\Core;

use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;

/**
 * Application Router
 * 
 * Maneja el enrutamiento de la aplicación, registra rutas GET/POST,
 * ejecuta middlewares y despacha las peticiones a los controladores.
 * 
 * @package App\Core
 */
class Router
{
    private array $routes = [];

    /**
     * Registra una ruta GET
     * 
     * @param string $path Ruta (ej: '/forms', '/forms/{id}')
     * @param string $handler Controlador@método (ej: 'FormController@index')
     * @param array $middlewares Middlewares a ejecutar (ej: ['auth', 'role:admin'])
     */
    public function get(string $path, string $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    /**
     * Registra una ruta POST
     * 
     * @param string $path Ruta (ej: '/forms', '/login')
     * @param string $handler Controlador@método
     * @param array $middlewares Middlewares a ejecutar
     */
    public function post(string $path, string $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    /**
     * Agrega una ruta al registro interno
     * 
     * @param string $method Método HTTP (GET, POST)
     * @param string $path Ruta
     * @param string $handler Controlador@método
     * @param array $middlewares Middlewares
     */
    private function addRoute(string $method, string $path, string $handler, array $middlewares): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Despacha la petición actual al controlador apropiado
     * 
     * 1. Obtiene el método HTTP y URI
     * 2. Busca una ruta que coincida
     * 3. Ejecuta los middlewares
     * 4. Ejecuta el controlador
     * 5. Si no encuentra ruta, devuelve 404
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover el path base si está presente (para subdirectorios)
        $appUrl = $_ENV['APP_URL'] ?? '';
        $basePath = rtrim(parse_url($appUrl, PHP_URL_PATH) ?? '', '/');
        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Asegurar que URI empiece con /
        if (empty($uri) || $uri === '') {
            $uri = '/';
        }
        
        // Buscar ruta que coincida
        foreach ($this->routes as $route) {
            $pattern = $this->convertToRegex($route['path']);
            
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remover el match completo
                
                // Ejecutar middlewares (auth, role, etc)
                if (!$this->executeMiddlewares($route['middlewares'])) {
                    return; // Middleware bloqueó la petición
                }
                
                // Ejecutar el controlador
                $this->executeController($route['handler'], $matches);
                return;
            }
        }
        
        // No se encontró ninguna ruta
        http_response_code(404);
        echo '404 - Página no encontrada: ' . $uri;
    }

    /**
     * Convierte una ruta con parámetros a expresión regular
     * 
     * Ejemplo: '/forms/{id}' -> '#^/forms/([a-zA-Z0-9_-]+)$#'
     * 
     * @param string $path Ruta con parámetros {param}
     * @return string Expresión regular
     */
    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Ejecuta los middlewares de una ruta
     * 
     * Middlewares disponibles:
     * - 'auth': Verifica que el usuario esté autenticado
     * - 'role:admin': Verifica que el usuario tenga el rol especificado
     * 
     * @param array $middlewares Lista de middlewares
     * @return bool True si todos los middlewares pasaron, False si alguno bloqueó
     */
    private function executeMiddlewares(array $middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            // Middleware de autenticación
            if ($middleware === 'auth') {
                if (!AuthMiddleware::handle()) {
                    return false; // Usuario no autenticado
                }
            } 
            // Middleware de rol (ej: 'role:admin')
            elseif (str_starts_with($middleware, 'role:')) {
                $role = substr($middleware, 5); // Extraer el rol
                if (!RoleMiddleware::handle($role)) {
                    return false; // Usuario no tiene el rol requerido
                }
            }
        }
        return true; // Todos los middlewares pasaron
    }

    /**
     * Ejecuta el método del controlador
     * 
     * @param string $handler Formato 'ControllerName@methodName'
     * @param array $params Parámetros extraídos de la URL
     * @throws \Exception Si el controlador o método no existe
     */
    private function executeController(string $handler, array $params): void
    {
        // Separar controlador y método (ej: 'FormController@index')
        [$controllerName, $method] = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        // Verificar que el controlador existe
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} not found");
        }
        
        // Instanciar el controlador
        $controller = new $controllerClass();
        
        // Verificar que el método existe
        if (!method_exists($controller, $method)) {
            throw new \Exception("Method {$method} not found in {$controllerClass}");
        }
        
        // Ejecutar el método con los parámetros
        call_user_func_array([$controller, $method], $params);
    }
}
