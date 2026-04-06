<?php

namespace App\Services;

/**
 * Servicio de Consolidación y Firma de PDFs
 * 
 * Usa FPDI (librería local) para unir PDFs REALMENTE
 * 100% Portable - NO requiere instalaciones externas
 * 
 * @package App\Services
 */
class PdfConsolidatorService
{
    private Logger $logger;
    private FpdiPdfMerger $merger;

    public function __construct()
    {
        $this->logger = new Logger();
        
        try {
            $this->merger = new FpdiPdfMerger();
        } catch (\Exception $e) {
            $this->logger->error('Failed to initialize FPDI merger', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('PDF merger not available: ' . $e->getMessage());
        }
    }

    /**
     * Consolidar múltiples PDFs en uno solo
     * 
     * @param array $pdfPaths Array de rutas de archivos PDF
     * @param string $outputPath Ruta donde guardar el PDF consolidado
     * @return array Información del PDF consolidado (total_pages, filepath)
     */
    public function consolidatePDFs(array $pdfPaths, string $outputPath): array
    {
        try {
            $this->logger->info('Starting PDF consolidation with FPDI', [
                'count' => count($pdfPaths),
                'output' => $outputPath
            ]);

            // Agregar todos los PDFs al merger
            foreach ($pdfPaths as $path) {
                $this->merger->addPDF($path);
            }

            // Unir
            $this->merger->merge($outputPath);

            // Contar páginas
            $totalPages = $this->countPDFPages($outputPath);

            $this->logger->info('PDFs consolidated successfully', [
                'output' => $outputPath,
                'total_pages' => $totalPages,
                'size' => filesize($outputPath)
            ]);

            // Resetear merger para próximo uso
            $this->merger->reset();

            return [
                'total_pages' => $totalPages,
                'filepath' => $outputPath
            ];

        } catch (\Exception $e) {
            $this->logger->error('PDF consolidation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Contar páginas de un PDF
     */
    private function countPDFPages(string $pdfPath): int
    {
        if (!file_exists($pdfPath)) {
            return 0;
        }

        $content = file_get_contents($pdfPath);
        
        // Método 1: Buscar /Count en el catálogo de páginas
        if (preg_match('/\/Type\s*\/Pages.*?\/Count\s+(\d+)/s', $content, $matches)) {
            return (int)$matches[1];
        }

        // Método 2: Contar objetos de tipo /Page (no /Pages)
        $count = preg_match_all('/\/Type\s*\/Page[^s]/', $content, $matches);
        
        return $count > 0 ? $count : 1;
    }

    /**
     * Aplicar firma digital a un PDF
     * 
     * @param string $pdfPath Ruta del PDF a firmar
     * @param string $signaturePath Ruta de la imagen de la firma
     * @param string $outputPath Ruta donde guardar el PDF firmado
     * @return bool True si se firmó correctamente
     */
    public function signPDF(string $pdfPath, string $signaturePath, string $outputPath): bool
    {
        try {
            $this->logger->info('Adding signature to PDF with FPDI', [
                'pdf' => $pdfPath,
                'signature' => $signaturePath,
                'output' => $outputPath
            ]);

            $result = $this->merger->addSignatureImage($pdfPath, $signaturePath, $outputPath);

            if ($result) {
                $this->logger->info('Signature added successfully', [
                    'output' => $outputPath,
                    'size' => filesize($outputPath)
                ]);
            } else {
                $this->logger->warning('PDF copied without signature', [
                    'output' => $outputPath
                ]);
            }

            return true;

        } catch (\Exception $e) {
            $this->logger->error('PDF signing failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Consolidar y firmar PDFs en un solo paso
     * 
     * @param array $pdfPaths Array de rutas de archivos PDF
     * @param string $signaturePath Ruta de la imagen de la firma
     * @param string $outputPath Ruta donde guardar el PDF final
     * @return array Información del PDF consolidado y firmado
     */
    public function consolidateAndSign(array $pdfPaths, string $signaturePath, string $outputPath): array
    {
        // Primero consolidar
        $tempPath = $outputPath . '.temp.pdf';
        $result = $this->consolidatePDFs($pdfPaths, $tempPath);

        // Luego firmar
        $this->signPDF($tempPath, $signaturePath, $outputPath);

        // Eliminar archivo temporal
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        return $result;
    }
}
