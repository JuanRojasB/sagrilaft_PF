<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

/**
 * Servicio para rellenar PDFs usando templates originales
 * Carga los PDFs de storage/pdf y rellena los campos con datos
 */
class PdfTemplateFiller
{
    private Fpdi $pdf;
    private array $data;
    private string $templatePath;

    public function __construct()
    {
        require_once __DIR__ . '/../Libraries/FPDI-2.6.0/src/autoload.php';
    }

    /**
     * Cargar template y rellenar con datos
     * 
     * @param string $templateFile Nombre del archivo PDF (ej: "FGF-17 DECLARACIÓN...")
     * @param array $data Datos a rellenar
     * @return string PDF generado
     */
    public function fill(string $templateFile, array $data): string
    {
        $this->templatePath = __DIR__ . '/../../storage/pdf/' . $templateFile;
        
        if (!file_exists($this->templatePath)) {
            throw new \Exception("Template no encontrado: $templateFile");
        }

        $this->data = $data;
        $this->pdf = new Fpdi();

        try {
            // Cargar todas las páginas del template
            $pageCount = $this->pdf->setSourceFile($this->templatePath);
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $templateId = $this->pdf->importPage($i);
                $size = $this->pdf->getTemplateSize($templateId);
                
                // Crear página con mismo tamaño que template
                $this->pdf->AddPage(
                    $size['width'] > $size['height'] ? 'L' : 'P',
                    [$size['width'], $size['height']]
                );
                
                // Insertar template
                $this->pdf->useTemplate($templateId);
                
                // Rellenar campos si es la primera página
                if ($i === 1) {
                    $this->fillFields();
                }
            }
            
            return $this->pdf->Output('S');
            
        } catch (\Exception $e) {
            throw new \Exception("Error rellenando PDF: " . $e->getMessage());
        }
    }

    /**
     * Rellenar campos del PDF (a posiciones específicas)
     * Esto se personaliza según el template
     */
    private function fillFields(): void
    {
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(0, 0, 0);
        
        // Aquí se agregan campos específicos según el template
        // Los números son aproximados y variarán según el PDF
        
        // Ejemplo para FGF-17:
        if (strpos($this->templatePath, 'FGF-17') !== false) {
            // Nombre declarante (aproximadamente en x=50, y=70)
            if (!empty($this->data['nombre_declarante'] ?? $this->data['representante_nombre'])) {
                $this->pdf->SetXY(50, 70);
                $this->pdf->Cell(100, 5, $this->data['nombre_declarante'] ?? $this->data['representante_nombre']);
            }
            
            // Origen de recursos (aproximadamente en x=50, y=110)
            if (!empty($this->data['origen_fondos'] ?? $this->data['origen_recursos'])) {
                $this->pdf->SetXY(50, 110);
                $this->pdf->MultiCell(120, 4, $this->data['origen_fondos'] ?? $this->data['origen_recursos']);
            }
            
            // Es PEP
            if (!empty($this->data['es_pep'])) {
                $x = ($this->data['es_pep'] === 'si') ? 140 : 150;
                $this->pdf->SetXY($x, 145);
                $this->pdf->Cell(5, 5, 'X');
            }
        }
        
        // Similar para FCO-03, FCO-02, etc.
    }

    /**
     * Buscar template automáticamente según tipo de formulario
     */
    public static function getTemplateForType(string $formType): ?string
    {
        $templates = [
            'cliente_natural'         => 'FGF 08 Creación de cliente persona natural (1).pdf',
            'cliente_juridica'        => 'FGF 16 Creación de clientes persona juridica (2).pdf',
            'declaracion_cliente'     => 'FGF-17 DECLARACIÓN ORIGEN DE FONDOS CLIENTES VERSION 02 (2).pdf',
            'proveedor_natural'       => 'FCO-05 Conocimiento de proveedor persona natural (1).pdf',
            'proveedor_juridica'      => 'FCO-02 Conocimiento del proveedor persona juridica (1).pdf',
            'declaracion_proveedor'   => 'FCO-03 Declaracion sobre origen de fondos (1).pdf',
            'proveedor_internacional' => 'FCO-04 Conocimiento de proveedor internacional (1).pdf',
        ];
        
        return $templates[$formType] ?? null;
    }
}
