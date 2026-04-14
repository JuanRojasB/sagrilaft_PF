<?php
/**
 * TEST DE CORREOS - Sistema SAGRILAFT
 * 
 * Este script simula todos los escenarios de envío de correos
 * y muestra a quién se enviaría cada correo según el tipo de formulario.
 * 
 * NO ENVÍA CORREOS REALES, solo muestra la lista de destinatarios.
 */

require_once __DIR__ . '/app/Config/EmailRecipientsConfig.php';

use App\Config\EmailRecipientsConfig;

echo "═══════════════════════════════════════════════════════════════\n";
echo "  TEST DE CORREOS - Sistema SAGRILAFT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Modo actual
$testMode = EmailRecipientsConfig::TEST_MODE ? 'ACTIVO' : 'DESACTIVADO';
$testEmail = EmailRecipientsConfig::TEST_EMAIL;

echo "🔴 MODO TEST: {$testMode}\n";
if (EmailRecipientsConfig::TEST_MODE) {
    echo "   Todos los correos van a: {$testEmail}\n";
}
echo "\n";

// Datos de prueba
$formDataCliente = [
    'creator_email' => 'juan.david.rojas.burbano0@gmail.com',
    'creator_name' => 'Juan David Rojas',
    'asesor_email' => 'vendedor1@pollo-fiesta.com',
    'asesor_nombre' => 'Vendedor Ejemplo',
    'jefe_email' => 'gerente@pollo-fiesta.com',
    'jefe_nombre' => 'Gerente Ejemplo'
];

$formDataProveedor = [
    'area_solicitante_email' => 'area.solicitante@pollo-fiesta.com',
    'area_solicitante_nombre' => 'Área Solicitante Ejemplo'
];

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  1. NUEVO FORMULARIO (Link de Aprobación)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getNewFormRecipients('cliente_natural');
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  2. CLIENTE NATURAL - APROBADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getApprovedRecipients('cliente_natural', $formDataCliente);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  3. CLIENTE NATURAL - RECHAZADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getRejectedRecipients('cliente_natural', $formDataCliente);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  4. CLIENTE JURÍDICA - APROBADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getApprovedRecipients('cliente_juridica', $formDataCliente);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  5. PROVEEDOR NATURAL - APROBADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getApprovedRecipients('proveedor_natural', $formDataProveedor);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  6. PROVEEDOR NATURAL - RECHAZADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getRejectedRecipients('proveedor_natural', $formDataProveedor);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  7. PROVEEDOR JURÍDICA - APROBADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getApprovedRecipients('proveedor_juridica', $formDataProveedor);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  8. PROVEEDOR INTERNACIONAL - APROBADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getApprovedRecipients('proveedor_internacional', $formDataProveedor);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  9. TRANSPORTISTA NATURAL - APROBADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getApprovedRecipients('transportista_natural', []);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  10. TRANSPORTISTA NATURAL - RECHAZADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getRejectedRecipients('transportista_natural', []);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  11. TRANSPORTISTA JURÍDICA - APROBADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getApprovedRecipients('transportista_juridica', []);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  12. EMPLEADO - APROBADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getApprovedRecipients('empleado', []);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  13. EMPLEADO - RECHAZADO\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$recipients = EmailRecipientsConfig::getRejectedRecipients('empleado', []);
echo "Destinatarios:\n";
foreach ($recipients as $r) {
    echo "  ✉️  {$r['name']} <{$r['email']}> [{$r['type']}]\n";
}
echo "\n";

// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "  RESUMEN DE CORREOS CONFIGURADOS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$allEmails = [
    'oficialdecumplimiento@pollo-fiesta.com' => 'Angie - Oficial de Cumplimiento',
    'cartera@pollo-fiesta.com' => 'Directora de Cartera',
    'eymis.carey@pollo-fiesta.com' => 'Eymis Carey - Cartera',
    'compras@pollo-fiesta.com' => 'Briyith - Compras',
    'esperanza.aguilar@pollo-fiesta.com' => 'Esperanza Aguilar - Contabilidad',
    'alejandra.camargo@pollo-fiesta.com' => 'Alejandra Camargo - Contabilidad',
    'asistesoreria@pollo-fiesta.com' => 'Keyner - Tesorería',
    'controlderutas@pollo-fiesta.com' => 'Control de Rutas',
    'gerlogistica@pollo-fiesta.com' => 'Diego - Gerente Logística',
    'dirgestionhumana@pollo-fiesta.com' => 'Yohana - Dir. Gestión Humana',
    'seleccionpersonal@pollo-fiesta.com' => 'Selección de Personal',
    'r.humanos@pollo-fiesta.com' => 'Elsa - Recursos Humanos',
    'juan.david.rojas.burbano0@gmail.com' => 'Juan David Rojas (Test)'
];

echo "Total de correos configurados: " . count($allEmails) . "\n\n";
foreach ($allEmails as $email => $name) {
    echo "  ✅ {$name}\n     {$email}\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "  FIN DEL TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if (EmailRecipientsConfig::TEST_MODE) {
    echo "⚠️  RECORDATORIO: Modo TEST está ACTIVO\n";
    echo "   Todos los correos se envían a: {$testEmail}\n";
    echo "   Para activar modo producción:\n";
    echo "   1. Abrir: app/Config/EmailRecipientsConfig.php\n";
    echo "   2. Línea 12: Cambiar TEST_MODE = false\n\n";
}
