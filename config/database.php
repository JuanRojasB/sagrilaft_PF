<?php

/**
 * Configuración de Base de Datos
 * 
 * Define la configuración de conexión a MySQL:
 * - Driver: mysql
 * - Host, database, usuario, contraseña
 * - Charset y collation para UTF-8
 * - Opciones de PDO para seguridad
 * 
 * Los valores se obtienen del archivo .env
 * 
 * @package SAGRILAFT
 */

return [
    'driver' => 'mysql', // Driver de base de datos
    'host' => $_ENV['DB_HOST'] ?? 'localhost', // Host de MySQL
    'database' => $_ENV['DB_NAME'] ?? 'sagrilaft', // Nombre de la base de datos
    'username' => $_ENV['DB_USER'] ?? 'root', // Usuario de MySQL
    'password' => $_ENV['DB_PASS'] ?? '', // Contraseña de MySQL
    'charset' => 'utf8mb4', // Charset para soporte completo de UTF-8
    'collation' => 'utf8mb4_unicode_ci', // Collation para ordenamiento
    'prefix' => '', // Prefijo de tablas (vacío)
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devolver arrays asociativos
        PDO::ATTR_EMULATE_PREPARES => false, // Usar prepared statements reales
    ]
];
