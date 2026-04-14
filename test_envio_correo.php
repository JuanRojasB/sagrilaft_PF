<?php
/**
 * TEST DE ENVÍO REAL DE CORREO
 * 
 * Este script ENVÍA UN CORREO REAL a pasantesistemas1@pollo-fiesta.com
 * para verificar que el servidor SMTP funciona correctamente.
 */

// Cargar variables de entorno
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

require_once __DIR__ . '/app/Services/MailService.php';
require_once __DIR__ . '/app/Services/Logger.php';
require_once __DIR__ . '/app/Helpers/EmailHelper.php';

use App\Services\MailService;
use App\Helpers\EmailHelper;

echo "═══════════════════════════════════════════════════════════════\n";
echo "  TEST DE ENVÍO REAL DE CORREO - Sistema SAGRILAFT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Configuración SMTP
echo "📧 Configuración SMTP:\n";
echo "   Host: " . ($_ENV['MAIL_HOST'] ?? 'No configurado') . "\n";
echo "   Puerto: " . ($_ENV['MAIL_PORT'] ?? 'No configurado') . "\n";
echo "   Usuario: " . ($_ENV['MAIL_USER'] ?? 'No configurado') . "\n";
echo "   De: " . ($_ENV['MAIL_FROM'] ?? 'No configurado') . "\n";
echo "   Nombre: " . ($_ENV['MAIL_FROM_NAME'] ?? 'No configurado') . "\n\n";

// Destinatario
$destinatario = 'pasantesistemas1@pollo-fiesta.com';
echo "📬 Destinatario: {$destinatario}\n\n";

// Crear servicio de correo
$mailService = new MailService();

// Preparar contenido del correo
$subject = 'TEST - Sistema SAGRILAFT - ' . date('Y-m-d H:i:s');

$body = EmailHelper::emailHeader('TEST DE CORREO - Sistema SAGRILAFT') . "
<div class='badge badge-blue'>✓ PRUEBA DE ENVÍO</div>
<p class='msg'>Este es un correo de prueba para verificar que el sistema de envío de correos funciona correctamente.</p>

<table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
    <tr>
        <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
            <div class='info-item'><span class='info-label'>Fecha y Hora</span><span class='info-value'>" . date('d/m/Y H:i:s') . "</span></div>
        </td>
        <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
            <div class='info-item'><span class='info-label'>Servidor</span><span class='info-value'>" . ($_ENV['MAIL_HOST'] ?? 'N/A') . "</span></div>
        </td>
    </tr>
    <tr>
        <td colspan='2' style='padding:0 0 6px 0;'>
            <div class='info-item'><span class='info-label'>Remitente</span><span class='info-value'>" . ($_ENV['MAIL_FROM'] ?? 'N/A') . "</span></div>
        </td>
    </tr>
</table>

<div class='obs-box'>
    <strong>Información del Test</strong>
    <p>Si recibes este correo, significa que:</p>
    <ul style='margin:8px 0; padding-left:20px; color:#fde68a;'>
        <li>✅ La configuración SMTP es correcta</li>
        <li>✅ El servidor puede enviar correos</li>
        <li>✅ Las credenciales son válidas</li>
        <li>✅ El sistema está listo para producción</li>
    </ul>
</div>

<p style='font-size:12px;color:#475569;margin-top:12px;'>Este es un correo de prueba automático del sistema SAGRILAFT.</p>
" . EmailHelper::emailFooter();

// Preparar imágenes embebidas
$embeddedImages = [];
$logoPath = EmailHelper::getLogoImagePath();
if ($logoPath) {
    $embeddedImages['logo'] = $logoPath;
}
$sigPath = EmailHelper::getSignatureImagePath();
if ($sigPath) {
    $embeddedImages['signature'] = $sigPath;
}

echo "🚀 Enviando correo de prueba...\n\n";

try {
    $result = $mailService->sendViaSMTPWithImages(
        $destinatario,
        $subject,
        $body,
        $embeddedImages
    );
    
    if ($result) {
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  ✅ CORREO ENVIADO EXITOSAMENTE\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";
        echo "📬 Revisa la bandeja de entrada de: {$destinatario}\n";
        echo "📧 Asunto: {$subject}\n\n";
        echo "Si no lo ves en la bandeja principal, revisa:\n";
        echo "  • Carpeta de Spam/Correo no deseado\n";
        echo "  • Carpeta de Promociones\n";
        echo "  • Puede tardar 1-2 minutos en llegar\n\n";
    } else {
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  ❌ ERROR AL ENVIAR CORREO\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";
        echo "El correo no pudo ser enviado. Verifica:\n";
        echo "  • Configuración SMTP en .env\n";
        echo "  • Credenciales de correo\n";
        echo "  • Conexión a internet\n";
        echo "  • Firewall/antivirus\n\n";
    }
} catch (\Exception $e) {
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  ❌ EXCEPCIÓN AL ENVIAR CORREO\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Verifica:\n";
    echo "  • Configuración SMTP en .env\n";
    echo "  • Credenciales de correo\n";
    echo "  • Puerto 587 abierto\n";
    echo "  • Extensión OpenSSL habilitada en PHP\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "  FIN DEL TEST\n";
echo "═══════════════════════════════════════════════════════════════\n";
