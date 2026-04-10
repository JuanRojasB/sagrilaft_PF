<?php

namespace App\Config;

/**
 * Configuración de Notificaciones
 * 
 * Define los destinatarios de notificaciones según el tipo de formulario
 */
class NotificationConfig
{
    /**
     * Email para notificaciones de empleados
     * Angie es la encargada de aprobar/rechazar registros de empleados
     */
    const EMPLEADO_REVIEWER_EMAIL = 'angie@pollo-fiesta.com';
    const EMPLEADO_REVIEWER_NAME = 'Angie';
    
    /**
     * Email por defecto para otros tipos de formularios
     */
    const DEFAULT_REVIEWER_EMAIL = 'revisor@sagrilaft.com';
    const DEFAULT_REVIEWER_NAME = 'Revisor SAGRILAFT';
    
    /**
     * Obtener el email del revisor según el tipo de formulario
     * 
     * @param string $formType Tipo de formulario (cliente, proveedor, empleado, etc.)
     * @return array ['email' => string, 'name' => string]
     */
    public static function getReviewerByFormType(string $formType): array
    {
        switch ($formType) {
            case 'empleado':
                return [
                    'email' => self::EMPLEADO_REVIEWER_EMAIL,
                    'name' => self::EMPLEADO_REVIEWER_NAME
                ];
            
            default:
                return [
                    'email' => self::DEFAULT_REVIEWER_EMAIL,
                    'name' => self::DEFAULT_REVIEWER_NAME
                ];
        }
    }
}
