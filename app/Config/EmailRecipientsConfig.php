<?php

namespace App\Config;

/**
 * Configuración de Destinatarios de Correos por Tipo de Formulario
 * 
 * Define quién recibe correos según el tipo de formulario y su estado (aprobado/rechazado)
 */
class EmailRecipientsConfig
{
    /**
     * MODO PRODUCCIÓN: Todos los correos van a pasantesistemas1@pollo-fiesta.com
     * Cambiar a false cuando esté listo para producción real
     */
    const TEST_MODE = true;
    const TEST_EMAIL = 'pasantesistemas1@pollo-fiesta.com';
    
    /**
     * Obtener destinatarios para NUEVO FORMULARIO (link de aprobación)
     * Siempre va al revisor (Angie)
     */
    public static function getNewFormRecipients(string $formType): array
    {
        if (self::TEST_MODE) {
            return [
                ['email' => self::TEST_EMAIL, 'name' => 'Test - Sistemas', 'type' => 'test']
            ];
        }
        
        // En producción: solo Angie recibe el link de aprobación
        return [
            ['email' => 'oficialdecumplimiento@pollo-fiesta.com', 'name' => 'Angie Paola Martínez', 'type' => 'reviewer']
        ];
    }
    
    /**
     * Obtener destinatarios cuando un formulario es APROBADO
     */
    public static function getApprovedRecipients(string $formType, array $formData = []): array
    {
        if (self::TEST_MODE) {
            return [
                ['email' => self::TEST_EMAIL, 'name' => 'Test - Sistemas', 'type' => 'test']
            ];
        }
        
        $recipients = [];
        
        switch ($formType) {
            case 'cliente_natural':
            case 'cliente_juridica':
                // CLIENTES APROBADOS:
                // Comercial + Gerente Comercial + Cliente + Cartera (Camila x2, Eymis, Directora) + Oficial Cumplimiento
                
                // 1. Cliente (creador del formulario)
                if (!empty($formData['creator_email'])) {
                    $recipients[] = [
                        'email' => $formData['creator_email'],
                        'name' => $formData['creator_name'] ?? 'Cliente',
                        'type' => 'creator'
                    ];
                }
                
                // 2. Asesor Comercial
                if (!empty($formData['asesor_email'])) {
                    $recipients[] = [
                        'email' => $formData['asesor_email'],
                        'name' => $formData['asesor_nombre'] ?? 'Asesor Comercial',
                        'type' => 'asesor'
                    ];
                }
                
                // 3. Gerente Comercial (jefe del asesor)
                if (!empty($formData['jefe_email'])) {
                    $recipients[] = [
                        'email' => $formData['jefe_email'],
                        'name' => $formData['jefe_nombre'] ?? 'Gerente Comercial',
                        'type' => 'jefe'
                    ];
                }
                
                // 4. Cartera
                $recipients[] = ['email' => 'cartera@pollo-fiesta.com', 'name' => 'Cartera', 'type' => 'cartera'];
                $recipients[] = ['email' => 'eymis.carey@pollo-fiesta.com', 'name' => 'Eymis Carey - Cartera', 'type' => 'cartera'];
                
                // 5. Oficial de Cumplimiento
                $recipients[] = ['email' => 'oficialdecumplimiento@pollo-fiesta.com', 'name' => 'Angie Paola Martínez', 'type' => 'oficial'];
                break;
                
            case 'proveedor_natural':
            case 'proveedor_juridica':
            case 'proveedor_internacional':
                // PROVEEDORES APROBADOS:
                // Compras + Contabilidad + Tesorería + Área solicitante
                
                $recipients[] = ['email' => 'compras@pollo-fiesta.com', 'name' => 'Compras', 'type' => 'compras'];
                $recipients[] = ['email' => 'esperanza.aguilar@pollo-fiesta.com', 'name' => 'Esperanza Aguilar - Contabilidad', 'type' => 'contabilidad'];
                $recipients[] = ['email' => 'alejandra.camargo@pollo-fiesta.com', 'name' => 'Alejandra Camargo - Contabilidad', 'type' => 'contabilidad'];
                $recipients[] = ['email' => 'asistesoreria@pollo-fiesta.com', 'name' => 'Asistente Tesorería', 'type' => 'tesoreria'];
                
                // Área solicitante (si existe en el formulario)
                if (!empty($formData['area_solicitante_email'])) {
                    $recipients[] = [
                        'email' => $formData['area_solicitante_email'],
                        'name' => $formData['area_solicitante_nombre'] ?? 'Área Solicitante',
                        'type' => 'area_solicitante'
                    ];
                }
                break;
                
            case 'transportista_natural':
            case 'transportista_juridica':
                // TRANSPORTISTAS APROBADOS:
                // Rutas + Gerente Logístico
                
                $recipients[] = ['email' => 'controlderutas@pollo-fiesta.com', 'name' => 'Control de Rutas', 'type' => 'rutas'];
                $recipients[] = ['email' => 'gerlogistica@pollo-fiesta.com', 'name' => 'Diego - Gerente Logística', 'type' => 'gerente_logistico'];
                break;
                
            case 'empleado':
                // EMPLEADOS:
                // Gestión Humana
                
                $recipients[] = ['email' => 'dirgestionhumana@pollo-fiesta.com', 'name' => 'Yohana - Directora Gestión Humana', 'type' => 'gestion_humana'];
                $recipients[] = ['email' => 'seleccionpersonal@pollo-fiesta.com', 'name' => 'Selección de Personal', 'type' => 'gestion_humana'];
                $recipients[] = ['email' => 'r.humanos@pollo-fiesta.com', 'name' => 'Elsa - Recursos Humanos', 'type' => 'gestion_humana'];
                break;
        }
        
        return $recipients;
    }
    
    /**
     * Obtener destinatarios cuando un formulario es RECHAZADO
     */
    public static function getRejectedRecipients(string $formType, array $formData = []): array
    {
        if (self::TEST_MODE) {
            return [
                ['email' => self::TEST_EMAIL, 'name' => 'Test - Sistemas', 'type' => 'test']
            ];
        }
        
        $recipients = [];
        
        switch ($formType) {
            case 'cliente_natural':
            case 'cliente_juridica':
                // CLIENTES RECHAZADOS:
                // Comercial + Gerente Comercial
                
                // 1. Asesor Comercial
                if (!empty($formData['asesor_email'])) {
                    $recipients[] = [
                        'email' => $formData['asesor_email'],
                        'name' => $formData['asesor_nombre'] ?? 'Asesor Comercial',
                        'type' => 'asesor'
                    ];
                }
                
                // 2. Gerente Comercial (jefe del asesor)
                if (!empty($formData['jefe_email'])) {
                    $recipients[] = [
                        'email' => $formData['jefe_email'],
                        'name' => $formData['jefe_nombre'] ?? 'Gerente Comercial',
                        'type' => 'jefe'
                    ];
                }
                break;
                
            case 'proveedor_natural':
            case 'proveedor_juridica':
            case 'proveedor_internacional':
                // PROVEEDORES RECHAZADOS:
                // Compras + Área solicitante
                
                $recipients[] = ['email' => 'compras@pollo-fiesta.com', 'name' => 'Compras', 'type' => 'compras'];
                
                // Área solicitante (si existe en el formulario)
                if (!empty($formData['area_solicitante_email'])) {
                    $recipients[] = [
                        'email' => $formData['area_solicitante_email'],
                        'name' => $formData['area_solicitante_nombre'] ?? 'Área Solicitante',
                        'type' => 'area_solicitante'
                    ];
                }
                break;
                
            case 'transportista_natural':
            case 'transportista_juridica':
                // TRANSPORTISTAS RECHAZADOS:
                // Rutas + Gerente Logístico
                
                $recipients[] = ['email' => 'controlderutas@pollo-fiesta.com', 'name' => 'Control de Rutas', 'type' => 'rutas'];
                $recipients[] = ['email' => 'gerlogistica@pollo-fiesta.com', 'name' => 'Diego - Gerente Logística', 'type' => 'gerente_logistico'];
                break;
                
            case 'empleado':
                // EMPLEADOS RECHAZADOS:
                // Gestión Humana
                
                $recipients[] = ['email' => 'dirgestionhumana@pollo-fiesta.com', 'name' => 'Yohana - Directora Gestión Humana', 'type' => 'gestion_humana'];
                $recipients[] = ['email' => 'seleccionpersonal@pollo-fiesta.com', 'name' => 'Selección de Personal', 'type' => 'gestion_humana'];
                $recipients[] = ['email' => 'r.humanos@pollo-fiesta.com', 'name' => 'Elsa - Recursos Humanos', 'type' => 'gestion_humana'];
                break;
        }
        
        return $recipients;
    }
    
    /**
     * Lista de correos adicionales para guardar (no enviar aún)
     */
    public static function getAdditionalEmails(): array
    {
        return [
            'juan.david.rojas.burbano0@gmail.com'
        ];
    }
}
