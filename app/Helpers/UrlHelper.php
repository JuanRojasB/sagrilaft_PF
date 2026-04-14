<?php

namespace App\Helpers;

/**
 * Helper para generar URLs compatibles con WordPress
 */
class UrlHelper
{
    /**
     * Genera una URL para la aplicación
     * 
     * @param string $route Ruta (ej: '/login', '/form/create')
     * @return string URL completa
     */
    public static function route(string $route): string
    {
        // Asegurar que la ruta empiece con /
        if (!str_starts_with($route, '/')) {
            $route = '/' . $route;
        }
        
        // Retornar URL con parámetro route
        return 'index.php?route=' . $route;
    }
    
    /**
     * Genera una URL completa con dominio
     * 
     * @param string $route Ruta (ej: '/login')
     * @return string URL completa con dominio
     */
    public static function full(string $route): string
    {
        $baseUrl = $_ENV['APP_URL'] ?? 'https://pollo-fiesta.com/gestion-sagrilaft/public';
        
        // Remover /index.php del final si existe
        $baseUrl = rtrim($baseUrl, '/');
        $baseUrl = preg_replace('#/index\.php$#', '', $baseUrl);
        
        return $baseUrl . '/' . self::route($route);
    }
}
