<?php

namespace App\Services;

/**
 * Servicio de Gestión de Plantillas PDF
 * 
 * Selecciona el formato PDF correcto según:
 * - Tipo de usuario (cliente, proveedor, transportista)
 * - Tipo de persona (natural, jurídica)
 * - Alcance (nacional, internacional)
 */
class TemplateService
{
    private string $templatesPath;
    
    public function __construct()
    {
        $this->templatesPath = __DIR__ . '/../../storage/templates/';
    }
    
    /**
     * Obtener la plantilla principal según tipo de usuario y persona
     * 
     * @param string $userType cliente|proveedor|transportista
     * @param string $personType natural|juridica
     * @param bool $isInternational Solo para proveedores
     * @return string|null Ruta completa al archivo PDF
     */
    public function getMainTemplate(string $userType, string $personType, bool $isInternational = false): ?string
    {
        $templates = [
            'cliente' => [
                'natural' => 'FGF 08 Creación de cliente persona natural.pdf',
                'juridica' => 'FGF 16 Creación de clientes persona juridica.pdf'
            ],
            'proveedor' => [
                'natural' => $isInternational 
                    ? 'FCO-04 Conocimiento de proveedor internacional.pdf'
                    : 'FCO-05 Conocimiento de proveedor persona natural.pdf',
                'juridica' => $isInternational
                    ? 'FCO-04 Conocimiento de proveedor internacional.pdf'
                    : 'FCO-02 Conocimiento del proveedor persona juridica.pdf'
            ],
            'transportista' => [
                'natural' => 'FCO-05 Conocimiento de proveedor persona natural.pdf', // Usar formato proveedor
                'juridica' => 'FCO-02 Conocimiento del proveedor persona juridica.pdf'
            ]
        ];
        
        $filename = $templates[$userType][$personType] ?? null;
        
        if (!$filename) {
            return null;
        }
        
        $fullPath = $this->templatesPath . $filename;
        
        return file_exists($fullPath) ? $fullPath : null;
    }
    
    /**
     * Obtener la plantilla de declaración de origen de fondos
     * 
     * @param string $userType cliente|proveedor|transportista
     * @return string|null Ruta completa al archivo PDF
     */
    public function getDeclarationTemplate(string $userType): ?string
    {
        $templates = [
            'cliente' => 'FGF-17 DECLARACIÓN ORIGEN DE FONDOS CLIENTES VERSION 02.pdf',
            'proveedor' => 'FCO-03 Declaracion sobre origen de fondos.pdf',
            'transportista' => 'FCO-03 Declaracion sobre origen de fondos.pdf'
        ];
        
        $filename = $templates[$userType] ?? null;
        
        if (!$filename) {
            return null;
        }
        
        $fullPath = $this->templatesPath . $filename;
        
        return file_exists($fullPath) ? $fullPath : null;
    }
    
    /**
     * Obtener todas las plantillas necesarias para un formulario
     * 
     * @param string $userType
     * @param string $personType
     * @param bool $isInternational
     * @return array Array de rutas a los PDFs
     */
    public function getAllTemplates(string $userType, string $personType, bool $isInternational = false): array
    {
        $templates = [];
        
        // Plantilla principal
        $main = $this->getMainTemplate($userType, $personType, $isInternational);
        if ($main) {
            $templates['main'] = $main;
        }
        
        // Declaración de origen de fondos
        $declaration = $this->getDeclarationTemplate($userType);
        if ($declaration) {
            $templates['declaration'] = $declaration;
        }
        
        return $templates;
    }
    
    /**
     * Obtener el catálogo de actividades económicas
     * 
     * @return string|null Ruta al archivo Excel
     */
    public function getEconomicActivitiesCatalog(): ?string
    {
        $fullPath = $this->templatesPath . 'actividad economica.xlsx';
        return file_exists($fullPath) ? $fullPath : null;
    }
    
    /**
     * Verificar si todas las plantillas necesarias existen
     * 
     * @param string $userType
     * @param string $personType
     * @return bool
     */
    public function hasAllTemplates(string $userType, string $personType): bool
    {
        $templates = $this->getAllTemplates($userType, $personType);
        return !empty($templates);
    }
}
