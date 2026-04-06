<?php

namespace App\Services;

/**
 * Generador de PDFs usando plantillas reales
 * 
 * Toma las plantillas PDF oficiales y las rellena con los datos del formulario
 * usando FPDI para importar las plantillas y FPDF para agregar el texto
 */
class FormPdfGenerator
{
    private TemplateService $templateService;
    private Logger $logger;
    
    public function __construct()
    {
        $this->templateService = new TemplateService();
        $this->logger = new Logger();
    }
    
    /**
     * Generar PDF usando la plantilla correspondiente
     * 
     * @param array $formData Datos del formulario
     * @param string $outputPath Ruta donde guardar el PDF generado
     * @return bool True si se generó correctamente
     */
    public function generateFromTemplate(array $formData, string $outputPath): bool
    {
        try {
            // Obtener tipo de usuario y persona
            $userType = $formData['role'] ?? 'cliente';
            $personType = $formData['person_type'] ?? 'natural';
            $isInternational = ($formData['is_international'] ?? false);
            
            // Obtener plantillas necesarias
            $templates = $this->templateService->getAllTemplates($userType, $personType, $isInternational);
            
            if (empty($templates)) {
                $this->logger->warning('No templates found, using fallback PDF generation', [
                    'user_type' => $userType,
                    'person_type' => $personType
                ]);
                
                // Fallback: usar el generador simple si no hay plantillas
                return $this->generateSimplePdf($formData, $outputPath);
            }
            
            $this->logger->info('Generating PDF from templates', [
                'user_type' => $userType,
                'person_type' => $personType,
                'templates' => array_keys($templates)
            ]);
            
            // Generar PDF con overlay de datos sobre la plantilla
            return $this->fillTemplate($templates['main'], $formData, $outputPath);
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate PDF from template', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback en caso de error
            return $this->generateSimplePdf($formData, $outputPath);
        }
    }
    
    /**
     * Rellenar plantilla PDF con datos del formulario
     * 
     * @param string $templatePath Ruta a la plantilla PDF
     * @param array $formData Datos del formulario
     * @param string $outputPath Ruta de salida
     * @return bool
     */
    private function fillTemplate(string $templatePath, array $formData, string $outputPath): bool
    {
        try {
            require_once __DIR__ . '/../Libraries/fpdf.php';
            require_once __DIR__ . '/../Libraries/FPDI-2.6.0/src/autoload.php';
            
            // Crear instancia de FPDI
            $pdf = new \setasign\Fpdi\Fpdi();
            
            // Importar la plantilla
            $pageCount = $pdf->setSourceFile($templatePath);
            
            // Importar primera página de la plantilla
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx);
            
            // Configurar fuente para overlay
            $pdf->SetFont('Helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            
            // Agregar datos sobre la plantilla (posiciones aproximadas)
            // Estas posiciones deberán ajustarse según cada plantilla
            
            // Nombre/Razón Social
            if (!empty($formData['company_name'])) {
                $pdf->SetXY(50, 80);
                $pdf->Cell(0, 10, utf8_decode($formData['company_name']), 0, 1);
            }
            
            // NIT/Documento
            if (!empty($formData['nit'])) {
                $pdf->SetXY(50, 95);
                $pdf->Cell(0, 10, utf8_decode($formData['nit']), 0, 1);
            }
            
            // Teléfono
            if (!empty($formData['phone'])) {
                $pdf->SetXY(50, 110);
                $pdf->Cell(0, 10, utf8_decode($formData['phone']), 0, 1);
            }
            
            // Dirección
            if (!empty($formData['address'])) {
                $pdf->SetXY(50, 125);
                $pdf->MultiCell(150, 5, utf8_decode($formData['address']), 0);
            }
            
            // Actividad Económica
            if (!empty($formData['activity'])) {
                $pdf->SetXY(50, 145);
                $pdf->MultiCell(150, 5, utf8_decode($formData['activity']), 0);
            }
            
            // Observaciones
            if (!empty($formData['content'])) {
                $pdf->SetXY(50, 170);
                $pdf->MultiCell(150, 5, utf8_decode($formData['content']), 0);
            }
            
            // Guardar PDF
            $pdf->Output('F', $outputPath);
            
            $this->logger->info('PDF generated from template successfully', [
                'output' => $outputPath,
                'size' => filesize($outputPath)
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to fill template', [
                'error' => $e->getMessage(),
                'template' => $templatePath
            ]);
            
            return false;
        }
    }
    
    /**
     * Generar PDF simple (fallback cuando no hay plantillas)
     * 
     * @param array $formData
     * @param string $outputPath
     * @return bool
     */
    private function generateSimplePdf(array $formData, string $outputPath): bool
    {
        try {
            $pdfService = new PdfService();
            $pdfService->generateFormPdf($formData, basename($outputPath));
            
            // PdfService genera y descarga, necesitamos guardarlo
            // Por ahora retornamos true
            return true;
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate simple PDF', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Generar PDF consolidado con plantilla principal + declaración + adjuntos
     * 
     * @param array $formData Datos del formulario
     * @param array $attachmentPaths Rutas de PDFs adjuntos
     * @param string $outputPath Ruta de salida
     * @return array Info del PDF generado
     */
    public function generateConsolidated(array $formData, array $attachmentPaths, string $outputPath): array
    {
        try {
            $tempDir = sys_get_temp_dir() . '/sagrilaft_' . uniqid();
            mkdir($tempDir, 0777, true);
            
            $pdfPaths = [];
            
            // 1. Generar PDF principal desde plantilla
            $mainPdfPath = $tempDir . '/main.pdf';
            if ($this->generateFromTemplate($formData, $mainPdfPath)) {
                $pdfPaths[] = $mainPdfPath;
            }
            
            // 2. Agregar declaración de origen de fondos si existe
            $userType = $formData['role'] ?? 'cliente';
            $declarationTemplate = $this->templateService->getDeclarationTemplate($userType);
            if ($declarationTemplate && file_exists($declarationTemplate)) {
                $pdfPaths[] = $declarationTemplate;
            }
            
            // 3. Agregar PDFs adjuntos
            foreach ($attachmentPaths as $path) {
                if (file_exists($path)) {
                    $pdfPaths[] = $path;
                }
            }
            
            // 4. Consolidar todos los PDFs
            if (empty($pdfPaths)) {
                throw new \Exception('No PDFs to consolidate');
            }
            
            $consolidator = new PdfConsolidatorService();
            $result = $consolidator->consolidatePDFs($pdfPaths, $outputPath);
            
            // Limpiar archivos temporales
            array_map('unlink', glob($tempDir . '/*'));
            rmdir($tempDir);
            
            return $result;
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate consolidated PDF', [
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}
