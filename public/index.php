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
// Detectar si es una petición AJAX o a endpoints JSON
$uri = $_SERVER['REQUEST_URI'] ?? '';
$isAjaxRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                 strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$isJsonEndpoint = (strpos($uri, '/excel/') !== false || 
                   strpos($uri, '/api/') !== false ||
                   strpos($uri, '.json') !== false ||
                   $isAjaxRequest);

// En desarrollo: mostrar todos los errores (excepto en endpoints JSON)
// En producción: ocultar errores
if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
    if ($isJsonEndpoint) {
        // Para endpoints JSON/AJAX, no mostrar errores HTML
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
    } else {
        // Para páginas normales, mostrar errores
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    }
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ============================================================================
// INICIALIZAR Y EJECUTAR LA APLICACIÓN
// ============================================================================
$app = new App\Core\App();
$app->run();
