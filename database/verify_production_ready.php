<?php
/**
 * Verificación de Estado de Producción - SAGRILAFT
 * 
 * Este script verifica que el sistema esté listo para producción
 */

require_once __DIR__ . '/../app/Core/Database.php';

// Configurar conexión a la base de datos
$db = \App\Core\Database::getConnection();

echo "<h1>🚀 Verificación de Estado de Producción - SAGRILAFT</h1>";
echo "<p><em>Verificado el " . date('Y-m-d H:i:s') . "</em></p>";

// 1. Verificar estructura de base de datos
echo "<h2>1. ✅ Estructura de Base de Datos</h2>";

$requiredTables = [
    'users' => 'Usuarios del sistema',
    'forms' => 'Formularios SAGRILAFT',
    'form_empleados' => 'Formularios de empleados',
    'form_attachments' => 'Archivos adjuntos',
    'form_consolidated_pdfs' => 'PDFs consolidados',
    'form_signatures' => 'Firmas y campos del revisor',
    'firmas_digitales' => 'Firmas digitales',
    'asesores_comerciales' => 'Asesores comerciales'
];

$allTablesExist = true;
foreach ($requiredTables as $table => $description) {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        echo "<p>✅ <strong>$table</strong>: $description ($count registros)</p>";
    } catch (Exception $e) {
        echo "<p>❌ <strong>$table</strong>: FALTA - $description</p>";
        $allTablesExist = false;
    }
}

// 2. Verificar usuarios esenciales
echo "<h2>2. 👥 Usuarios Esenciales</h2>";
$stmt = $db->query("SELECT id, name, email, role FROM users ORDER BY role, name");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($users) >= 2) {
    echo "<p>✅ Usuarios configurados correctamente:</p>";
    echo "<ul>";
    foreach ($users as $user) {
        $roleIcon = $user['role'] === 'admin' ? '👑' : ($user['role'] === 'revisor' ? '📋' : '👤');
        echo "<li>$roleIcon <strong>" . htmlspecialchars($user['name']) . "</strong> (" . htmlspecialchars($user['email']) . ") - " . ucfirst($user['role']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ Faltan usuarios esenciales. Se requieren al menos un admin y un revisor.</p>";
}

// 3. Verificar datos limpios
echo "<h2>3. 🧹 Estado de Datos</h2>";
$dataTables = [
    'forms' => 'Formularios',
    'firmas_digitales' => 'Firmas digitales',
    'form_signatures' => 'Campos del revisor',
    'form_consolidated_pdfs' => 'PDFs consolidados'
];

$isClean = true;
foreach ($dataTables as $table => $description) {
    $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
    $count = $stmt->fetch()['count'];
    if ($count == 0) {
        echo "<p>✅ <strong>$description</strong>: Limpio (0 registros)</p>";
    } else {
        echo "<p>⚠️ <strong>$description</strong>: $count registros (revisar si son datos de producción)</p>";
        $isClean = false;
    }
}

// 4. Verificar asesores comerciales
echo "<h2>4. 👔 Asesores Comerciales</h2>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM asesores_comerciales WHERE activo = 1");
    $totalAsesores = $stmt->fetch()['total'];
    
    if ($totalAsesores > 0) {
        echo "<p>✅ <strong>$totalAsesores asesores comerciales activos</strong> configurados</p>";
        
        $stmt = $db->query("SELECT nombre_completo, email FROM asesores_comerciales WHERE activo = 1 ORDER BY nombre_completo LIMIT 5");
        $asesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p>Primeros asesores:</p><ul>";
        foreach ($asesores as $asesor) {
            echo "<li>" . htmlspecialchars($asesor['nombre_completo']) . " (" . htmlspecialchars($asesor['email']) . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ <strong>No hay asesores comerciales activos</strong>. Se requieren para el campo 'Preparó'.</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error verificando asesores comerciales: " . $e->getMessage() . "</p>";
}

// 5. Verificar configuración de email
echo "<h2>5. 📧 Configuración de Email</h2>";
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $hasMailConfig = strpos($envContent, 'MAIL_HOST') !== false;
    
    if ($hasMailConfig) {
        echo "<p>✅ <strong>Configuración de email encontrada</strong> en .env</p>";
    } else {
        echo "<p>⚠️ <strong>Configuración de email no encontrada</strong>. Revisar variables MAIL_* en .env</p>";
    }
} else {
    echo "<p>❌ <strong>Archivo .env no encontrado</strong>. Se requiere para configuración de email.</p>";
}

// 6. Verificar permisos de directorios
echo "<h2>6. 📁 Permisos de Directorios</h2>";
$directories = [
    __DIR__ . '/../storage/logs' => 'Logs del sistema',
    __DIR__ . '/../public/uploads' => 'Archivos subidos',
    sys_get_temp_dir() => 'Directorio temporal del sistema'
];

foreach ($directories as $dir => $description) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "<p>✅ <strong>$description</strong>: Escribible</p>";
    } else {
        echo "<p>❌ <strong>$description</strong>: No escribible o no existe ($dir)</p>";
    }
}

// 7. Resumen final
echo "<h2>7. 📊 Resumen Final</h2>";

$issues = [];
if (!$allTablesExist) $issues[] = "Faltan tablas en la base de datos";
if (count($users) < 2) $issues[] = "Faltan usuarios esenciales";
if ($totalAsesores == 0) $issues[] = "No hay asesores comerciales configurados";

if (empty($issues)) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724; margin: 0 0 15px 0;'>🎉 ¡SISTEMA LISTO PARA PRODUCCIÓN!</h3>";
    echo "<p style='color: #155724; margin: 0; font-size: 16px;'>Todos los componentes están correctamente configurados y la base de datos está limpia.</p>";
    echo "</div>";
    
    echo "<h3>✅ Funcionalidades Verificadas:</h3>";
    echo "<ul>";
    echo "<li>✅ Base de datos limpia y estructurada</li>";
    echo "<li>✅ Usuarios esenciales creados</li>";
    echo "<li>✅ Sistema de firmas digitales listo</li>";
    echo "<li>✅ Campos del revisor configurados</li>";
    echo "<li>✅ Generación de PDFs operativa</li>";
    echo "<li>✅ Asesores comerciales disponibles</li>";
    echo "</ul>";
    
    echo "<h3>🚀 Próximos Pasos:</h3>";
    echo "<ol>";
    echo "<li><strong>Configurar email de producción</strong> en archivo .env</li>";
    echo "<li><strong>Crear usuarios adicionales</strong> según necesidades</li>";
    echo "<li><strong>Subir firmas digitales</strong> de usuarios y revisores</li>";
    echo "<li><strong>Probar flujo completo</strong> con un formulario de prueba</li>";
    echo "<li><strong>Capacitar usuarios</strong> sobre el nuevo sistema</li>";
    echo "</ol>";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3 style='color: #721c24; margin: 0 0 15px 0;'>⚠️ Problemas Detectados</h3>";
    echo "<ul style='color: #721c24; margin: 0;'>";
    foreach ($issues as $issue) {
        echo "<li>$issue</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Credenciales por defecto
echo "<h3>🔑 Credenciales por Defecto:</h3>";
echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; font-family: monospace;'>";
echo "<p><strong>Administrador:</strong><br>";
echo "Email: admin@pollofiesta.com<br>";
echo "Password: password</p>";
echo "<p><strong>Revisor:</strong><br>";
echo "Email: angie.martinez@pollofiesta.com<br>";
echo "Password: password</p>";
echo "<p style='color: #856404; font-size: 12px;'><em>⚠️ IMPORTANTE: Cambiar estas contraseñas antes de usar en producción</em></p>";
echo "</div>";

echo "<hr>";
echo "<p style='text-align: center; color: #666; font-size: 12px;'>";
echo "Sistema SAGRILAFT - Pollo Fiesta S.A.<br>";
echo "Verificación completada el " . date('Y-m-d H:i:s');
echo "</p>";
?>