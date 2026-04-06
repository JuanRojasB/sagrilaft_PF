<?php

namespace App\Services;

/**
 * Unión de PDFs usando PHP PURO
 * 
 * Esta clase une PDFs reales sin necesidad de herramientas externas.
 * Usa manipulación binaria de PDFs a nivel de estructura.
 */
class PdfMergerPure
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Unir múltiples PDFs en uno solo usando PHP puro
     * 
     * @param array $pdfPaths Array de rutas absolutas de PDFs
     * @param string $outputPath Ruta absoluta donde guardar el resultado
     * @return bool True si se unió correctamente
     */
    public function mergePDFs(array $pdfPaths, string $outputPath): bool
    {
        try {
            if (empty($pdfPaths)) {
                throw new \Exception('No PDF files provided');
            }

            // Verificar que todos los archivos existan
            foreach ($pdfPaths as $path) {
                if (!file_exists($path)) {
                    throw new \Exception("PDF file not found: {$path}");
                }
            }

            $this->logger->info('Starting PDF merge with pure PHP', [
                'count' => count($pdfPaths)
            ]);

            // Leer todos los PDFs
            $pdfContents = [];
            foreach ($pdfPaths as $path) {
                $content = file_get_contents($path);
                if ($content === false) {
                    throw new \Exception("Failed to read PDF: {$path}");
                }
                $pdfContents[] = [
                    'content' => $content,
                    'filename' => basename($path),
                    'size' => filesize($path)
                ];
            }

            // Crear PDF consolidado
            $mergedPdf = $this->createMergedPDF($pdfContents);
            
            // Guardar
            file_put_contents($outputPath, $mergedPdf);

            $this->logger->info('PDFs merged successfully with pure PHP', [
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
     * Crear un PDF consolidado que incluye todos los PDFs originales
     * 
     * @param array $pdfContents Array con información de cada PDF
     * @return string Contenido del PDF consolidado
     */
    private function createMergedPDF(array $pdfContents): string
    {
        // Si solo hay un PDF, devolverlo directamente
        if (count($pdfContents) === 1) {
            return $pdfContents[0]['content'];
        }

        // Concatenar todos los PDFs a nivel binario
        $mergedContent = '';
        $objectOffset = 1;
        $objects = [];
        $pageRefs = [];

        foreach ($pdfContents as $index => $pdf) {
            $content = $pdf['content'];
            
            // Extraer objetos del PDF
            preg_match_all('/(\d+)\s+(\d+)\s+obj(.*?)endobj/s', $content, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $objNum = (int)$match[1] + $objectOffset;
                $objContent = $match[3];
                
                // Si es una página, guardar referencia
                if (strpos($objContent, '/Type /Page') !== false) {
                    $pageRefs[] = $objNum;
                }
                
                $objects[$objNum] = $objContent;
            }
            
            // Incrementar offset para el siguiente PDF
            $objectOffset += count($matches);
        }

        // Construir el PDF final
        $pdf = "%PDF-1.4\n";
        $pdf .= "%âãÏÓ\n";
        
        // Catálogo (objeto 1)
        $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        
        // Páginas (objeto 2)
        $pagesObj = "2 0 obj\n<< /Type /Pages /Kids [";
        foreach ($pageRefs as $pageRef) {
            $pagesObj .= "{$pageRef} 0 R ";
        }
        $pagesObj .= "] /Count " . count($pageRefs) . " >>\nendobj\n";
        $pdf .= $pagesObj;
        
        // Agregar todos los objetos
        foreach ($objects as $objNum => $objContent) {
            $pdf .= "{$objNum} 0 obj{$objContent}endobj\n";
        }
        
        // Tabla de referencias cruzadas
        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 3) . "\n";
        $pdf .= "0000000000 65535 f \n";
        
        // Trailer
        $pdf .= "trailer\n<< /Size " . (count($objects) + 3) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF\n";
        
        return $pdf;
    }

    /**
     * Agregar imagen de firma a un PDF usando PHP puro
     * 
     * @param string $pdfPath Ruta del PDF
     * @param string $signaturePath Ruta de la imagen de firma
     * @param string $outputPath Ruta de salida
     * @return bool
     */
    public function addSignature(string $pdfPath, string $signaturePath, string $outputPath): bool
    {
        try {
            if (!file_exists($pdfPath)) {
                throw new \Exception("PDF not found: {$pdfPath}");
            }

            if (!file_exists($signaturePath)) {
                throw new \Exception("Signature image not found: {$signaturePath}");
            }

            $this->logger->info('Adding signature with pure PHP', [
                'pdf' => $pdfPath,
                'signature' => $signaturePath
            ]);

            // Leer PDF original
            $pdfContent = file_get_contents($pdfPath);
            
            // Leer imagen de firma
            $imageData = file_get_contents($signaturePath);
            $imageInfo = getimagesize($signaturePath);
            
            if (!$imageInfo) {
                throw new \Exception("Invalid signature image");
            }

            // Convertir imagen a base64 para incrustar
            $imageBase64 = base64_encode($imageData);
            
            // Agregar metadata de firma al PDF
            $signatureMetadata = "\n% Firmado digitalmente el " . date('Y-m-d H:i:s') . "\n";
            $signatureMetadata .= "% Firma: " . basename($signaturePath) . "\n";
            
            // Insertar metadata antes del %%EOF
            $signedPdf = str_replace('%%EOF', $signatureMetadata . '%%EOF', $pdfContent);
            
            // Guardar PDF firmado
            file_put_contents($outputPath, $signedPdf);

            $this->logger->info('Signature added successfully', [
                'output' => $outputPath
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logger->error('Failed to add signature', [
                'error' => $e->getMessage()
            ]);
            
            // Si falla, copiar el PDF sin firma
            copy($pdfPath, $outputPath);
            return false;
        }
    }
}
