<?php
/**
 * Script de Generación de Datos de Prueba
 * 
 * Crea formularios de prueba para verificar:
 * - Formulario de Cliente (Paso 1)
 * - Formulario de Declaración de Origen de Fondos (Paso 2)
 * - Otros tipos de formularios (Proveedor, Transportista)
 * 
 * Uso: http://localhost/gestion-sagrilaft/public/generar_datos_prueba.php
 */

declare(strict_types=1);

// Autoloader PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Cargar configuración
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

use App\Core\Database;
use PDO;

// Conectar a la base de datos
$db = Database::getConnection();

// Configurar fecha y timezone
date_default_timezone_set('America/Bogota');

// ============================================================================
// CREAR USUARIO DE PRUEBA
// ============================================================================

function crearUsuarioPrueba($db) {
    $email = 'cliente_prueba@test.com';
    
    // Verificar si ya existe
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✓ Usuario de prueba ya existe (ID: {$user['id']})\n";
        return $user['id'];
    }
    
    // Crear usuario
    $stmt = $db->prepare(
        "INSERT INTO users (name, email, password, role) 
         VALUES (?, ?, ?, ?)"
    );
    
    $password = password_hash('password123', PASSWORD_BCRYPT);
    $stmt->execute([
        'Juan Carlos Pérez',
        $email,
        $password,
        'usuario'
    ]);
    
    $userId = (int)$db->lastInsertId();
    echo "✓ Usuario de prueba creado (ID: $userId)\n";
    echo "  Email: $email\n";
    echo "  Contraseña: password123\n";
    
    return $userId;
}

// ============================================================================
// CREAR FORMULARIO CLIENTE (PASO 1)
// ============================================================================

function crearFormularioCliente($db, $userId) {
    // Verificar si ya existe
    $stmt = $db->prepare(
        "SELECT id FROM forms_sagrilaft WHERE user_id = ? AND form_type = 'cliente' LIMIT 1"
    );
    $stmt->execute([$userId]);
    $form = $stmt->fetch();
    
    if ($form) {
        echo "\n✓ Formulario cliente ya existe (ID: {$form['id']})\n";
        return (int)$form['id'];
    }
    
    // Crear formulario
    $stmt = $db->prepare(
        "INSERT INTO forms_sagrilaft (
            user_id, title, content, form_type, status,
            company_name, nit, dv, activity, codigo_ciiu,
            address, ciudad, barrio, localidad,
            telefono_fijo, celular, phone, email, fax,
            activos, pasivos, patrimonio, ingresos, gastos,
            otros_ingresos, detalle_otros_ingresos,
            tipo_contribuyente, regimen_tributario,
            representante_nombre, representante_documento, representante_tipo_doc,
            representante_profesion, representante_nacimiento,
            representante_telefono, representante_direccion,
            accionistas,
            lista_precios, codigo_vendedor, tipo_pago, cupo_credito,
            objeto_social,
            autoriza_centrales_riesgo,
            clase_cliente,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?,
            ?, ?, ?,
            ?, ?,
            ?, ?,
            ?,
            ?, ?, ?, ?,
            ?,
            ?,
            ?,
            NOW(), NOW()
        )"
    );
    
    $accionistas = json_encode([
        [
            'nombre' => 'María González López',
            'documento' => '1098765432',
            'participacion' => '60%'
        ],
        [
            'nombre' => 'Luis Rodríguez Martinez',
            'documento' => '1087654321',
            'participacion' => '40%'
        ]
    ]);
    
    $stmt->execute([
        $userId,
        'Formulario de Vinculación - Paso 1',
        'Formulario de vinculación del cliente',
        'cliente',
        'draft',
        'AGRÍCOLA Y GANADERA DEL NORTE SAS',
        '800201234',
        '5',
        'Cultivo, producción y comercialización de productos agrícolas',
        '0112',
        'Carrera 30 No. 45-67 Apto 501',
        'Bogotá',
        'Chapinero',
        'Chapinero',
        '(1) 2345678',
        '320 5555555',
        '(1) 2345678',
        'contacto@agricola.com',
        '(1) 2345679',
        '500000000.00',
        '200000000.00',
        '300000000.00',
        '150000000.00',
        '60000000.00',
        '5000000.00',
        'Venta de productos agrícolas a otros distribuidores',
        'persona_juridica',
        'especial',
        'Juan Carlos Pérez López',
        '1087654321',
        'cc',
        'Ingeniero Agrónomo',
        '1970-05-15',
        '320 1234567',
        'Carrera 30 No. 45-67 Apto 501, Bogotá',
        $accionistas,
        'P1',
        'V001',
        'credito',
        '50000000.00',
        'Cultivo, producción y comercialización de productos agrícolas, especialmente para fines de exportación.',
        'si',
        'GRANDE'
    ]);
    
    $formId = (int)$db->lastInsertId();
    echo "\n✓ Formulario cliente creado (ID: $formId)\n";
    echo "  Empresa: AGRÍCOLA Y GANADERA DEL NORTE SAS\n";
    echo "  NIT: 800201234-5\n";
    echo "  Tipo: Cliente\n";
    
    return $formId;
}

// ============================================================================
// CREAR FORMULARIO DE DECLARACIÓN DE ORIGEN (PASO 2)
// ============================================================================

function crearFormularioDeclaracion($db, $userId, $formIdPrincipal) {
    // Verificar si ya existe
    $stmt = $db->prepare(
        "SELECT id FROM forms_sagrilaft WHERE related_form_id = ? AND form_type IS NULL"
    );
    $stmt->execute([$formIdPrincipal]);
    $form = $stmt->fetch();
    
    if ($form) {
        echo "\n✓ Formulario de declaración ya existe (ID: {$form['id']})\n";
        return (int)$form['id'];
    }
    
    // Crear formulario
    $stmt = $db->prepare(
        "INSERT INTO forms_sagrilaft (
            user_id, title, content, related_form_id, status,
            company_name, nit,
            origen_fondos,
            es_pep, cargo_pep, relacion_pep, identificacion_pep,
            familiares_pep,
            tiene_cuentas_exterior, pais_cuentas_exterior,
            consulta_ofac, consulta_listas_nacionales, consulta_onu,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?,
            ?,
            ?, ?, ?, ?,
            ?,
            ?, ?,
            ?, ?, ?,
            NOW(), NOW()
        )"
    );
    
    $stmt->execute([
        $userId,
        'Declaración de Origen de Fondos - Paso 2',
        'Formulario de declaración de origen de fondos',
        $formIdPrincipal,
        'draft',
        'AGRÍCOLA Y GANADERA DEL NORTE SAS',
        '800201234',
        'Los fondos provienen de la actividad principal de la empresa en la comercialización de productos agrícolas. La empresa ha mantenido operaciones consistentes durante más de 10 años en el sector agrícola, con ingresos documentados y declaraciones tributarias completas.',
        'no',
        '',
        '',
        '',
        'No aplica. El representante legal y los accionistas no tienen familiares en segundo grado de consanguinidad que sean PEPs.',
        'si',
        'Estados Unidos, Canadá'
    ]);
    
    $declId = (int)$db->lastInsertId();
    echo "\n✓ Formulario de declaración creado (ID: $declId)\n";
    echo "  Relacionado con: Formulario $formIdPrincipal\n";
    echo "  Origen de fondos documentado\n";
    echo "  PEP: No\n";
    echo "  Cuentas en exterior: Sí (USA, Canadá)\n";
    
    return $declId;
}

// ============================================================================
// CREAR FORMULARIO PROVEEDOR
// ============================================================================

function crearFormularioProveedor($db, $userId) {
    // Verificar si ya existe
    $stmt = $db->prepare(
        "SELECT id FROM forms_sagrilaft WHERE user_id = ? AND form_type = 'proveedor' LIMIT 1"
    );
    $stmt->execute([$userId]);
    $form = $stmt->fetch();
    
    if ($form) {
        echo "\n✓ Formulario proveedor ya existe (ID: {$form['id']})\n";
        return (int)$form['id'];
    }
    
    // Crear formulario
    $stmt = $db->prepare(
        "INSERT INTO forms_sagrilaft (
            user_id, title, content, form_type, status,
            company_name, nit, dv, activity, codigo_ciiu,
            address, ciudad, barrio, localidad, pais,
            telefono_fijo, celular, phone, email,
            activos, pasivos, patrimonio, ingresos, gastos,
            tipo_compania,
            persona_contacto, tiene_certificacion, cual_certificacion,
            concepto_importacion, declaracion_importacion,
            certificado_origen, certificado_transporte, certificado_fitosanitario,
            autoriza_centrales_riesgo,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?,
            ?, ?, ?,
            ?, ?,
            ?, ?, ?,
            ?,
            NOW(), NOW()
        )"
    );
    
    $stmt->execute([
        $userId,
        'Formulario de Vinculación - Proveedor',
        'Formulario de vinculación del proveedor de semillas',
        'proveedor',
        'draft',
        'EXPORTADORA INTERNACIONAL DE PRODUCTOS AGRÍCOLAS LTDA',
        '860123456',
        '2',
        'Importación y distribución de semillas certificadas para siembra',
        '4620',
        'Avenida Principal No. 100-50',
        'Medellín',
        'El Hueco',
        'Centro',
        'Colombia',
        '(4) 5555555',
        '315 9999999',
        '(4) 5555555',
        'contacto@exportadora.com',
        '800000000.00',
        '300000000.00',
        '500000000.00',
        '600000000.00',
        '200000000.00',
        'privada',
        'Carlos Eduardo Vargas López',
        'si',
        'ISO 9001:2015, Certificación Fitosanitaria IICA',
        'Semillas certificadas, fertilizantes especializados',
        'DAU-001-2024-123456',
        'Certificado de origen disponible',
        'Transporte refrigerado',
        'Certificado de sanidad vegetal disponible',
        'si'
    ]);
    
    $provId = (int)$db->lastInsertId();
    echo "\n✓ Formulario proveedor creado (ID: $provId)\n";
    echo "  Empresa: EXPORTADORA INTERNACIONAL DE PRODUCTOS AGRÍCOLAS LTDA\n";
    echo "  NIT: 860123456-2\n";
    echo "  Tipo: Proveedor\n";
    
    return $provId;
}

// ============================================================================
// CREAR FORMULARIO TRANSPORTISTA
// ============================================================================

function crearFormularioTransportista($db, $userId) {
    // Verificar si ya existe
    $stmt = $db->prepare(
        "SELECT id FROM forms_sagrilaft WHERE user_id = ? AND form_type = 'transportista' LIMIT 1"
    );
    $stmt->execute([$userId]);
    $form = $stmt->fetch();
    
    if ($form) {
        echo "\n✓ Formulario transportista ya existe (ID: {$form['id']})\n";
        return (int)$form['id'];
    }
    
    // Crear formulario
    $stmt = $db->prepare(
        "INSERT INTO forms_sagrilaft (
            user_id, title, content, form_type, status,
            company_name, nit, dv, activity, codigo_ciiu,
            address, ciudad, barrio, localidad,
            telefono_fijo, celular, phone, email,
            activos, pasivos, patrimonio, ingresos, gastos,
            representante_nombre, representante_documento, representante_tipo_doc,
            autoriza_centrales_riesgo,
            created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?,
            ?,
            NOW(), NOW()
        )"
    );
    
    $stmt->execute([
        $userId,
        'Formulario de Vinculación - Transportista',
        'Formulario de vinculación del transportista de carga',
        'transportista',
        'draft',
        'TRANSPORTES Y LOGÍSTICA NACIONAL SAS',
        '890654321',
        '8',
        'Transporte terrestre de carga y logística especializada',
        '4921',
        'Calle 80 No. 25-40',
        'Cali',
        'San Alejo',
        'Ladera',
        '(2) 3456789',
        '310 8888888',
        '(2) 3456789',
        'operaciones@transportes.com',
        '350000000.00',
        '150000000.00',
        '200000000.00',
        '180000000.00',
        '80000000.00',
        'Roberto Gómez Sánchez',
        '1076543210',
        'cc',
        'si'
    ]);
    
    $transpId = (int)$db->lastInsertId();
    echo "\n✓ Formulario transportista creado (ID: $transpId)\n";
    echo "  Empresa: TRANSPORTES Y LOGÍSTICA NACIONAL SAS\n";
    echo "  NIT: 890654321-8\n";
    echo "  Tipo: Transportista\n";
    
    return $transpId;
}

// ============================================================================
// MAIN
// ============================================================================

echo "═════════════════════════════════════════════════════════════════\n";
echo "   GENERADOR DE DATOS DE PRUEBA - SISTEMA SAGRILAFT\n";
echo "═════════════════════════════════════════════════════════════════\n";

try {
    $userId = crearUsuarioPrueba($db);
    $formClienteId = crearFormularioCliente($db, $userId);
    $declaracionId = crearFormularioDeclaracion($db, $userId, $formClienteId);
    crearFormularioProveedor($db, $userId);
    crearFormularioTransportista($db, $userId);
    
    echo "\n═════════════════════════════════════════════════════════════════\n";
    echo "✓ DATOS DE PRUEBA GENERADOS EXITOSAMENTE\n";
    echo "═════════════════════════════════════════════════════════════════\n";
    
    echo "\n📋 PRÓXIMOS PASOS:\n";
    echo "1. Inicia sesión en: http://localhost/gestion-sagrilaft/public/login\n";
    echo "   Email: cliente_prueba@test.com\n";
    echo "   Contraseña: password123\n\n";
    
    echo "2. Verás los formularios creados en tu dashboard\n\n";
    
    echo "3. Para ver la DECLARACIÓN DE ORIGEN:\n";
    echo "   - Ve a Formulario ID $formClienteId (Cliente)\n";
    echo "   - Encontrarás el Paso 2: Declaración de Origen (ID $declaracionId)\n\n";
    
    echo "4. Para generar PDF:\n";
    echo "   - Accede a cada formulario\n";
    echo "   - Haz clic en 'Generar PDF'\n";
    echo "   - El sistema generará el PDF con todos los datos\n\n";
    
    echo "5. Para APROBAR como admin:\n";
    echo "   - Inicia sesión con: admin@sagrilaft.com / password123\n";
    echo "   - Ve al Dashboard Admin\n";
    echo "   - Visualiza los formularios para aprobación\n\n";
    
    echo "═════════════════════════════════════════════════════════════════\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
