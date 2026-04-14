<?php
/**
 * TEST DE FLUJO COMPLETO - Sistema SAGRILAFT
 * 
 * Este script simula TODO el proceso usando el sistema real
 */

// Cargar variables de entorno del .env (priorizar .env.local si existe)
$envFile = __DIR__ . '/../.env.local';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/../.env';
}

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $_ENV[$name] = $value;
        putenv("{$name}={$value}");
    }
}

// Cargar el sistema completo
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/Form.php';
require_once __DIR__ . '/../app/Services/MailService.php';
require_once __DIR__ . '/../app/Services/Logger.php';
require_once __DIR__ . '/../app/Helpers/EmailHelper.php';
require_once __DIR__ . '/../app/Config/NotificationConfig.php';
require_once __DIR__ . '/../app/Config/EmailRecipientsConfig.php';

use App\Core\Database;
use App\Models\Form;
use App\Services\MailService;
use App\Helpers\EmailHelper;
use App\Config\EmailRecipientsConfig;

session_start();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Flujo Completo - SAGRILAFT</title>
    <style>
        body { font-family: 'Courier New', monospace; background: #0f172a; color: #e2e8f0; padding: 20px; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; background: #1e293b; padding: 30px; border-radius: 8px; border: 1px solid #334155; }
        h1 { color: #60a5fa; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        h2 { color: #fbbf24; margin-top: 30px; border-left: 4px solid #f59e0b; padding-left: 15px; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        .info { color: #60a5fa; }
        .warning { color: #fbbf24; }
        pre { background: #0f172a; padding: 15px; border-radius: 4px; overflow-x: auto; border: 1px solid #334155; }
        .step { background: #334155; padding: 15px; margin: 15px 0; border-radius: 4px; border-left: 4px solid #3b82f6; }
        .recipient { padding: 8px; margin: 5px 0; background: #1e293b; border-left: 3px solid #4ade80; }
        .stat { display: inline-block; padding: 8px 15px; margin: 5px; background: #334155; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧪 TEST DE FLUJO COMPLETO - Sistema SAGRILAFT</h1>
    
    <?php
    $testMode = EmailRecipientsConfig::TEST_MODE ? 'ACTIVO' : 'DESACTIVADO';
    echo "<p class='warning'>🔴 MODO TEST: <strong>{$testMode}</strong></p>";
    if (EmailRecipientsConfig::TEST_MODE) {
        echo "<p class='info'>Todos los correos van a: <strong>" . EmailRecipientsConfig::TEST_EMAIL . "</strong></p>";
    }
    ?>
    
    <h2>📋 PASO 1: Buscar formulario real en la BD</h2>
    <div class="step">
    <?php
    try {
        $db = Database::getConnection();
        
        $stmt = $db->query("
            SELECT * FROM forms 
            WHERE form_type IN ('cliente_natural', 'cliente_juridica', 'proveedor_natural', 'empleado')
            ORDER BY id DESC 
            LIMIT 1
        ");
        $form = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$form) {
            echo "<p class='error'>❌ No se encontró ningún formulario en la base de datos.</p>";
            echo "<p>Por favor, crea un formulario primero desde el sistema.</p>";
            exit;
        }
        
        echo "<p class='success'>✅ Formulario encontrado:</p>";
        echo "<pre>";
        echo "ID: {$form['id']}\n";
        echo "Tipo: {$form['form_type']}\n";
        echo "Empresa/Nombre: " . ($form['company_name'] ?? $form['empleado_nombre'] ?? 'N/A') . "\n";
        echo "NIT/Cédula: " . ($form['nit'] ?? $form['empleado_cedula'] ?? 'N/A') . "\n";
        echo "Estado actual: {$form['approval_status']}\n";
        echo "Creado: {$form['created_at']}";
        echo "</pre>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error al buscar formulario: " . htmlspecialchars($e->getMessage()) . "</p>";
        exit;
    }
    ?>
    </div>
    
    <h2>📧 PASO 2: Enviar correo a Angie (Nuevo Formulario)</h2>
    <div class="step">
    <?php
    $mailService = new MailService();
    
    // Generar token si no existe
    if (empty($form['approval_token'])) {
        $approvalToken = bin2hex(random_bytes(32));
        $stmt = $db->prepare("UPDATE forms SET approval_token = ? WHERE id = ?");
        $stmt->execute([$approvalToken, $form['id']]);
        $form['approval_token'] = $approvalToken;
        echo "<p class='success'>✅ Token de aprobación generado</p>";
    }
    
    $approvalUrl = ($_ENV['APP_URL'] ?? 'http://localhost') . "/index.php?route=/approval/{$form['approval_token']}";
    
    echo "<p class='info'>📬 Destinatario: oficialdecumplimiento@pollo-fiesta.com</p>";
    echo "<p class='info'>🔗 Link: <a href='{$approvalUrl}' target='_blank' style='color:#60a5fa;'>{$approvalUrl}</a></p>";
    
    $subject = 'SAGRILAFT - Nuevo Formulario para Aprobar';
    $isEmpleado = $form['form_type'] === 'empleado';
    
    if ($isEmpleado) {
        $body = EmailHelper::emailHeader('Nuevo Registro de Empleado #' . $form['id']) . "
        <p class='msg'>Se ha recibido un nuevo registro de empleado que requiere revisión y aprobación.</p>
        <div style='text-align:center;margin-top:24px;'>
            <a href='{$approvalUrl}' class='btn'>Revisar Registro</a>
        </div>
        " . EmailHelper::emailFooter();
    } else {
        $body = EmailHelper::emailHeader('Nuevo Formulario SAGRILAFT #' . $form['id']) . "
        <p class='msg'>Se ha recibido un nuevo formulario que requiere revisión y aprobación.</p>
        <div style='text-align:center;margin-top:24px;'>
            <a href='{$approvalUrl}' class='btn'>Revisar Formulario</a>
        </div>
        " . EmailHelper::emailFooter();
    }
    
    $embeddedImages = [];
    $logoPath = EmailHelper::getLogoImagePath();
    if ($logoPath) $embeddedImages['logo'] = $logoPath;
    $sigPath = EmailHelper::getSignatureImagePath();
    if ($sigPath) $embeddedImages['signature'] = $sigPath;
    
    echo "<p>🚀 Enviando correo...</p>";
    
    try {
        $recipients = EmailRecipientsConfig::getNewFormRecipients($form['form_type']);
        $to = $recipients[0]['email'];
        
        $result = $mailService->sendViaSMTPWithImages($to, $subject, $body, $embeddedImages);
        
        if ($result) {
            echo "<p class='success'>✅ Correo enviado exitosamente a: <strong>{$to}</strong></p>";
        } else {
            echo "<p class='error'>❌ Error al enviar correo</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Excepción: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
    </div>
    
    <h2>✓ PASO 3: Simular aprobación del formulario</h2>
    <div class="step">
    <?php
    echo "<p>📝 Marcando formulario como aprobado...</p>";
    
    $stmt = $db->prepare("
        UPDATE forms 
        SET approval_status = 'approved',
            approved_by = 'Angie Paola Martínez Paredes',
            approval_date = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$form['id']]);
    
    echo "<p class='success'>✅ Formulario #{$form['id']} marcado como aprobado</p>";
    ?>
    </div>
    
    <h2>📬 PASO 4: Enviar correos de aprobación</h2>
    <div class="step">
    <?php
    $formData = [
        'creator_email' => 'juan.david.rojas.burbano0@gmail.com',
        'creator_name' => 'Juan David Rojas (Test)',
    ];
    
    $recipients = EmailRecipientsConfig::getApprovedRecipients($form['form_type'], $formData);
    
    echo "<p class='info'>📧 Destinatarios para formulario aprobado ({$form['form_type']}):</p>";
    foreach ($recipients as $r) {
        echo "<div class='recipient'>• {$r['name']} &lt;{$r['email']}&gt; [{$r['type']}]</div>";
    }
    
    $subject = 'SAGRILAFT - Formulario Aprobado';
    $body = EmailHelper::emailHeader('Formulario SAGRILAFT Aprobado') . "
    <div class='badge badge-green'>✓ APROBADO</div>
    <p class='msg'>El formulario ha sido aprobado por el Oficial de Cumplimiento.</p>
    <p style='font-size:12px;color:#475569;margin-top:12px;'>📎 Se adjunta el PDF del formulario aprobado y firmado digitalmente.</p>
    " . EmailHelper::emailFooter();
    
    echo "<p>🚀 Enviando correos...</p>";
    
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
                echo "<p class='success'>✅ Enviado a: {$recipient['name']}</p>";
                $sentCount++;
            } else {
                echo "<p class='error'>❌ Error al enviar a: {$recipient['email']}</p>";
                $errorCount++;
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Excepción: " . htmlspecialchars($e->getMessage()) . "</p>";
            $errorCount++;
        }
        
        usleep(500000); // 0.5 segundos entre envíos
    }
    ?>
    </div>
    
    <h2>📊 RESUMEN DEL TEST</h2>
    <div class="step">
        <div class="stat">📝 Formulario ID: <strong><?= $form['id'] ?></strong></div>
        <div class="stat">📋 Tipo: <strong><?= $form['form_type'] ?></strong></div>
        <div class="stat">✅ Enviados: <strong><?= $sentCount ?></strong></div>
        <div class="stat">❌ Errores: <strong><?= $errorCount ?></strong></div>
        <div class="stat">📧 Total: <strong><?= count($recipients) ?></strong></div>
        
        <?php if (EmailRecipientsConfig::TEST_MODE): ?>
        <p class='warning' style='margin-top:20px;'>
            ⚠️ RECORDATORIO: Modo TEST está ACTIVO<br>
            Todos los correos se enviaron a: <strong><?= EmailRecipientsConfig::TEST_EMAIL ?></strong><br>
            Revisa esa bandeja de entrada.
        </p>
        <?php endif; ?>
        
        <p class='success' style='margin-top:20px;font-size:18px;'>✅ Test completado exitosamente</p>
    </div>
</div>
</body>
</html>
