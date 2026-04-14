<?php

/**
 * SAGRILAFT System - Punto de Entrada
 * 
 * Este es el archivo principal que recibe todas las peticiones HTTP.
 * 
 * Funciones:
 * 1. Autoloader manual PSR-4 (carga clases automáticamente)
 * 2. Carga variables de entorno desde .env
 * 3. Inicia la sesión PHP
 * 4. Configura el manejo de errores según el entorno
 * 5. Inicializa y ejecuta la aplicación
 * 
 * @package SAGRILAFT
 * @author  Sistema SAGRILAFT
 * @version 1.0.0
 */

declare(strict_types=1);

// Iniciar output buffering para capturar cualquier salida no deseada
ob_start();

// ============================================================================
// AUTOLOADER MANUAL (PSR-4)
// ============================================================================
// Carga automáticamente las clases cuando se necesitan
// Convierte App\Controllers\FormController -> app/Controllers/FormController.php
spl_autoload_register(function ($class) {
    $prefix = 'App\\'; // Namespace base
    $base_dir = __DIR__ . '/../app/'; // Directorio base
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // No es una clase de App\
    }
    
    // Obtener la ruta relativa de la clase
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Cargar el archivo si existe
    if (file_exists($file)) {
        require $file;
    }
});

// ============================================================================
// CARGAR VARIABLES DE ENTORNO (.env)
// ============================================================================
// Lee el archivo .env y carga las variables en $_ENV
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$name, $value] = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value);
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}

// ============================================================================
// INICIAR SESIÓN
// ============================================================================
session_start();

// ============================================================================
// CONFIGURAR MANEJO DE ERRORES
// ============================================================================
// Siempre mostrar errores para diagnosticar
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../storage/logs/php-errors.log');

// ============================================================================
// INICIALIZAR Y EJECUTAR LA APLICACIÓN
// ============================================================================

try {
    // Manejar rutas sin mod_rewrite (compatibilidad con WordPress)
    if (isset($_GET['route'])) {
        // Si hay parámetro route, usarlo como URI
        $_SERVER['REQUEST_URI'] = $_GET['route'];
    } else {
        // Si no hay parámetro route, limpiar la URI actual
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remover query string
        $uri = strtok($uri, '?');
        
        // Remover /gestion-sagrilaft/public/ de la URI si existe
        $uri = preg_replace('#^/gestion-sagrilaft/public/#', '/', $uri);
        
        // Remover /index.php de la URI si existe
        $uri = preg_replace('#^/index\.php#', '/', $uri);
        
        // Si la URI está vacía, usar /
        if (empty($uri) || $uri === '') {
            $uri = '/';
        }
        
        $_SERVER['REQUEST_URI'] = $uri;
    }

    $app = new App\Core\App();
    $app->run();
    
} catch (\Throwable $e) {
    // Capturar cualquier error fatal
    http_response_code(500);
    echo '<h1>Error del Sistema</h1>';
    echo '<pre>';
    echo 'Mensaje: ' . $e->getMessage() . "\n";
    echo 'Archivo: ' . $e->getFile() . "\n";
    echo 'Línea: ' . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString();
    echo '</pre>';
    
    // Registrar en archivo
    $logFile = __DIR__ . '/../storage/logs/fatal-error.log';
    $logEntry = date('[Y-m-d H:i:s] ') . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
