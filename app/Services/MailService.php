<?php

namespace App\Services;

/**
 * Servicio de Email - Implementación en PHP Puro (Sin PHPMailer)
 * 
 * Envía emails usando SMTP manual o la función mail() de PHP.
 * No requiere librerías externas como PHPMailer.
 * 
 * Métodos disponibles:
 * - send(): Enviar email usando mail() de PHP
 * - sendViaSMTP(): Enviar email usando conexión SMTP manual
 * - sendFromTemplate(): Enviar email desde plantilla PHP
 * 
 * Configuración:
 * - MAIL_HOST: Servidor SMTP
 * - MAIL_PORT: Puerto SMTP (587 para TLS)
 * - MAIL_USER: Usuario SMTP
 * - MAIL_PASS: Contraseña SMTP
 * - MAIL_FROM: Email remitente
 * - MAIL_FROM_NAME: Nombre del remitente
 * 
 * @package App\Services
 */
class MailService
{
    private Logger $logger;
    private string $from;
    private string $fromName;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->from = $_ENV['MAIL_FROM'];
        $this->fromName = $_ENV['MAIL_FROM_NAME'];
    }

    /**
     * Enviar email usando mail() nativo de PHP
     * 
     * Método simple que usa la función mail() de PHP.
     * Soporta HTML y archivos adjuntos.
     * 
     * @param string $to Email del destinatario
     * @param string $subject Asunto del email
     * @param string $body Cuerpo del email (HTML)
     * @param array $attachments Rutas de archivos a adjuntar
     * @return bool true si se envió correctamente
     */
    public function send(string $to, string $subject, string $body, array $attachments = []): bool
    {
        try {
            $boundary = md5(time());
            
            // Headers
            $headers = [
                "From: {$this->fromName} <{$this->from}>",
                "Reply-To: {$this->from}",
                "MIME-Version: 1.0",
                "Content-Type: multipart/mixed; boundary=\"{$boundary}\""
            ];
            
            // Message body
            $message = "--{$boundary}\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $body . "\r\n\r\n";
            
            // Attachments
            foreach ($attachments as $file) {
                if (file_exists($file)) {
                    $filename = basename($file);
                    $content = chunk_split(base64_encode(file_get_contents($file)));
                    $mime = mime_content_type($file);
                    
                    $message .= "--{$boundary}\r\n";
                    $message .= "Content-Type: {$mime}; name=\"{$filename}\"\r\n";
                    $message .= "Content-Transfer-Encoding: base64\r\n";
                    $message .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n\r\n";
                    $message .= $content . "\r\n";
                }
            }
            
            $message .= "--{$boundary}--";
            
            // Send email
            $result = mail($to, $subject, $message, implode("\r\n", $headers));
            
            if ($result) {
                $this->logger->info('Email sent', [
                    'to' => $to,
                    'subject' => $subject
                ]);
            } else {
                $this->logger->error('Email send failed', ['to' => $to]);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            $this->logger->error('Email send error', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Enviar email desde plantilla PHP
     * 
     * Carga una plantilla desde app/Views/emails/ y la renderiza con datos.
     * 
     * @param string $to Email del destinatario
     * @param string $subject Asunto del email
     * @param string $template Nombre de la plantilla (sin .php)
     * @param array $data Datos para la plantilla
     * @return bool true si se envió correctamente
     */
    public function sendFromTemplate(string $to, string $subject, string $template, array $data): bool
    {
        ob_start();
        extract($data);
        require __DIR__ . "/../Views/emails/{$template}.php";
        $body = ob_get_clean();
        
        return $this->send($to, $subject, $body);
    }

    /**
     * Enviar email vía SMTP (implementación manual)
     * 
     * Más confiable que mail() ya que se conecta directamente al servidor SMTP.
     * Implementa el protocolo SMTP manualmente usando sockets.
     * 
     * Proceso:
     * 1. Conectar al servidor SMTP
     * 2. Iniciar TLS para conexión segura
     * 3. Autenticar con usuario y contraseña
     * 4. Enviar email
     * 5. Cerrar conexión
     * 
     * @param string $to Email del destinatario
     * @param string $subject Asunto del email
     * @param string $body Cuerpo del email (HTML)
     * @return bool true si se envió correctamente
     */
    public function sendViaSMTP(string $to, string $subject, string $body): bool
    {
        $host = $_ENV['MAIL_HOST'] ?? 'localhost';
        $port = $_ENV['MAIL_PORT'] ?? 587;
        $user = $_ENV['MAIL_USER'] ?? '';
        $pass = $_ENV['MAIL_PASS'] ?? '';
        
        try {
            $socket = fsockopen($host, $port, $errno, $errstr, 30);
            
            if (!$socket) {
                throw new \Exception("Cannot connect to SMTP server: $errstr ($errno)");
            }
            
            // SMTP conversation
            $this->smtpRead($socket, '220'); // Welcome
            $this->smtpWrite($socket, "EHLO {$host}\r\n");
            $this->smtpReadMultiline($socket, '250'); // Read all EHLO responses
            
            $this->smtpWrite($socket, "STARTTLS\r\n");
            $this->smtpRead($socket, '220');
            
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            
            $this->smtpWrite($socket, "EHLO {$host}\r\n");
            $this->smtpReadMultiline($socket, '250'); // Read all EHLO responses after TLS
            
            $this->smtpWrite($socket, "AUTH LOGIN\r\n");
            $this->smtpRead($socket, '334');
            
            $this->smtpWrite($socket, base64_encode($user) . "\r\n");
            $this->smtpRead($socket, '334');
            
            $this->smtpWrite($socket, base64_encode($pass) . "\r\n");
            $this->smtpRead($socket, '235');
            
            $this->smtpWrite($socket, "MAIL FROM: <{$this->from}>\r\n");
            $this->smtpRead($socket, '250');
            
            $this->smtpWrite($socket, "RCPT TO: <{$to}>\r\n");
            $this->smtpRead($socket, '250');
            
            $this->smtpWrite($socket, "DATA\r\n");
            $this->smtpRead($socket, '354');
            
            $message = "From: {$this->fromName} <{$this->from}>\r\n";
            $message .= "To: {$to}\r\n";
            $message .= "Subject: {$subject}\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "\r\n";
            $message .= $body . "\r\n";
            $message .= ".\r\n";
            
            $this->smtpWrite($socket, $message);
            $this->smtpRead($socket, '250');
            
            $this->smtpWrite($socket, "QUIT\r\n");
            $this->smtpRead($socket, '221');
            
            fclose($socket);
            
            $this->logger->info('Email sent via SMTP', ['to' => $to]);
            return true;
            
        } catch (\Exception $e) {
            $this->logger->error('SMTP send failed', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar email vía SMTP con imágenes embebidas
     * 
     * Similar a sendViaSMTP pero soporta imágenes embebidas usando CID (Content-ID).
     * Las imágenes se adjuntan al correo y se referencian en el HTML usando cid:nombre
     * 
     * @param string $to Email del destinatario
     * @param string $subject Asunto del email
     * @param string $body Cuerpo del email (HTML)
     * @param array $embeddedImages Array de imágenes ['cid' => 'ruta_archivo']
     * @return bool true si se envió correctamente
     */
    public function sendViaSMTPWithImages(string $to, string $subject, string $body, array $embeddedImages = [], array $attachments = []): bool
    {
        $host = $_ENV['MAIL_HOST'] ?? 'localhost';
        $port = $_ENV['MAIL_PORT'] ?? 587;
        $user = $_ENV['MAIL_USER'] ?? '';
        $pass = $_ENV['MAIL_PASS'] ?? '';
        
        try {
            $socket = fsockopen($host, $port, $errno, $errstr, 30);
            
            if (!$socket) {
                throw new \Exception("Cannot connect to SMTP server: $errstr ($errno)");
            }
            
            // SMTP conversation (igual que sendViaSMTP)
            $this->smtpRead($socket, '220');
            $this->smtpWrite($socket, "EHLO {$host}\r\n");
            $this->smtpReadMultiline($socket, '250');
            
            $this->smtpWrite($socket, "STARTTLS\r\n");
            $this->smtpRead($socket, '220');
            
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            
            $this->smtpWrite($socket, "EHLO {$host}\r\n");
            $this->smtpReadMultiline($socket, '250');
            
            $this->smtpWrite($socket, "AUTH LOGIN\r\n");
            $this->smtpRead($socket, '334');
            
            $this->smtpWrite($socket, base64_encode($user) . "\r\n");
            $this->smtpRead($socket, '334');
            
            $this->smtpWrite($socket, base64_encode($pass) . "\r\n");
            $this->smtpRead($socket, '235');
            
            $this->smtpWrite($socket, "MAIL FROM: <{$this->from}>\r\n");
            $this->smtpRead($socket, '250');
            
            $this->smtpWrite($socket, "RCPT TO: <{$to}>\r\n");
            $this->smtpRead($socket, '250');
            
            $this->smtpWrite($socket, "DATA\r\n");
            $this->smtpRead($socket, '354');
            
            // Boundaries
            $boundaryRelated = 'rel_' . md5(uniqid('', true));
            $boundaryMixed   = 'mix_' . md5(uniqid('', true));
            $hasAttachments  = !empty($attachments);

            // ── Headers ──────────────────────────────────────────────────────
            $message  = "From: {$this->fromName} <{$this->from}>\r\n";
            $message .= "To: {$to}\r\n";
            $message .= "Subject: {$subject}\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $message .= "X-Priority: 3\r\n";

            if ($hasAttachments) {
                // multipart/mixed envuelve todo cuando hay adjuntos
                $message .= "Content-Type: multipart/mixed; boundary=\"{$boundaryMixed}\"\r\n";
                $message .= "\r\n";
                $message .= "This is a multi-part message in MIME format.\r\n\r\n";
                // Abre la parte related dentro de mixed
                $message .= "--{$boundaryMixed}\r\n";
            } else {
                $message .= "Content-Type: multipart/related; type=\"text/html\"; boundary=\"{$boundaryRelated}\"\r\n";
                $message .= "\r\n";
                $message .= "This is a multi-part message in MIME format.\r\n\r\n";
            }

            // ── Parte HTML + imágenes CID (multipart/related) ─────────────
            if ($hasAttachments) {
                $message .= "Content-Type: multipart/related; type=\"text/html\"; boundary=\"{$boundaryRelated}\"\r\n";
                $message .= "\r\n";
            }

            // HTML
            $message .= "--{$boundaryRelated}\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: quoted-printable\r\n";
            $message .= "\r\n";
            $message .= quoted_printable_encode($body) . "\r\n\r\n";

            // Imágenes CID (logo, firma, etc.)
            foreach ($embeddedImages as $cid => $imagePath) {
                if (!file_exists($imagePath)) continue;
                $finfo     = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType  = finfo_file($finfo, $imagePath);
                finfo_close($finfo);
                $fileName  = ($cid === 'signature') ? 'SAGRILAFT.png' : basename($imagePath);
                $imageB64  = chunk_split(base64_encode(file_get_contents($imagePath)));

                $message .= "--{$boundaryRelated}\r\n";
                $message .= "Content-Type: {$mimeType}; name=\"{$fileName}\"\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n";
                $message .= "Content-ID: <{$cid}>\r\n";
                $message .= "Content-Disposition: inline; filename=\"{$fileName}\"\r\n";
                $message .= "\r\n";
                $message .= $imageB64 . "\r\n";
            }

            $message .= "--{$boundaryRelated}--\r\n\r\n";

            // ── Adjuntos (solo si los hay, dentro de mixed) ───────────────
            if ($hasAttachments) {
                foreach ($attachments as $attachment) {
                    if (!isset($attachment['path']) || !file_exists($attachment['path'])) continue;
                    $finfo      = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType   = finfo_file($finfo, $attachment['path']);
                    finfo_close($finfo);
                    $fileName   = $attachment['name'] ?? basename($attachment['path']);
                    $attachB64  = chunk_split(base64_encode(file_get_contents($attachment['path'])));

                    $message .= "--{$boundaryMixed}\r\n";
                    $message .= "Content-Type: {$mimeType}; name=\"{$fileName}\"\r\n";
                    $message .= "Content-Transfer-Encoding: base64\r\n";
                    $message .= "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n";
                    $message .= "\r\n";
                    $message .= $attachB64 . "\r\n";
                }
                $message .= "--{$boundaryMixed}--\r\n";
            }

            $message .= ".\r\n";
            
            $this->smtpWrite($socket, $message);
            $this->smtpRead($socket, '250');
            
            $this->smtpWrite($socket, "QUIT\r\n");
            $this->smtpRead($socket, '221');
            
            fclose($socket);
            
            $this->logger->info('Email sent via SMTP with images and attachments', [
                'to' => $to, 
                'images' => count($embeddedImages),
                'attachments' => count($attachments)
            ]);
            return true;
            
        } catch (\Exception $e) {
            $this->logger->error('SMTP send with images failed', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Escribir comando al socket SMTP
     * 
     * @param resource $socket Socket de conexión
     * @param string $command Comando SMTP a enviar
     */
    private function smtpWrite($socket, string $command): void
    {
        fwrite($socket, $command);
    }

    /**
     * Leer respuesta del socket SMTP
     * 
     * @param resource $socket Socket de conexión
     * @param string $expectedCode Código de respuesta esperado (ej: "250")
     * @return string Respuesta del servidor
     * @throws \Exception Si el código no coincide
     */
    private function smtpRead($socket, string $expectedCode): string
    {
        $response = fgets($socket, 515);
        
        if (substr($response, 0, 3) !== $expectedCode) {
            throw new \Exception("SMTP Error: Expected {$expectedCode}, got {$response}");
        }
        
        return $response;
    }

    /**
     * Leer respuesta multilínea del socket SMTP
     * 
     * Usado para comandos como EHLO que devuelven múltiples líneas.
     * Lee hasta encontrar una línea que termine con espacio (no guión).
     * 
     * @param resource $socket Socket de conexión
     * @param string $expectedCode Código de respuesta esperado
     * @return array Lista de respuestas del servidor
     * @throws \Exception Si el código no coincide
     */
    private function smtpReadMultiline($socket, string $expectedCode): array
    {
        $responses = [];
        
        while (true) {
            $response = fgets($socket, 515);
            $responses[] = $response;
            
            // Check if this is the last line (code followed by space, not hyphen)
            if (substr($response, 0, 3) === $expectedCode && substr($response, 3, 1) === ' ') {
                break;
            }
            
            // If we get an unexpected code, throw error
            if (substr($response, 0, 3) !== $expectedCode) {
                throw new \Exception("SMTP Error: Expected {$expectedCode}, got {$response}");
            }
        }
        
        return $responses;
    }
}
