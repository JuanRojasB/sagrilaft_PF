<?php

/**
 * Configuración de JWT (JSON Web Tokens)
 * 
 * Define la configuración para tokens de autenticación:
 * - Secret: Clave secreta para firmar tokens (debe ser única y segura)
 * - Expiration: Tiempo de vida del token en segundos (3600 = 1 hora)
 * - Algorithm: Algoritmo de firma (HS256)
 * 
 * Los valores se obtienen del archivo .env
 * 
 * @package SAGRILAFT
 */

return [
    'secret' => $_ENV['JWT_SECRET'] ?? 'change-this-secret-key', // Clave secreta (CAMBIAR en producción)
    'expiration' => (int)($_ENV['JWT_EXPIRATION'] ?? 3600), // Tiempo de vida en segundos (1 hora)
    'algorithm' => 'HS256', // Algoritmo de firma HMAC SHA-256
];
