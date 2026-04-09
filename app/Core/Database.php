<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Connection Manager
 * 
 * Maneja la conexión a la base de datos MySQL usando PDO.
 * Implementa el patrón Singleton para reutilizar la misma conexión.
 * 
 * Configuración desde variables de entorno (.env):
 * - DB_HOST: Host de MySQL (ej: localhost)
 * - DB_NAME: Nombre de la base de datos (ej: sagrilaft)
 * - DB_USER: Usuario de MySQL
 * - DB_PASS: Contraseña de MySQL
 * 
 * @package App\Core
 */
class Database
{
    private static ?PDO $connection = null;

    /**
     * Obtiene la conexión a la base de datos (Patrón Singleton)
     * 
     * Crea una única instancia de PDO que se reutiliza en toda la aplicación.
     * Configuración:
     * - Modo de error: Excepciones
     * - Fetch mode: Array asociativo
     * - Prepared statements reales (no emulados)
     * - Conexión no persistente
     * 
     * @return PDO Instancia de PDO conectada
     * @throws PDOException Si falla la conexión
     */
    public static function getConnection(): PDO
    {
        if (!self::$connection) {
            // Fallback: si $_ENV no tiene las variables, intentar getenv() o valores por defecto
            $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
            $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'sagrilaft';
            $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root';
            $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

            $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
            
            self::$connection = new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false
                ]
            );
        }
        
        return self::$connection;
    }

    /**
     * Cierra la conexión a la base de datos
     * 
     * Libera la conexión PDO. Útil al final de scripts largos.
     */
    public static function closeConnection(): void
    {
        self::$connection = null;
    }
}
