<?php
/**
 * PREVIEW DE TODOS LOS EMAILS DEL SISTEMA
 * Acceder en: /gestion-sagrilaft/public/email_preview.php
 * SOLO PARA DESARROLLO
 */

$email = $_GET['email'] ?? 'index';

$BASE = '/gestion-sagrilaft/public';
$LOGO = $BASE . '/assets/img/orb-logo.png';
$FIRMA = $BASE . '/assets/img/correo_info_angie.png';

// ── Header y footer comunes a todos los emails ────────────────────────────────
function emailHeader(string $title, string $BASE, string $LOGO): string {
    return "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width,initial-scale=1.0'>
        <title>{$title}</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box;}
            body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;line-height:1.5;color:#e2e8f0;background:#020617;padding:20px;}
            .wrapper{max-width:640px;margin:0 auto;background:#0f172a;border:1px solid #1e3a5f;border-radius:8px;overflow:hidden;}
            .top-bar{background:#0a1628;padding:14px 24px;border-bottom:2px solid #1d4ed8;}
            .top-bar-logo{width:36px;height:36px;object-fit:contain;display:block;}
            .top-bar-name{font-size:15px;font-weight:700;color:#e2e8f0;display:block;line-height:1.2;}
            .top-bar-sub{font-size:10px;color:#64748b;letter-spacing:0.5px;text-transform:uppercase;display:block;}
            .email-title{background:#0f172a;padding:20px 24px;border-bottom:1px solid #1e293b;}
            .email-title h1{font-size:17px;font-weight:600;color:#e2e8f0;margin:0;}
            .body{padding:24px;}
            .info-item{background:#1e293b;border:1px solid #334155;border-radius:6px;padding:10px 14px;margin-bottom:8px;}
            .info-label{font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;display:block;margin-bottom:3px;}
            .info-value{font-size:13px;color:#e2e8f0;}
            .badge{display:inline-block;padding:7px 16px;border-radius:4px;font-weight:700;font-size:12px;margin-bottom:16px;letter-spacing:0.5px;}
            .badge-green{background:rgba(34,197,94,0.15);color:#4ade80;border:1px solid rgba(34,197,94,0.35);}
            .badge-yellow{background:rgba(251,191,36,0.15);color:#fbbf24;border:1px solid rgba(251,191,36,0.35);}
            .badge-red{background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.35);}
            .badge-blue{background:rgba(59,130,246,0.15);color:#60a5fa;border:1px solid rgba(59,130,246,0.35);}
            .obs-box{background:rgba(251,191,36,0.08);border-left:3px solid #f59e0b;padding:12px 16px;border-radius:0 4px 4px 0;margin:16px 0;}
            .obs-box.red{background:rgba(239,68,68,0.08);border-left-color:#ef4444;}
            .obs-box strong{display:block;margin-bottom:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#fbbf24;}
            .obs-box.red strong{color:#f87171;}
            .obs-box p{font-size:13px;color:#fde68a;line-height:1.5;}
            .obs-box.red p{color:#fecaca;}
            .msg{font-size:13px;color:#94a3b8;margin-bottom:16px;}
            .btn{display:inline-block;padding:10px 22px;background:#1d4ed8;color:#fff;text-decoration:none;border-radius:5px;font-weight:600;font-size:13px;margin-top:8px;}
            .grid2{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;}
            .grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:8px;}
            .divider{height:1px;background:#1e293b;margin:20px 0;}
            .footer-sig{padding:16px 24px;background:#0a1628;border-top:1px solid #1e293b;text-align:center;}
            .footer-sig img{width:100%;max-width:600px;height:auto;display:block;margin:0 auto;}
            .footer-sig .sig-placeholder{background:#1e293b;border:1px dashed #334155;padding:16px;color:#475569;font-size:11px;border-radius:4px;display:inline-block;width:100%;}
            .footer-bottom{padding:12px 24px;background:#020617;text-align:center;border-top:1px solid #0f172a;}
            .footer-bottom p{font-size:11px;color:#475569;margin:2px 0;}
            @media only screen and (max-width:480px){
                .body{padding:16px !important;}
                .grid2,.grid3{grid-template-columns:1fr !important;}
            }
        </style>
    </head>
    <body>
    <div class='wrapper'>
        <div class='top-bar'>
            <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                <tr>
                    <td style='padding-right:12px;width:44px;vertical-align:middle;'>
                        <img src='{$LOGO}' alt='Pollo Fiesta' class='top-bar-logo' width='36' height='36'>
                    </td>
                    <td style='vertical-align:middle;'>
                        <span class='top-bar-name'>Pollo Fiesta S.A.</span>
                        <span class='top-bar-sub'>Sistema SAGRILAFT</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class='email-title'><h1>{$title}</h1></div>
        <div class='body'>";
}

function emailFooter(string $FIRMA): string {
    return "
        </div>
        <div class='footer-sig'>
            <img src='{$FIRMA}' alt='Firma Pollo Fiesta' onerror=\"this.style.display='none';this.nextElementSibling.style.display='block';\">
            <div class='sig-placeholder' style='display:none;'>[ Firma corporativa Pollo Fiesta S.A. ]</div>
        </div>
        <div class='footer-bottom'>
            <p><strong>Pollo Fiesta S.A.</strong> &nbsp;·&nbsp; NIT 860.032.450-9</p>
            <p>Sistema SAGRILAFT &nbsp;·&nbsp; © " . date('Y') . " Todos los derechos reservados</p>
            <p style='margin-top:6px;color:#334155;'>Este es un mensaje automático, por favor no responder.</p>
        </div>
    </div>
    </body></html>";
}

// ── Índice ────────────────────────────────────────────────────────────────────
if ($email === 'index') {
    $emails = [
        ['id' => 'nuevo_formulario',      'label' => '1. Nuevo formulario (al revisor)',           'color' => '#3b82f6'],
        ['id' => 'aprobado',              'label' => '2. Formulario aprobado (al usuario)',         'color' => '#22c55e'],
        ['id' => 'aprobado_obs',          'label' => '3. Aprobado con observaciones (al usuario)',  'color' => '#f59e0b'],
        ['id' => 'rechazado',             'label' => '4. Formulario rechazado (al usuario)',        'color' => '#ef4444'],
        ['id' => 'bienvenida',            'label' => '5. Bienvenida / cuenta creada',               'color' => '#8b5cf6'],
        ['id' => 'reset_codigo',          'label' => '6. Código recuperación contraseña',           'color' => '#06b6d4'],
        ['id' => 'reset_confirmacion',    'label' => '7. Contraseña actualizada',                   'color' => '#10b981'],
        ['id' => 'logistica',             'label' => '8. Evaluación logística',                     'color' => '#f97316'],
        ['id' => 'vendedor_cliente',      'label' => '9. Vendedor asignado (al cliente)',           'color' => '#ec4899'],
        ['id' => 'vendedor_notificacion', 'label' => '10. Nuevo cliente asignado (al vendedor)',    'color' => '#14b8a6'],
    ];
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Preview Emails - SAGRILAFT</title>
        <style>
            body{font-family:system-ui,sans-serif;background:#f1f5f9;margin:0;padding:2rem;}
            .brand{display:flex;align-items:center;gap:10px;margin-bottom:0.25rem;}
            .brand img{width:32px;height:32px;}
            .brand span{font-size:1.2rem;font-weight:700;color:#0f172a;}
            p{color:#64748b;margin-bottom:2rem;}
            .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;}
            .card{background:#fff;border:1px solid #e2e8f0;border-radius:.5rem;padding:1.25rem;text-decoration:none;display:flex;align-items:center;gap:1rem;transition:box-shadow .15s;}
            .card:hover{box-shadow:0 4px 12px rgba(0,0,0,.1);}
            .dot{width:12px;height:12px;border-radius:50%;flex-shrink:0;}
            .label{color:#0f172a;font-size:.9rem;font-weight:500;}
            .note{margin-top:2rem;padding:1rem;background:#fef9c3;border:1px solid #fde047;border-radius:.5rem;color:#a16207;font-size:.85rem;}
        </style>
    </head>
    <body>
        <div class="brand">
            <img src="<?= $LOGO ?>" alt="Logo">
            <span>Pollo Fiesta S.A. — Preview de Emails</span>
        </div>
        <p>Haz clic en cualquier email para ver el preview. Se abre en nueva pestaña.</p>
        <div class="grid">
            <?php foreach ($emails as $e): ?>
            <a href="?email=<?= $e['id'] ?>" class="card" target="_blank">
                <div class="dot" style="background:<?= $e['color'] ?>"></div>
                <span class="label"><?= $e['label'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="note">⚠️ Solo para desarrollo. Eliminar o proteger antes de producción.</div>
    </body>
    </html>
    <?php
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

switch ($email) {

    // ── 1. Nuevo formulario (al revisor) ─────────────────────────────────────
    case 'nuevo_formulario':
        echo emailHeader('Nuevo Formulario SAGRILAFT #42', $BASE, $LOGO);
        echo "
        <p class='msg'>Se ha recibido un nuevo formulario que requiere revisión y aprobación.</p>
        <div class='grid3'>
            <div class='info-item'><span class='info-label'>Empresa / Persona</span><span class='info-value'>Empresa Demo S.A.S.</span></div>
            <div class='info-item'><span class='info-label'>NIT / Documento</span><span class='info-value'>900.123.456-7</span></div>
            <div class='info-item'><span class='info-label'>Teléfono</span><span class='info-value'>601 234 5678</span></div>
        </div>
        <div class='info-item'>
            <span class='info-label'>Dirección</span>
            <span class='info-value'>Calle 100 # 15-20, Bogotá D.C.</span>
            <div style='margin-top:8px;'><a href='#' style='display:inline-block;padding:5px 12px;background:#1d4ed8;color:#fff;text-decoration:none;border-radius:4px;font-size:11px;font-weight:600;'>📍 Ver en Google Maps</a></div>
        </div>
        <div class='info-item' style='margin-bottom:16px;'>
            <span class='info-label'>Actividad Económica</span>
            <span class='info-value'>4711 - Comercio al por menor en establecimientos no especializados</span>
        </div>
        <div class='info-item'>
            <span class='info-label'>Documentos Adjuntos (3)</span>
            <ul style='margin:8px 0;padding-left:18px;color:#94a3b8;'>
                <li style='margin:4px 0;font-size:12px;'>RUT_empresa_demo.pdf (0.45 MB)</li>
                <li style='margin:4px 0;font-size:12px;'>Camara_Comercio_2024.pdf (1.20 MB)</li>
                <li style='margin:4px 0;font-size:12px;'>Cedula_representante.jpg (0.18 MB)</li>
            </ul>
        </div>
        <div style='text-align:center;margin-top:24px;'>
            <a href='#' class='btn'>Revisar Formulario</a>
        </div>";
        echo emailFooter($FIRMA);
        break;

    // ── 2. Formulario aprobado ────────────────────────────────────────────────
    case 'aprobado':
        echo emailHeader('Formulario SAGRILAFT Aprobado', $BASE, $LOGO);
        echo "
        <div class='badge badge-green'>✓ APROBADO</div>
        <p class='msg'>Hola <strong style='color:#e2e8f0;'>Juan Pérez</strong>, tu formulario ha sido aprobado exitosamente.</p>
        <div class='grid2'>
            <div class='info-item'><span class='info-label'>ID Formulario</span><span class='info-value'>#42</span></div>
            <div class='info-item'><span class='info-label'>NIT / Cédula</span><span class='info-value'>900.123.456-7</span></div>
        </div>
        <div class='info-item' style='margin-bottom:16px;'>
            <span class='info-label'>Revisado por</span>
            <span class='info-value'>Oficial de Cumplimiento — Pollo Fiesta S.A.</span>
        </div>
        <p style='font-size:12px;color:#475569;margin-top:8px;'>📎 Se adjunta el PDF del formulario aprobado y firmado digitalmente.</p>";
        echo emailFooter($FIRMA);
        break;

    // ── 3. Aprobado con observaciones ─────────────────────────────────────────
    case 'aprobado_obs':
        echo emailHeader('Formulario SAGRILAFT Aprobado con Observaciones', $BASE, $LOGO);
        echo "
        <div class='badge badge-yellow'>⚠ APROBADO CON OBSERVACIONES</div>
        <p class='msg'>Hola <strong style='color:#e2e8f0;'>Juan Pérez</strong>, tu formulario ha sido aprobado pero requiere correcciones.</p>
        <div class='grid2'>
            <div class='info-item'><span class='info-label'>ID Formulario</span><span class='info-value'>#42</span></div>
            <div class='info-item'><span class='info-label'>NIT / Cédula</span><span class='info-value'>900.123.456-7</span></div>
        </div>
        <div class='info-item' style='margin-bottom:16px;'>
            <span class='info-label'>Revisado por</span>
            <span class='info-value'>Oficial de Cumplimiento — Pollo Fiesta S.A.</span>
        </div>
        <div class='obs-box'>
            <strong>Observaciones</strong>
            <p>Por favor actualizar el RUT con fecha vigente 2024. El certificado de cámara de comercio debe ser de los últimos 30 días. Favor reenviar documentos actualizados.</p>
        </div>";
        echo emailFooter($FIRMA);
        break;

    // ── 4. Rechazado ──────────────────────────────────────────────────────────
    case 'rechazado':
        echo emailHeader('Formulario SAGRILAFT Rechazado', $BASE, $LOGO);
        echo "
        <div class='badge badge-red'>✗ RECHAZADO</div>
        <p class='msg'>Hola <strong style='color:#e2e8f0;'>Juan Pérez</strong>, tu formulario ha sido rechazado.</p>
        <div class='grid2'>
            <div class='info-item'><span class='info-label'>ID Formulario</span><span class='info-value'>#42</span></div>
            <div class='info-item'><span class='info-label'>NIT / Cédula</span><span class='info-value'>900.123.456-7</span></div>
        </div>
        <div class='info-item' style='margin-bottom:16px;'>
            <span class='info-label'>Revisado por</span>
            <span class='info-value'>Oficial de Cumplimiento — Pollo Fiesta S.A.</span>
        </div>
        <div class='obs-box red'>
            <strong>Motivo del rechazo</strong>
            <p>La información suministrada no corresponde con los documentos adjuntos. El NIT registrado no coincide con el RUT presentado. Por favor verificar y volver a enviar el formulario con la información correcta.</p>
        </div>";
        echo emailFooter($FIRMA);
        break;

    // ── 5. Bienvenida ─────────────────────────────────────────────────────────
    case 'bienvenida':
        echo emailHeader('Bienvenido a SAGRILAFT', $BASE, $LOGO);
        echo "
        <div class='badge badge-blue'>Cuenta Creada</div>
        <p class='msg'>Hola <strong style='color:#e2e8f0;'>Juan Pérez</strong>, tu cuenta ha sido creada exitosamente en el sistema SAGRILAFT de Pollo Fiesta S.A.</p>
        <div class='info-item' style='margin-bottom:8px;'>
            <span class='info-label'>Email de acceso</span>
            <span class='info-value'>juan@empresa.com</span>
        </div>
        <div class='info-item' style='margin-bottom:20px;'>
            <span class='info-label'>Contraseña</span>
            <span class='info-value'>La que estableciste durante el registro</span>
        </div>
        <div style='text-align:center;margin-top:8px;'>
            <a href='#' class='btn'>Acceder al Sistema</a>
        </div>
        <div class='divider'></div>
        <p style='font-size:12px;color:#475569;'><strong>Nota:</strong> Si no solicitaste esta cuenta, por favor ignora este correo o contacta al administrador del sistema.</p>";
        echo emailFooter($FIRMA);
        break;

    // ── 6. Código recuperación contraseña ─────────────────────────────────────
    case 'reset_codigo':
        echo emailHeader('Recuperación de Contraseña', $BASE, $LOGO);
        echo "
        <p class='msg'>Hola <strong style='color:#e2e8f0;'>Juan Pérez</strong>, recibimos una solicitud para restablecer la contraseña de tu cuenta en Pollo Fiesta S.A. — SAGRILAFT.</p>
        <div style='background:#1e293b;border:2px dashed #1d4ed8;border-radius:8px;padding:24px;text-align:center;margin:20px 0;'>
            <div style='font-size:38px;font-weight:700;letter-spacing:10px;color:#60a5fa;font-family:monospace;'>847291</div>
            <p style='margin:12px 0 0;color:#64748b;font-size:13px;'>Este código expira en <strong style='color:#e2e8f0;'>15 minutos</strong></p>
        </div>
        <div class='obs-box' style='background:rgba(251,191,36,0.08);border-left-color:#f59e0b;'>
            <strong style='color:#fbbf24;'>⚠️ Importante</strong>
            <p style='color:#fde68a;'>
                • No compartas este código con nadie<br>
                • Si no solicitaste este cambio, ignora este email<br>
                • El código solo se puede usar una vez
            </p>
        </div>
        <p style='font-size:12px;color:#475569;margin-top:16px;'>Si tienes problemas, contacta a soporte de Pollo Fiesta S.A.</p>";
        echo emailFooter($FIRMA);
        break;

    // ── 7. Contraseña actualizada ─────────────────────────────────────────────
    case 'reset_confirmacion':
        echo emailHeader('Contraseña Actualizada', $BASE, $LOGO);
        echo "
        <div style='text-align:center;margin-bottom:16px;'>
            <div style='font-size:48px;'>✓</div>
            <div class='badge badge-green' style='margin-top:8px;'>Contraseña Actualizada</div>
        </div>
        <p class='msg'>Hola <strong style='color:#e2e8f0;'>Juan Pérez</strong>, tu contraseña ha sido actualizada exitosamente.</p>
        <div class='info-item' style='margin-bottom:16px;'>
            <span class='info-label'>Estado</span>
            <span class='info-value'>Ya puedes iniciar sesión con tu nueva contraseña.</span>
        </div>
        <div class='divider'></div>
        <p style='font-size:12px;color:#475569;'>Si no realizaste este cambio, contacta inmediatamente al soporte de Pollo Fiesta S.A.</p>";
        echo emailFooter($FIRMA);
        break;

    // ── 8. Evaluación logística ───────────────────────────────────────────────
    case 'logistica':
        echo emailHeader('Evaluación Logística SAGRILAFT', $BASE, $LOGO);
        echo "
        <div class='badge badge-green'>✓ APTA</div>
        <p class='msg'>Estimado/a <strong style='color:#e2e8f0;'>María García</strong>, tu evaluación logística ha sido actualizada por Pollo Fiesta S.A.</p>
        <div class='info-item' style='margin-bottom:16px;'>
            <span class='info-label'>Resultado</span>
            <span class='info-value'>Documentación completa y verificada.</span>
        </div>
        <div class='obs-box'>
            <strong>Observaciones</strong>
            <p>Documentación completa y verificada. Vehículo en buen estado. Licencia vigente hasta diciembre 2025.</p>
        </div>
        <p style='font-size:12px;color:#475569;margin-top:16px;'>Puedes ver más detalles en tu perfil del sistema.</p>";
        echo emailFooter($FIRMA);
        break;

    // ── 9. Vendedor asignado (al cliente) ─────────────────────────────────────
    case 'vendedor_cliente':
        echo emailHeader('Vendedor Asignado — Pollo Fiesta S.A.', $BASE, $LOGO);
        echo "
        <div class='badge badge-blue'>Vendedor Asignado</div>
        <p class='msg'>Estimado/a <strong style='color:#e2e8f0;'>Ana Torres</strong>, se te ha asignado un vendedor en el sistema SAGRILAFT de Pollo Fiesta S.A.</p>
        <div class='grid2'>
            <div class='info-item'><span class='info-label'>Vendedor</span><span class='info-value'>Carlos López</span></div>
            <div class='info-item'><span class='info-label'>Teléfono</span><span class='info-value'>310 987 6543</span></div>
        </div>
        <div class='info-item' style='margin-bottom:16px;'>
            <span class='info-label'>Email de contacto</span>
            <span class='info-value'>carlos@sagrilaft.com</span>
        </div>
        <p style='font-size:13px;color:#94a3b8;'>Tu vendedor estará disponible para atender tus consultas y gestionar tus solicitudes.</p>";
        echo emailFooter($FIRMA);
        break;

    // ── 10. Nuevo cliente asignado (al vendedor) ──────────────────────────────
    case 'vendedor_notificacion':
        echo emailHeader('Nuevo Cliente Asignado — Pollo Fiesta S.A.', $BASE, $LOGO);
        echo "
        <div class='badge badge-blue'>Nuevo Cliente</div>
        <p class='msg'>Hola <strong style='color:#e2e8f0;'>Carlos López</strong>, se te ha asignado un nuevo cliente en el sistema SAGRILAFT de Pollo Fiesta S.A.</p>
        <div class='grid2'>
            <div class='info-item'><span class='info-label'>Cliente</span><span class='info-value'>Ana Torres</span></div>
            <div class='info-item'><span class='info-label'>Teléfono</span><span class='info-value'>315 111 2233</span></div>
        </div>
        <div class='grid2'>
            <div class='info-item'><span class='info-label'>Email</span><span class='info-value'>ana@cliente.com</span></div>
            <div class='info-item'><span class='info-label'>Empresa</span><span class='info-value'>Distribuidora Torres Ltda.</span></div>
        </div>
        <p style='font-size:13px;color:#94a3b8;margin-top:8px;'>Por favor, ponte en contacto con el cliente para iniciar la gestión.</p>";
        echo emailFooter($FIRMA);
        break;

    default:
        header('Location: ?email=index');
        exit;
}
