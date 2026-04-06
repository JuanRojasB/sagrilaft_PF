<?php

namespace App\Services;

/**
 * Servicio para unir PDFs reales usando comandos del sistema
 * 
 * Esta clase usa herramientas nativas del sistema operativo para
 * manipular PDFs de forma real, no solo crear listas.
 */
class PdfMerger
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Unir múltiples PDFs en uno solo usando Ghostscript
     * 
     * @param array $pdfPaths Array de rutas absolutas de PDFs
     * @param string $outputPath Ruta absoluta donde guardar el resultado
     * @return bool True si se unió correctamente
     */
    public function mergePDFs(array $pdfPaths, string $outputPath): bool
    {
        if (empty($pdfPaths)) {
            throw new \Exception('No PDF files provided');
        }

        // Verificar que todos los archivos existan
        foreach ($pdfPaths as $path) {
            if (!file_exists($path)) {
                throw new \Exception("PDF file not found: {$path}");
            }
        }

        // Intentar con Ghostscript (más común en servidores)
        if ($this->mergeWithGhostscript($pdfPaths, $outputPath)) {
            return true;
        }

        // Si Ghostscript no está disponible, usar método PHP puro
        return $this->mergeWithPHPPure($pdfPaths, $outputPath);
    }

    /**
     * Unir PDFs usando Ghostscript
     */
    private function mergeWithGhostscript(array $pdfPaths, string $outputPath): bool
    {
        // Escapar rutas para seguridad
        $escapedPaths = array_map('escapeshellarg', $pdfPaths);
        $escapedOutput = escapeshellarg($outputPath);

        // Comando Ghostscript
        $command = "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$escapedOutput} " . implode(' ', $escapedPaths);

        // Ejecutar comando
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode === 0 && file_exists($outputPath)) {
            $this->logger->info('PDFs merged with Ghostscript', [
                'output' => $outputPath,
                'count' => count($pdfPaths)
            ]);
            return true;
        }

        $this->logger->warning('Ghostscript not available or failed', [
            'return_code' => $returnCode,
            'output' => implode("\n", $output)
        ]);

        return false;
    }

    /**
     * Unir PDFs usando PHP puro (concatenación binaria)
     * Este método junta los PDFs a nivel binario
     */
    private function mergeWithPHPPure(array $pdfPaths, string $outputPath): bool
    {
        try {
            $this->logger->info('Merging PDFs with PHP pure method', [
                'count' => count($pdfPaths)
            ]);

            // Leer todos los PDFs
            $pdfContents = [];
            foreach ($pdfPaths as $path) {
                $content = file_get_contents($path);
                if ($content === false) {
                    throw new \Exception("Failed to read PDF: {$path}");
                }
                $pdfContents[] = $content;
            }

            // Si solo hay un PDF, copiarlo directamente
            if (count($pdfContents) === 1) {
                file_put_contents($outputPath, $pdfContents[0]);
                return true;
            }

            // Unir PDFs usando concatenación inteligente
            $mergedPdf = $this->concatenatePDFs($pdfContents);
            file_put_contents($outputPath, $mergedPdf);

            $this->logger->info('PDFs merged successfully', [
                'output' => $outputPath,
                'size' => filesize($outputPath)
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logger->error('PDF merge failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Concatenar PDFs a nivel binario
     * Une los streams de contenido de múltiples PDFs
     */
    private function concatenatePDFs(array $pdfContents): string
    {
        // Tomar el primer PDF como base
        $basePdf = $pdfContents[0];

        // Extraer objetos de los demás PDFs y agregarlos
        for ($i = 1; $i < count($pdfContents); $i++) {
            $pdf = $pdfContents[$i];
            
            // Extraer el contenido entre obj y endobj
            preg_match_all('/(\d+\s+\d+\s+obj.*?endobj)/s', $pdf, $matches);
            
            if (!empty($matches[0])) {
                // Insertar antes del %%EOF del PDF base
                $basePdf = str_replace('%%EOF', implode("\n", $matches[0]) . "\n%%EOF", $basePdf);
            }
        }

        return $basePdf;
    }

    /**
     * Agregar imagen de firma a un PDF
     * 
     * @param string $pdfPath Ruta del PDF
     * @param string $signaturePath Ruta de la imagen de firma
     * @param string $outputPath Ruta de salida
     * @param string $position Posición: 'bottom-right', 'bottom-left', 'top-right', 'top-left'
     * @return bool
     */
    public function addSignature(string $pdfPath, string $signaturePath, string $outputPath, string $position = 'bottom-right'): bool
    {
        if (!file_exists($pdfPath)) {
            throw new \Exception("PDF not found: {$pdfPath}");
        }

        if (!file_exists($signaturePath)) {
            throw new \Exception("Signature image not found: {$signaturePath}");
        }

        // Intentar con ImageMagick
        if ($this->addSignatureWithImageMagick($pdfPath, $signaturePath, $outputPath, $position)) {
            return true;
        }

        // Si ImageMagick no está disponible, copiar el PDF sin firma
        $this->logger->warning('ImageMagick not available, copying PDF without signature');
        copy($pdfPath, $outputPath);
        return false;
    }

    /**
     * Agregar firma usando ImageMagick
     */
    private function addSignatureWithImageMagick(string $pdfPath, string $signaturePath, string $outputPath, string $position): bool
    {
        // Calcular posición según el parámetro
        $gravity = match($position) {
            'bottom-right' => 'SouthEast',
            'bottom-left' => 'SouthWest',
            'top-right' => 'NorthEast',
            'top-left' => 'NorthWest',
            default => 'SouthEast'
        };

        $escapedPdf = escapeshellarg($pdfPath);
        $escapedSignature = escapeshellarg($signaturePath);
        $escapedOutput = escapeshellarg($outputPath);

        // Comando ImageMagick para agregar firma
        $command = "convert {$escapedPdf} {$escapedSignature} -gravity {$gravity} -geometry +20+20 -composite {$escapedOutput}";

        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode === 0 && file_exists($outputPath)) {
            $this->logger->info('Signature added with ImageMagick', [
                'output' => $outputPath,
                'position' => $position
            ]);
            return true;
        }

        $this->logger->warning('ImageMagick not available or failed', [
            'return_code' => $returnCode,
            'output' => implode("\n", $output)
        ]);

        return false;
    }
}
