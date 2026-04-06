<?php

/**
 * Configuración de la Aplicación
 * 
 * Define configuraciones generales de la aplicación como:
 * - Nombre de la aplicación
 * - Entorno (development/production)
 * - URL base
 * - Zona horaria
 * - Idioma
 * 
 * Los valores se obtienen del archivo .env
 * 
 * @package SAGRILAFT
 */

return [
    'name' => $_ENV['APP_NAME'] ?? 'SAGRILAFT', // Nombre de la aplicación
    'env' => $_ENV['APP_ENV'] ?? 'production', // Entorno: development o production
    'url' => $_ENV['APP_URL'] ?? 'http://localhost', // URL base de la aplicación
    'timezone' => 'America/Bogota', // Zona horaria de Colombia
    'locale' => 'es', // Idioma español
];
