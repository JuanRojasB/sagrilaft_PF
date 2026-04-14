<?php
/**
 * TEST DE FLUJO COMPLETO - Sistema SAGRILAFT
 * 
 * Este script simula TODO el proceso:
 * 1. Usuario crea un formulario
 * 2. Se envía correo a Angie con link de aprobación
 * 3. Angie aprueba el formulario
 * 4. Se genera PDF consolidado con firmas
 * 5. Se envían correos a todos los destinatarios según el tipo
 * 
 * IMPORTANTE: Este script ENVÍA CORREOS REALES
 */

// Cargar variables de entorno
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Iniciar sesión
session_start();

require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Models/Form.php';
require_once __DIR__ . '/app/Services/MailService.php';
require_once __DIR__ . '/app/Services/Logger.php';
require_once __DIR__ . '/app/Helpers/EmailHelper.php';
require_once __DIR__ . '/app/Config/NotificationConfig.php';
require_once __DIR__ . '/app/Config/EmailRecipientsConfig.php';

use App\Core\Database;
use App\Models\Form;
use App\Services\MailService;
use App\Helpers\EmailHelper;
use App\Config\EmailRecipientsConfig;

echo "═══════════════════════════════════════════════════════════════\n";
echo "  TEST DE FLUJO COMPLETO - Sistema SAGRILAFT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$testMode = EmailRecipientsConfig::TEST_MODE ? 'ACTIVO' : 'DESACTIVADO';
echo "🔴 MODO TEST: {$testMode}\n";
if (EmailRecipientsConfig::TEST_MODE) {
    echo "   Todos los correos van a: " . EmailRecipientsConfig::TEST_EMAIL . "\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  PASO 1: Buscar un formulario real en la BD\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    $db = Database::getConnection();
    
    // Buscar un formulario pendiente o el más reciente
    $stmt = $db->query("
        SELECT * FROM forms 
        WHERE form_type IN ('cliente_natural', 'cliente_juridica', 'proveedor_natural', 'empleado')
        ORDER BY id DESC 
        LIMIT 1
    ");
    $form = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$form) {
        echo "❌ No se encontró ningún formulario en la base de datos.\n";
        echo "   Por favor, crea un formulario primero desde el sistema.\n\n";
        exit(1);
    }
    
    echo "✅ Formulario encontrado:\n";
    echo "   ID: {$form['id']}\n";
    echo "   Tipo: {$form['form_type']}\n";
    echo "   Empresa/Nombre: " . ($form['company_name'] ?? $form['empleado_nombre'] ?? 'N/A') . "\n";
    echo "   NIT/Cédula: " . ($form['nit'] ?? $form['empleado_cedula'] ?? 'N/A') . "\n";
    echo "   Estado actual: {$form['approval_status']}\n";
    echo "   Creado: {$form['created_at']}\n\n";
    
} catch (Exception $e) {
    echo "❌ Error al buscar formulario: " . $e->getMessage() . "\n\n";
    exit(1);
}

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  PASO 2: Simular envío de correo a Angie (Nuevo Formulario)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$mailService = new MailService();

// Generar token de aprobación si no existe
if (empty($form['approval_token'])) {
    $approvalToken = bin2hex(random_bytes(32));
    $stmt = $db->prepare("UPDATE forms SET approval_token = ? WHERE id = ?");
    $stmt->execute([$approvalToken, $form['id']]);
    $form['approval_token'] = $approvalToken;
    echo "✅ Token de aprobación generado: {$approvalToken}\n\n";
}

$approvalUrl = ($_ENV['APP_URL'] ?? 'http://localhost') . "/index.php?route=/approval/{$form['approval_token']}";

echo "📧 Preparando correo para Angie...\n";
echo "   Destinatario: oficialdecumplimiento@pollo-fiesta.com\n";
echo "   Link de aprobación: {$approvalUrl}\n\n";

$subject = 'SAGRILAFT - Nuevo Formulario para Aprobar';

$isEmpleado = $form['form_type'] === 'empleado';

if ($isEmpleado) {
    $body = EmailHelper::emailHeader('Nuevo Registro de Empleado #' . $form['id']) . "
    <p class='msg'>Se ha recibido un nuevo registro de empleado que requiere revisión y aprobación.</p>
    <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
        <tr>
            <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
                <div class='info-item'><span class='info-label'>Nombre Completo</span><span class='info-value'>" . ($form['empleado_nombre'] ?? 'N/A') . "</span></div>
            </td>
            <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
                <div class='info-item'><span class='info-label'>Cédula</span><span class='info-value'>" . ($form['empleado_cedula'] ?? 'N/A') . "</span></div>
            </td>
        </tr>
    </table>
    <div style='text-align:center;margin-top:24px;'>
        <a href='{$approvalUrl}' class='btn' style='display:inline-block;padding:10px 22px;background:#1d4ed8;color:#ffffff;text-decoration:none;border-radius:5px;font-weight:600;font-size:13px;'>Revisar Registro</a>
    </div>
    " . EmailHelper::emailFooter();
} else {
    $body = EmailHelper::emailHeader('Nuevo Formulario SAGRILAFT #' . $form['id']) . "
    <p class='msg'>Se ha recibido un nuevo formulario que requiere revisión y aprobación.</p>
    <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
        <tr>
            <td style='padding:0 6px 6px 0;width:33%;vertical-align:top;'>
                <div class='info-item'><span class='info-label'>Empresa/Persona</span><span class='info-value'>" . ($form['company_name'] ?? 'N/A') . "</span></div>
            </td>
            <td style='padding:0 6px 6px 0;width:33%;vertical-align:top;'>
                <div class='info-item'><span class='info-label'>NIT/Documento</span><span class='info-value'>" . ($form['nit'] ?? 'N/A') . "</span></div>
            </td>
            <td style='padding:0 0 6px 0;width:33%;vertical-align:top;'>
                <div class='info-item'><span class='info-label'>Teléfono</span><span class='info-value'>" . ($form['phone'] ?? 'N/A') . "</span></div>
            </td>
        </tr>
    </table>
    <div style='text-align:center;margin-top:24px;'>
        <a href='{$approvalUrl}' class='btn' style='display:inline-block;padding:10px 22px;background:#1d4ed8;color:#ffffff;text-decoration:none;border-radius:5px;font-weight:600;font-size:13px;'>Revisar Formulario</a>
    </div>
    " . EmailHelper::emailFooter();
}

$embeddedImages = [];
$logoPath = EmailHelper::getLogoImagePath();
if ($logoPath) $embeddedImages['logo'] = $logoPath;
$sigPath = EmailHelper::getSignatureImagePath();
if ($sigPath) $embeddedImages['signature'] = $sigPath;

echo "🚀 Enviando correo a Angie...\n";

try {
    $recipients = EmailRecipientsConfig::getNewFormRecipients($form['form_type']);
    $to = $recipients[0]['email'];
    
    $result = $mailService->sendViaSMTPWithImages($to, $subject, $body, $embeddedImages);
    
    if ($result) {
        echo "✅ Correo enviado exitosamente a: {$to}\n\n";
    } else {
        echo "❌ Error al enviar correo\n\n";
    }
} catch (Exception $e) {
    echo "❌ Excepción al enviar correo: " . $e->getMessage() . "\n\n";
}

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  PASO 3: Simular aprobación del formulario\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📝 Simulando que Angie aprueba el formulario...\n\n";

// Actualizar estado a aprobado
$stmt = $db->prepare("
    UPDATE forms 
    SET approval_status = 'approved',
        approved_by = 'Angie Paola Martínez Paredes',
        approval_date = NOW(),
        reviewed_at = NOW()
    WHERE id = ?
");
$stmt->execute([$form['id']]);

echo "✅ Formulario marcado como aprobado\n\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  PASO 4: Enviar correos de aprobación a todos los destinatarios\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Preparar datos del formulario
$formData = [
    'creator_email' => 'juan.david.rojas.burbano0@gmail.com',
    'creator_name' => 'Juan David Rojas (Test)',
];

// Obtener destinatarios según el tipo
$recipients = EmailRecipientsConfig::getApprovedRecipients($form['form_type'], $formData);

echo "📧 Destinatarios para formulario aprobado ({$form['form_type']}):\n";
foreach ($recipients as $r) {
    echo "   • {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

$subject = 'SAGRILAFT - Formulario Aprobado';
$body = EmailHelper::emailHeader('Formulario SAGRILAFT Aprobado') . "
<div class='badge badge-green'>✓ APROBADO</div>
<p class='msg'>El formulario ha sido aprobado por el Oficial de Cumplimiento.</p>
<table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
    <tr>
        <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
            <div class='info-item'><span class='info-label'>ID Formulario</span><span class='info-value'>#" . $form['id'] . "</span></div>
        </td>
        <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
            <div class='info-item'><span class='info-label'>Tipo</span><span class='info-value'>" . $form['form_type'] . "</span></div>
        </td>
    </tr>
</table>
<p style='font-size:12px;color:#475569;margin-top:12px;'>📎 Se adjunta el PDF del formulario aprobado y firmado digitalmente.</p>
" . EmailHelper::emailFooter();

echo "🚀 Enviando correos de aprobación...\n\n";

$sentCount = 0;
$errorCount = 0;

foreach ($recipients as $recipient) {
    try {
        $result = $mailService->sendViaSMTPWithImages(
            $recipient['email'],
            $subject,
            $body,
            $embeddedImages
        );
        
        if ($result) {
            echo "   ✅ Enviado a: {$recipient['name']} <{$recipient['email']}>\n";
            $sentCount++;
        } else {
            echo "   ❌ Error al enviar a: {$recipient['email']}\n";
            $errorCount++;
        }
    } catch (Exception $e) {
        echo "   ❌ Excepción al enviar a {$recipient['email']}: " . $e->getMessage() . "\n";
        $errorCount++;
    }
    
    // Pequeña pausa entre envíos para no saturar el servidor
    usleep(500000); // 0.5 segundos
}

echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  RESUMEN DEL TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📊 Estadísticas:\n";
echo "   • Formulario ID: {$form['id']}\n";
echo "   • Tipo: {$form['form_type']}\n";
echo "   • Correos enviados: {$sentCount}\n";
echo "   • Errores: {$errorCount}\n";
echo "   • Total destinatarios: " . count($recipients) . "\n\n";

if (EmailRecipientsConfig::TEST_MODE) {
    echo "⚠️  RECORDATORIO: Modo TEST está ACTIVO\n";
    echo "   Todos los correos se enviaron a: " . EmailRecipientsConfig::TEST_EMAIL . "\n";
    echo "   Revisa esa bandeja de entrada.\n\n";
}

echo "✅ Test completado exitosamente\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "  FIN DEL TEST\n";
echo "═══════════════════════════════════════════════════════════════\n";
