<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a SAGRILAFT</title>
    <style>
        /* Emails usan colores inline para compatibilidad con clientes de correo */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #e5e7eb; background-color: #020617; padding: 20px; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background-color: rgba(15, 23, 42, 0.9); border-radius: 12px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.7); border: 1px solid rgba(30, 64, 175, 0.7); }
        .email-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 30px 20px; text-align: center; border-bottom: 2px solid rgba(56, 189, 248, 0.6); }
        .email-header h1 { color: #e5e7eb; font-size: 24px; font-weight: 600; margin: 0; }
        .email-header p { color: #cbd5e1; font-size: 14px; margin: 8px 0 0; }
        .email-body { padding: 30px 20px; background: #0f172a; }
        .status-badge { display: inline-block; padding: 12px 24px; border-radius: 999px; font-weight: 600; font-size: 16px; margin: 20px 0; background-color: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 2px solid rgba(59, 130, 246, 0.5); }
        .info-card { background-color: #1e293b; border: 1px solid rgba(148, 163, 184, 0.3); border-radius: 8px; padding: 16px; margin: 16px 0; }
        .info-row { padding: 8px 0; border-bottom: 1px solid rgba(148, 163, 184, 0.3); }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #cbd5e1; font-size: 14px; display: block; margin-bottom: 4px; }
        .info-value { color: #e5e7eb; font-size: 14px; }
        .button { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #1d4ed8, #38bdf8); color: #e5e7eb !important; text-decoration: none; border-radius: 999px; font-weight: 600; font-size: 14px; margin: 20px 0; box-shadow: 0 10px 15px -3px rgba(30, 64, 175, 0.8); }
        .email-footer { background-color: #0f172a; padding: 20px; text-align: center; border-top: 1px solid rgba(148, 163, 184, 0.3); }
        .email-footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>SAGRILAFT</h1>
        </div>
        
        <div class="email-body">
            <div style="text-align: center;">
                <div class="status-badge">Cuenta Creada</div>
            </div>
            
            <p style="font-size: 16px; color: #e5e7eb; margin-bottom: 16px;">
                Hola <strong><?= htmlspecialchars($name ?? 'Usuario') ?></strong>,
            </p>
            
            <p style="font-size: 14px; color: #cbd5e1; margin-bottom: 20px;">
                Tu cuenta ha sido creada exitosamente en el sistema SAGRILAFT. Ya puedes acceder a la plataforma con tus credenciales.
            </p>
            
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Email de acceso:</span>
                    <span class="info-value"><?= htmlspecialchars($email ?? '') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contraseña:</span>
                    <span class="info-value">La que estableciste durante el registro</span>
                </div>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="index.php?route=/login" class="button">
                    Acceder al Sistema
                </a>
            </div>
            
            <p style="font-size: 13px; color: #9ca3af; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(148, 163, 184, 0.3);">
                <strong>Nota:</strong> Si no solicitaste esta cuenta, por favor ignora este correo o contacta al administrador del sistema.
            </p>
        </div>
        
        <div class="email-footer">
            <p><strong>SAGRILAFT</strong></p>
            <p>&copy; <?= date('Y') ?> Todos los derechos reservados</p>
            <p style="margin-top: 12px; color: #94a3b8;">Este es un mensaje automático, por favor no responder a este correo.</p>
        </div>
    </div>
</body>
</html>
