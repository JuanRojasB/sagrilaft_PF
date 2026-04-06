<?php

namespace App\Services;

/**
 * Servicio de Registro de Logs
 * 
 * Registra eventos y errores del sistema en archivos de log.
 * Crea un archivo por día en formato: app-YYYY-MM-DD.log
 * 
 * Niveles de log:
 * - INFO: Eventos informativos (login, creación de recursos)
 * - WARNING: Advertencias (intentos fallidos, validaciones)
 * - ERROR: Errores del sistema (excepciones, fallos)
 * 
 * Ubicación: storage/logs/
 * 
 * @package App\Services
 */
class Logger
{
    private string $logPath;

    public function __construct()
    {
        $this->logPath = __DIR__ . '/../../storage/logs/';
        
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Registrar mensaje informativo
     * 
     * @param string $message Mensaje a registrar
     * @param array $context Datos adicionales (opcional)
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Registrar mensaje de error
     * 
     * @param string $message Mensaje de error
     * @param array $context Datos adicionales (opcional)
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Registrar mensaje de advertencia
     * 
     * @param string $message Mensaje de advertencia
     * @param array $context Datos adicionales (opcional)
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Escribir entrada en el archivo de log
     * 
     * Formato: [HH:MM:SS] [NIVEL] Mensaje {contexto_json}
     * 
     * @param string $level Nivel del log (INFO, ERROR, WARNING)
     * @param string $message Mensaje a registrar
     * @param array $context Datos adicionales en formato JSON
     */
    private function log(string $level, string $message, array $context): void
    {
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $logFile = $this->logPath . "app-{$date}.log";
        
        $logEntry = sprintf(
            "[%s] [%s] %s %s\n",
            $time,
            $level,
            $message,
            !empty($context) ? json_encode($context) : ''
        );
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}
