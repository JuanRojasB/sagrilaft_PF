<?php

namespace App\Services;

// Incluir librerías locales
require_once __DIR__ . '/../Libraries/fpdf.php';
require_once __DIR__ . '/../Libraries/FPDI-2.6.0/src/autoload.php';

use setasign\Fpdi\Fpdi;

/**
 * Unión de PDFs usando FPDI (librería local)
 * 
 * Con manejo robusto de errores y fallback a Ghostscript
 */
class FpdiPdfMerger
{
    private Logger $logger;
    private ?Fpdi $pdf = null;
    private array $files = [];

    public function __construct()
    {
        $this->logger = new Logger();
        
        try {
            $this->pdf = new Fpdi();
            $this->logger->info('FPDI initialized successfully');
        } catch (\Exception $e) {
            $this->logger->error('Failed to initialize FPDI', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Agregar un PDF para unir
     */
    public function addPDF(string $filepath): void
    {
        if (!file_exists($filepath)) {
            throw new \Exception("PDF file not found: {$filepath}");
        }

        $this->files[] = $filepath;
    }

    /**
     * Unir todos los PDFs agregados
     */
    public function merge(string $outputPath): bool
    {
        try {
            if (empty($this->files)) {
                throw new \Exception('No PDF files to merge');
            }

            $this->logger->info('Attempting to merge PDFs with FPDI', [
                'count' => count($this->files)
            ]);

            // Intentar con FPDI primero
            try {
                return $this->mergeWithFPDI($outputPath);
            } catch (\Exception $e) {
                $this->logger->warning('FPDI merge failed, trying Ghostscript', [
                    'error' => $e->getMessage()
                ]);
                
                // Intentar con Ghostscript
                return $this->mergeWithGhostscript($outputPath);
            }

        } catch (\Exception $e) {
            $this->logger->error('All PDF merge methods failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Unir con FPDI
     */
    private function mergeWithFPDI(string $outputPath): bool
    {
        $totalPages = 0;
        $repairer = new PdfRepairer();
        $successfulPdfs = [];
        $failedPdfs = [];

        // Procesar cada PDF
        foreach ($this->files as $file) {
            try {
                // Intentar reparar el PDF si tiene problemas
                $workingFile = $file;
                if ($repairer->needsRepair($file)) {
                    $this->logger->info('PDF needs repair, attempting to fix', [
                        'file' => basename($file)
                    ]);
                    
                    $repairedFile = sys_get_temp_dir() . '/repaired_' . basename($file);
                    if ($repairer->repair($file, $repairedFile)) {
                        $workingFile = $repairedFile;
                        $this->logger->info('PDF repaired successfully', [
                            'file' => basename($file)
                        ]);
                    }
                }

                // Obtener número de páginas
                $pageCount = $this->pdf->setSourceFile($workingFile);
                
                $this->logger->info('Processing PDF with FPDI', [
                    'file' => basename($file),
                    'pages' => $pageCount
                ]);

                // Importar cada página
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    // Importar página
                    $templateId = $this->pdf->importPage($pageNo);
                    
                    // Obtener tamaño de la página
                    $size = $this->pdf->getTemplateSize($templateId);
                    
                    // Determinar orientación
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    
                    // Agregar página con el tamaño correcto
                    $this->pdf->AddPage($orientation, [$size['width'], $size['height']]);
                    
                    // Usar la página importada
                    $this->pdf->useTemplate($templateId);
                    
                    $totalPages++;
                }

                $successfulPdfs[] = basename($file);

                // Limpiar archivo temporal si se creó
                if ($workingFile !== $file && file_exists($workingFile)) {
                    unlink($workingFile);
                }

            } catch (\Exception $e) {
                $this->logger->warning('Skipping corrupted PDF', [
                    'file' => basename($file),
                    'error' => $e->getMessage()
                ]);
                
                $failedPdfs[] = basename($file);
                
                // Continuar con el siguiente PDF en lugar de fallar completamente
                continue;
            }
        }

        // Si no se pudo procesar ningún PDF, lanzar error
        if (empty($successfulPdfs)) {
            throw new \Exception('No se pudo procesar ningún PDF. Todos los archivos están corruptos.');
        }

        // Si algunos PDFs fallaron, agregar una página de advertencia
        if (!empty($failedPdfs)) {
            $this->addWarningPage($failedPdfs);
        }

        // Guardar PDF consolidado
        $this->pdf->Output('F', $outputPath);

        $this->logger->info('PDFs merged with FPDI (some may have been skipped)', [
            'output' => $outputPath,
            'total_pages' => $totalPages,
            'successful' => count($successfulPdfs),
            'failed' => count($failedPdfs),
            'size' => filesize($outputPath)
        ]);

        return true;
    }

    /**
     * Agregar página de advertencia sobre PDFs corruptos
     */
    private function addWarningPage(array $failedPdfs): void
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', 'B', 16);
        $this->pdf->SetTextColor(255, 0, 0);
        $this->pdf->Cell(0, 10, 'ADVERTENCIA', 0, 1, 'C');
        
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Ln(5);
        $this->pdf->MultiCell(0, 6, 'Los siguientes archivos PDF no pudieron ser incluidos en este documento consolidado debido a que estan corruptos o tienen problemas de estructura:');
        
        $this->pdf->Ln(5);
        $this->pdf->SetFont('Arial', 'B', 11);
        foreach ($failedPdfs as $filename) {
            $this->pdf->Cell(10, 6, '-');
            $this->pdf->MultiCell(0, 6, $filename);
        }
        
        $this->pdf->Ln(5);
        $this->pdf->SetFont('Arial', 'I', 10);
        $this->pdf->MultiCell(0, 5, 'Nota: Los archivos originales estan disponibles en el sistema y pueden ser descargados individualmente.');
    }

    /**
     * Unir con Ghostscript (fallback)
     */
    private function mergeWithGhostscript(string $outputPath): bool
    {
        // Escapar rutas para seguridad
        $escapedPaths = array_map('escapeshellarg', $this->files);
        $escapedOutput = escapeshellarg($outputPath);

        // Comando Ghostscript
        $command = "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$escapedOutput} " . implode(' ', $escapedPaths);

        // Ejecutar comando
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode === 0 && file_exists($outputPath)) {
            $this->logger->info('PDFs merged with Ghostscript', [
                'output' => $outputPath,
                'size' => filesize($outputPath)
            ]);
            return true;
        }

        throw new \Exception('Ghostscript merge failed: ' . implode("\n", $output));
    }

    /**
     * Agregar imagen de firma a un PDF
     */
    public function addSignatureImage(string $pdfPath, string $signaturePath, string $outputPath): bool
    {
        try {
            $this->logger->info('Adding signature to PDF', [
                'pdf' => $pdfPath,
                'signature' => $signaturePath
            ]);

            // Intentar con FPDI primero
            try {
                return $this->addSignatureWithFPDI($pdfPath, $signaturePath, $outputPath);
            } catch (\Exception $e) {
                $this->logger->warning('FPDI signature failed, trying ImageMagick', [
                    'error' => $e->getMessage()
                ]);
                
                // Intentar con ImageMagick
                return $this->addSignatureWithImageMagick($pdfPath, $signaturePath, $outputPath);
            }

        } catch (\Exception $e) {
            $this->logger->error('All signature methods failed', [
                'error' => $e->getMessage()
            ]);
            
            // Si todo falla, copiar el PDF sin firma
            copy($pdfPath, $outputPath);
            return false;
        }
    }

    /**
     * Agregar firma con FPDI
     */
    private function addSignatureWithFPDI(string $pdfPath, string $signaturePath, string $outputPath): bool
    {
        // Crear nuevo PDF
        $pdf = new Fpdi();
        
        // Obtener número de páginas del PDF original
        $pageCount = $pdf->setSourceFile($pdfPath);

        // Procesar cada página
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // Importar página
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            
            // Determinar orientación
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
            
            // Agregar página
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            
            // Usar template
            $pdf->useTemplate($templateId);
            
            // Agregar firma en la esquina inferior derecha
            $signatureWidth = 24;  // 40% más pequeña (40 * 0.6 = 24)
            $signatureHeight = 12; // 40% más pequeña (20 * 0.6 = 12)
            $x = $size['width'] - $signatureWidth - 10;
            $y = $size['height'] - $signatureHeight - 10;
            
            // Verificar que la imagen existe y agregarla
            if (file_exists($signaturePath)) {
                $pdf->Image($signaturePath, $x, $y, $signatureWidth, $signatureHeight);
            }
        }

        // Guardar PDF firmado
        $pdf->Output('F', $outputPath);

        $this->logger->info('Signature added with FPDI', [
            'output' => $outputPath
        ]);

        return true;
    }

    /**
     * Agregar firma con ImageMagick (fallback)
     */
    private function addSignatureWithImageMagick(string $pdfPath, string $signaturePath, string $outputPath): bool
    {
        $escapedPdf = escapeshellarg($pdfPath);
        $escapedSignature = escapeshellarg($signaturePath);
        $escapedOutput = escapeshellarg($outputPath);

        // Comando ImageMagick
        $command = "convert {$escapedPdf} {$escapedSignature} -gravity SouthEast -geometry +20+20 -composite {$escapedOutput}";

        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode === 0 && file_exists($outputPath)) {
            $this->logger->info('Signature added with ImageMagick', [
                'output' => $outputPath
            ]);
            return true;
        }

        throw new \Exception('ImageMagick signature failed');
    }

    /**
     * Resetear
     */
    public function reset(): void
    {
        $this->files = [];
        $this->pdf = new Fpdi();
    }
}
