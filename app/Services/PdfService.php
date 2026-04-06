<?php

namespace App\Services;

/**
 * Servicio de Generación de PDF - Sin librerías externas
 * 
 * Genera archivos PDF usando la estructura básica de PDF 1.4.
 * Implementación en PHP puro, sin dependencias como FPDF, TCPDF o DomPDF.
 * 
 * Características:
 * - Genera PDFs válidos con estructura PDF 1.4
 * - Diseño profesional con gradientes y colores
 * - Incluye header, contenido, metadata y footer
 * - Descarga automática del archivo
 * 
 * Estructura del PDF:
 * 1. Header: %PDF-1.4
 * 2. Objetos: Catálogo, Páginas, Recursos, Contenido
 * 3. Tabla xref: Referencias a objetos
 * 4. Trailer: Información del documento
 * 
 * Limitaciones:
 * - No soporta imágenes (requiere extensión GD)
 * - Fuentes limitadas a Helvetica y Helvetica-Bold
 * - Una sola página por documento
 * 
 * @package App\Services
 */
class PdfService
{
    private string $storagePath;
    private array $offsets = [];

    public function __construct()
    {
        $this->storagePath = __DIR__ . '/../../storage/pdf/';
        
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Generar PDF desde datos del formulario
     * 
     * Crea un archivo PDF con diseño profesional que incluye:
     * - Header azul con título SAGRILAFT
     * - Título del formulario en caja gris con borde azul
     * - Metadata (ID, fecha, estado)
     * - Contenido del formulario
     * - Información detallada en tabla
     * - Footer con fecha de generación
     * 
     * El PDF se descarga automáticamente al navegador.
     * 
     * @param array $form Datos del formulario
     * @param string $filename Nombre del archivo PDF
     * @return void
     */
    public function generateFormPdf(array $form, string $filename): void
    {
        $pdf = '';
        $this->offsets = [];
        
        // Header PDF
        $pdf .= "%PDF-1.4\n";
        $pdf .= "%âãÏÓ\n";
        
        // Objeto 1: Catálogo
        $this->offsets[1] = strlen($pdf);
        $pdf .= "1 0 obj\n";
        $pdf .= "<< /Type /Catalog /Pages 2 0 R >>\n";
        $pdf .= "endobj\n";
        
        // Objeto 2: Páginas
        $this->offsets[2] = strlen($pdf);
        $pdf .= "2 0 obj\n";
        $pdf .= "<< /Type /Pages /Kids [3 0 R] /Count 1 >>\n";
        $pdf .= "endobj\n";
        
        // Objeto 3: Página
        $this->offsets[3] = strlen($pdf);
        $pdf .= "3 0 obj\n";
        $pdf .= "<< /Type /Page /Parent 2 0 R /Resources 4 0 R /MediaBox [0 0 612 792] /Contents 5 0 R >>\n";
        $pdf .= "endobj\n";
        
        // Objeto 4: Recursos (Fuentes)
        $this->offsets[4] = strlen($pdf);
        $pdf .= "4 0 obj\n";
        $pdf .= "<< /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> /F2 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >> >> >>\n";
        $pdf .= "endobj\n";
        
        // Generar contenido
        $content = $this->generateContent($form);
        
        // Objeto 5: Contenido de la página
        $this->offsets[5] = strlen($pdf);
        $pdf .= "5 0 obj\n";
        $pdf .= "<< /Length " . strlen($content) . " >>\n";
        $pdf .= "stream\n";
        $pdf .= $content;
        $pdf .= "\nendstream\n";
        $pdf .= "endobj\n";
        
        // Tabla xref
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= "0 6\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= 5; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $this->offsets[$i]);
        }
        
        // Trailer
        $pdf .= "trailer\n";
        $pdf .= "<< /Size 6 /Root 1 0 R >>\n";
        $pdf .= "startxref\n";
        $pdf .= $xrefOffset . "\n";
        $pdf .= "%%EOF\n";
        
        // Enviar PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $pdf;
        exit;
    }

    /**
     * Generar contenido del PDF
     * 
     * Crea el stream de contenido con comandos PDF para dibujar:
     * - Rectángulos de colores (header, cajas)
     * - Texto con diferentes fuentes y tamaños
     * - Líneas y bordes
     * 
     * Comandos PDF usados:
     * - BT/ET: Begin/End Text
     * - Tf: Set Font
     * - Td: Text Position
     * - Tj: Show Text
     * - rg/RG: Set Color (fill/stroke)
     * - re: Rectangle
     * - f: Fill
     * - S: Stroke
     * - m/l: Move/Line
     * 
     * @param array $form Datos del formulario
     * @return string Contenido del PDF en formato stream
     */
    private function generateContent(array $form): string
    {
        $content = '';
        $y = 730;
        
        // Logo/Título simple
        $content .= "BT\n";
        $content .= "0 0 0 rg\n";
        $content .= "/F2 20 Tf\n";
        $content .= "50 " . $y . " Td\n";
        $content .= "(" . $this->escape("SAGRILAFT") . ") Tj\n";
        $content .= "ET\n";
        
        $y -= 25;
        
        // Línea separadora
        $content .= "0.8 0.8 0.8 RG\n";
        $content .= "1 w\n";
        $content .= "50 " . $y . " m 562 " . $y . " l S\n";
        
        $y -= 25;
        
        // Título del formulario (sin "Formulario SAGRILAFT -")
        $content .= "BT\n";
        $content .= "0 0 0 rg\n";
        $content .= "/F2 13 Tf\n";
        $content .= "50 " . $y . " Td\n";
        $titleText = str_replace('Formulario SAGRILAFT - ', '', $form['title']);
        $titleText = $this->truncate($titleText, 60);
        $content .= "(" . $this->escape($titleText) . ") Tj\n";
        $content .= "ET\n";
        
        $y -= 18;
        
        // Metadata
        $content .= "BT\n";
        $content .= "0.4 0.4 0.4 rg\n";
        $content .= "/F1 8 Tf\n";
        $content .= "50 " . $y . " Td\n";
        $content .= "(" . $this->escape("ID: #" . $form['id']) . ") Tj\n";
        $content .= "ET\n";
        
        $content .= "BT\n";
        $content .= "0.4 0.4 0.4 rg\n";
        $content .= "/F1 8 Tf\n";
        $content .= "150 " . $y . " Td\n";
        $fecha = date('d/m/Y H:i', strtotime($form['created_at']));
        $content .= "(" . $this->escape("Creado: " . $fecha) . ") Tj\n";
        $content .= "ET\n";
        
        // Estado
        if (isset($form['approval_status'])) {
            $estado = $form['approval_status'] === 'approved' ? 'APROBADO' : 
                     ($form['approval_status'] === 'rejected' ? 'RECHAZADO' : 'PENDIENTE');
            
            $content .= "BT\n";
            $content .= "0 0 0 rg\n";
            $content .= "/F2 8 Tf\n";
            $content .= "350 " . $y . " Td\n";
            $content .= "(" . $this->escape("Estado: " . $estado) . ") Tj\n";
            $content .= "ET\n";
        }
        
        $y -= 28;
        
        // Caja: Empresa/Persona
        if (!empty($form['company_name'])) {
            // Fondo gris claro
            $content .= "q\n";
            $content .= "0.97 0.97 0.97 rg\n";
            $content .= "50 " . ($y - 28) . " 512 32 re f\n";
            $content .= "Q\n";
            
            // Borde
            $content .= "0.8 0.8 0.8 RG\n";
            $content .= "0.5 w\n";
            $content .= "50 " . ($y - 28) . " 512 32 re S\n";
            
            // Label
            $content .= "BT\n";
            $content .= "0.5 0.5 0.5 rg\n";
            $content .= "/F2 7 Tf\n";
            $content .= "55 " . ($y - 9) . " Td\n";
            $content .= "(" . $this->escape("EMPRESA/PERSONA") . ") Tj\n";
            $content .= "ET\n";
            
            // Valor
            $content .= "BT\n";
            $content .= "0 0 0 rg\n";
            $content .= "/F1 9 Tf\n";
            $content .= "55 " . ($y - 21) . " Td\n";
            $content .= "(" . $this->escape($form['company_name']) . ") Tj\n";
            $content .= "ET\n";
            
            $y -= 36;
        }
        
        // Cajas: NIT/Documento y Teléfono (lado a lado)
        if (!empty($form['nit']) || !empty($form['phone'])) {
            // Caja NIT
            if (!empty($form['nit'])) {
                $content .= "q\n";
                $content .= "0.97 0.97 0.97 rg\n";
                $content .= "50 " . ($y - 28) . " 250 32 re f\n";
                $content .= "Q\n";
                
                $content .= "0.8 0.8 0.8 RG\n";
                $content .= "0.5 w\n";
                $content .= "50 " . ($y - 28) . " 250 32 re S\n";
                
                $content .= "BT\n";
                $content .= "0.5 0.5 0.5 rg\n";
                $content .= "/F2 7 Tf\n";
                $content .= "55 " . ($y - 9) . " Td\n";
                $content .= "(" . $this->escape("NIT/DOCUMENTO") . ") Tj\n";
                $content .= "ET\n";
                
                $content .= "BT\n";
                $content .= "0 0 0 rg\n";
                $content .= "/F1 9 Tf\n";
                $content .= "55 " . ($y - 21) . " Td\n";
                $content .= "(" . $this->escape($form['nit']) . ") Tj\n";
                $content .= "ET\n";
            }
            
            // Caja Teléfono
            if (!empty($form['phone'])) {
                $content .= "q\n";
                $content .= "0.97 0.97 0.97 rg\n";
                $content .= "312 " . ($y - 28) . " 250 32 re f\n";
                $content .= "Q\n";
                
                $content .= "0.8 0.8 0.8 RG\n";
                $content .= "0.5 w\n";
                $content .= "312 " . ($y - 28) . " 250 32 re S\n";
                
                $content .= "BT\n";
                $content .= "0.5 0.5 0.5 rg\n";
                $content .= "/F2 7 Tf\n";
                $content .= "317 " . ($y - 9) . " Td\n";
                $content .= "(" . $this->escape("TELEFONO") . ") Tj\n";
                $content .= "ET\n";
                
                $content .= "BT\n";
                $content .= "0 0 0 rg\n";
                $content .= "/F1 9 Tf\n";
                $content .= "317 " . ($y - 21) . " Td\n";
                $content .= "(" . $this->escape($form['phone']) . ") Tj\n";
                $content .= "ET\n";
            }
            
            $y -= 36;
        }
        
        // Caja: Dirección
        if (!empty($form['address'])) {
            $content .= "q\n";
            $content .= "0.97 0.97 0.97 rg\n";
            $content .= "50 " . ($y - 28) . " 512 32 re f\n";
            $content .= "Q\n";
            
            $content .= "0.8 0.8 0.8 RG\n";
            $content .= "0.5 w\n";
            $content .= "50 " . ($y - 28) . " 512 32 re S\n";
            
            $content .= "BT\n";
            $content .= "0.5 0.5 0.5 rg\n";
            $content .= "/F2 7 Tf\n";
            $content .= "55 " . ($y - 9) . " Td\n";
            $content .= "(" . $this->escape("DIRECCION") . ") Tj\n";
            $content .= "ET\n";
            
            $content .= "BT\n";
            $content .= "0 0 0 rg\n";
            $content .= "/F1 9 Tf\n";
            $content .= "55 " . ($y - 21) . " Td\n";
            $addressText = $this->truncate($form['address'], 70);
            $content .= "(" . $this->escape($addressText) . ") Tj\n";
            $content .= "ET\n";
            
            $y -= 36;
        }
        
        // Caja: Actividad Económica (más alta para texto largo)
        if (!empty($form['activity'])) {
            $boxHeight = 45;
            
            $content .= "q\n";
            $content .= "0.97 0.97 0.97 rg\n";
            $content .= "50 " . ($y - $boxHeight) . " 512 " . $boxHeight . " re f\n";
            $content .= "Q\n";
            
            $content .= "0.8 0.8 0.8 RG\n";
            $content .= "0.5 w\n";
            $content .= "50 " . ($y - $boxHeight) . " 512 " . $boxHeight . " re S\n";
            
            $content .= "BT\n";
            $content .= "0.5 0.5 0.5 rg\n";
            $content .= "/F2 7 Tf\n";
            $content .= "55 " . ($y - 9) . " Td\n";
            $content .= "(" . $this->escape("ACTIVIDAD ECONOMICA") . ") Tj\n";
            $content .= "ET\n";
            
            $content .= "BT\n";
            $content .= "0 0 0 rg\n";
            $content .= "/F1 8 Tf\n";
            $content .= "55 " . ($y - 20) . " Td\n";
            
            $lines = $this->wrapText($form['activity'], 85);
            foreach ($lines as $i => $line) {
                if ($i > 0) {
                    $content .= "0 -10 Td\n";
                }
                $content .= "(" . $this->escape($line) . ") Tj\n";
                if ($i >= 2) {
                    break;
                }
            }
            $content .= "ET\n";
            
            $y -= ($boxHeight + 8);
        }
        
        // Caja de observaciones (sin título separado)
        $boxHeight = 100;
        
        $content .= "q\n";
        $content .= "0.97 0.97 0.97 rg\n";
        $content .= "50 " . ($y - $boxHeight) . " 512 " . $boxHeight . " re f\n";
        $content .= "Q\n";
        
        $content .= "0.8 0.8 0.8 RG\n";
        $content .= "0.5 w\n";
        $content .= "50 " . ($y - $boxHeight) . " 512 " . $boxHeight . " re S\n";
        
        // Label
        $content .= "BT\n";
        $content .= "0.5 0.5 0.5 rg\n";
        $content .= "/F2 7 Tf\n";
        $content .= "55 " . ($y - 9) . " Td\n";
        $content .= "(" . $this->escape("OBSERVACIONES") . ") Tj\n";
        $content .= "ET\n";
        
        // Contenido
        $content .= "BT\n";
        $content .= "0 0 0 rg\n";
        $content .= "/F1 8 Tf\n";
        $content .= "55 " . ($y - 20) . " Td\n";
        
        $lines = $this->wrapText($form['content'], 85);
        foreach ($lines as $i => $line) {
            if ($i > 0) {
                $content .= "0 -10 Td\n";
            }
            $content .= "(" . $this->escape($line) . ") Tj\n";
            if ($i >= 7) {
                break;
            }
        }
        $content .= "ET\n";
        
        $y -= ($boxHeight + 10);
        
        // Footer
        $content .= "q\n";
        $content .= "0.231 0.510 0.965 RG\n";
        $content .= "2 w\n";
        $content .= "45 90 m 567 90 l S\n";
        $content .= "Q\n";
        
        $content .= "BT\n";
        $content .= "0.4 0.46 0.54 rg\n";
        $content .= "/F2 9 Tf\n";
        $content .= "45 70 Td\n";
        $content .= "(" . $this->escape("Documento generado por el Sistema SAGRILAFT") . ") Tj\n";
        $content .= "ET\n";
        
        $content .= "BT\n";
        $content .= "0.4 0.46 0.54 rg\n";
        $content .= "/F1 8 Tf\n";
        $content .= "45 55 Td\n";
        date_default_timezone_set('America/Bogota');
        $fechaGeneracion = date('d/m/Y H:i');
        $content .= "(" . $this->escape("Fecha de generacion: " . $fechaGeneracion) . ") Tj\n";
        $content .= "ET\n";
        
        $content .= "BT\n";
        $content .= "0.5 0.56 0.64 rg\n";
        $content .= "/F1 7 Tf\n";
        $content .= "45 40 Td\n";
        $content .= "(" . $this->escape("SAGRILAFT") . ") Tj\n";
        $content .= "ET\n";
        
        return $content;
    }

    /**
     * Dividir texto en líneas
     * 
     * Usa wordwrap para dividir texto largo en múltiples líneas.
     * 
     * @param string $text Texto a dividir
     * @param int $width Ancho máximo en caracteres
     * @return array Array de líneas
     */
    private function wrapText(string $text, int $width): array
    {
        return explode("\n", wordwrap($text, $width, "\n", true));
    }

    /**
     * Truncar texto largo
     * 
     * Corta el texto si excede la longitud máxima y agrega "...".
     * 
     * @param string $text Texto a truncar
     * @param int $length Longitud máxima
     * @return string Texto truncado
     */
    private function truncate(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - 3) . '...';
    }

    /**
     * Escapar caracteres especiales para PDF
     * 
     * Escapa caracteres que tienen significado especial en PDF:
     * - \ (backslash)
     * - ( (paréntesis izquierdo)
     * - ) (paréntesis derecho)
     * 
     * También convierte de UTF-8 a ISO-8859-1 para compatibilidad.
     * 
     * @param string $text Texto a escapar
     * @return string Texto escapado
     */
    private function escape(string $text): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
        return $text;
    }
}
