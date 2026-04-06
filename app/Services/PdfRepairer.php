<?php

namespace App\Services;

/**
 * Reparador de PDFs
 * 
 * Repara PDFs con problemas de estructura para que FPDI pueda procesarlos
 */
class PdfRepairer
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Reparar un PDF corrupto
     * 
     * @param string $inputPath PDF original
     * @param string $outputPath PDF reparado
     * @return bool True si se reparó correctamente
     */
    public function repair(string $inputPath, string $outputPath): bool
    {
        try {
            $this->logger->info('Attempting to repair PDF', [
                'input' => basename($inputPath)
            ]);

            // Leer contenido del PDF
            $content = file_get_contents($inputPath);
            
            if ($content === false) {
                throw new \Exception('Failed to read PDF file');
            }

            // Reparar estructura básica
            $repairedContent = $this->repairPdfStructure($content);
            
            // Guardar PDF reparado
            file_put_contents($outputPath, $repairedContent);

            $this->logger->info('PDF repaired successfully', [
                'output' => basename($outputPath),
                'original_size' => strlen($content),
                'repaired_size' => strlen($repairedContent)
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logger->error('PDF repair failed', [
                'error' => $e->getMessage()
            ]);
            
            // Si falla la reparación, copiar el original
            copy($inputPath, $outputPath);
            return false;
        }
    }

    /**
     * Reparar estructura del PDF
     */
    private function repairPdfStructure(string $content): string
    {
        // 1. Asegurar que empieza con %PDF
        if (!preg_match('/^%PDF-/', $content)) {
            $content = "%PDF-1.4\n" . $content;
        }

        // 2. Asegurar que termina con %%EOF
        if (!preg_match('/%%EOF\s*$/', $content)) {
            $content = rtrim($content) . "\n%%EOF\n";
        }

        // 3. Reconstruir tabla de referencias cruzadas si está corrupta
        $content = $this->rebuildXrefTable($content);

        return $content;
    }

    /**
     * Reconstruir tabla de referencias cruzadas
     */
    private function rebuildXrefTable(string $content): string
    {
        // Extraer todos los objetos
        preg_match_all('/(\d+)\s+(\d+)\s+obj/s', $content, $matches, PREG_OFFSET_CAPTURE);
        
        if (empty($matches[0])) {
            return $content;
        }

        // Construir nueva tabla xref
        $xrefEntries = [];
        foreach ($matches[0] as $index => $match) {
            $objNum = (int)$matches[1][$index][0];
            $offset = $match[1];
            $xrefEntries[$objNum] = sprintf("%010d 00000 n ", $offset);
        }

        // Ordenar por número de objeto
        ksort($xrefEntries);

        // Construir nueva sección xref
        $maxObj = max(array_keys($xrefEntries));
        $newXref = "xref\n0 " . ($maxObj + 1) . "\n";
        $newXref .= "0000000000 65535 f \n";
        
        for ($i = 1; $i <= $maxObj; $i++) {
            if (isset($xrefEntries[$i])) {
                $newXref .= $xrefEntries[$i] . "\n";
            } else {
                $newXref .= "0000000000 00000 f \n";
            }
        }

        // Reemplazar xref antigua con la nueva
        $content = preg_replace('/xref\s+\d+\s+\d+\s+[\s\S]*?trailer/s', $newXref . "trailer", $content);

        return $content;
    }

    /**
     * Verificar si un PDF necesita reparación
     */
    public function needsRepair(string $pdfPath): bool
    {
        try {
            $content = file_get_contents($pdfPath);
            
            // Verificaciones básicas
            if (!preg_match('/^%PDF-/', $content)) {
                return true;
            }
            
            if (!preg_match('/%%EOF\s*$/', $content)) {
                return true;
            }
            
            // Verificar si tiene tabla xref
            if (!preg_match('/xref/', $content)) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            return true;
        }
    }
}
