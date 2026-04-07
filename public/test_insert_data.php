<?php
/**
 * Script de Prueba - Insertar Datos Completos
 * 
 * Este script inserta datos de ejemplo completos en la base de datos
 * para probar el sistema SAGRILAFT con todos los campos llenos.
 * 
 * IMPORTANTE: Solo usar en desarrollo/testing
 * 
 * Acceso: http://localhost/gestion-sagrilaft/public/test_insert_data.php
 */

// Cargar variables de entorno
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// Solo permitir en desarrollo
if (($_ENV['APP_ENV'] ?? 'production') !== 'development') {
    die('Este script solo está disponible en modo desarrollo');
}

require_once __DIR__ . '/../app/Core/Database.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Insertar Datos Completos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 40px;
        }
        .info-box {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .info-box h3 {
            color: #1e40af;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .info-box ul {
            list-style: none;
            padding: 0;
        }
        .info-box li {
            padding: 8px 0;
            color: #475569;
            font-size: 14px;
        }
        .info-box li strong {
            color: #1e293b;
            display: inline-block;
            width: 180px;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #64748b;
        }
        .btn-secondary:hover {
            box-shadow: 0 10px 25px rgba(100, 116, 139, 0.4);
        }
        .result {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .result.error {
            background: #fef2f2;
            border-color: #fca5a5;
        }
        .result h3 {
            color: #166534;
            margin-bottom: 15px;
        }
        .result.error h3 {
            color: #991b1b;
        }
        .result pre {
            background: white;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 13px;
            color: #1e293b;
        }
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #92400e;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Test - Insertar Datos Completos</h1>
            <p>Sistema SAGRILAFT - Datos de Prueba</p>
        </div>
        
        <div class="content">
            <?php if (!isset($_POST['insert'])): ?>
                
                <div class="warning">
                    ⚠️ <strong>Advertencia:</strong> Este script insertará datos de prueba en la base de datos. 
                    Solo usar en ambiente de desarrollo.
                </div>
                
                <div class="info-box">
                    <h3>📋 Datos que se insertarán:</h3>
                    <ul>
                        <li><strong>Usuario:</strong> test@empresa-ejemplo.com (password: password)</li>
                        <li><strong>Empresa:</strong> EMPRESA EJEMPLO S.A.S.</li>
                        <li><strong>NIT:</strong> 900123456-7</li>
                        <li><strong>Tipo:</strong> Cliente Jurídica</li>
                        <li><strong>Representante:</strong> Juan Carlos Pérez Rodríguez</li>
                        <li><strong>Formulario:</strong> Completo con todos los campos</li>
                        <li><strong>Declaración:</strong> Origen de fondos completa</li>
                        <li><strong>Adjuntos:</strong> 3 archivos de ejemplo</li>
                    </ul>
                </div>
                
                <div class="info-box">
                    <h3>✅ Incluye:</h3>
                    <ul>
                        <li>✓ Datos básicos de la empresa</li>
                        <li>✓ Información del representante legal</li>
                        <li>✓ Actividad económica y CIIU</li>
                        <li>✓ Información financiera completa</li>
                        <li>✓ 2 Accionistas con participación</li>
                        <li>✓ 2 Referencias comerciales</li>
                        <li>✓ Información PEP</li>
                        <li>✓ Operaciones internacionales</li>
                        <li>✓ Declaración de origen de fondos</li>
                        <li>✓ 3 Documentos adjuntos</li>
                    </ul>
                </div>
                
                <form method="POST">
                    <div class="actions">
                        <button type="submit" name="insert" class="btn">
                            🚀 Insertar Datos de Prueba
                        </button>
                        <a href="<?= $_ENV['APP_URL'] ?? '/' ?>" class="btn btn-secondary">
                            ← Volver al Inicio
                        </a>
                    </div>
                </form>
                
            <?php else:
                // Insertar datos
                try {
                    $db = \App\Core\Database::getConnection();
                    $db->beginTransaction();
                    
                    // 1. Insertar usuario
                    $stmt = $db->prepare("
                        INSERT INTO users (email, password, role, document_type, document_number, phone, created_at)
                        VALUES (?, ?, 'user', 'NIT', '900123456-7', '3001234567', NOW())
                    ");
                    $stmt->execute([
                        'test@empresa-ejemplo.com',
                        password_hash('password', PASSWORD_DEFAULT)
                    ]);
                    $userId = $db->lastInsertId();
                    
                    // 2. Insertar formulario principal
                    $accionistas = json_encode([
                        [
                            'nombre' => 'María Fernanda López García',
                            'documento' => '52987654',
                            'participacion' => '60',
                            'nacionalidad' => 'Colombiana',
                            'cc' => '1',
                            'ce' => '0'
                        ],
                        [
                            'nombre' => 'Carlos Alberto Martínez Silva',
                            'documento' => '80456789',
                            'participacion' => '40',
                            'nacionalidad' => 'Colombiana',
                            'cc' => '1',
                            'ce' => '0'
                        ]
                    ]);
                    
                    $referencias = json_encode([
                        [
                            'empresa' => 'DISTRIBUIDORA NACIONAL S.A.',
                            'contacto' => 'Ana María Gómez',
                            'telefono' => '6014567890',
                            'email' => 'agomez@distribuidora.com'
                        ],
                        [
                            'empresa' => 'COMERCIALIZADORA DEL SUR LTDA',
                            'contacto' => 'Roberto Sánchez',
                            'telefono' => '6027891234',
                            'email' => 'rsanchez@comsur.com'
                        ]
                    ]);
                    
                    $stmt = $db->prepare("
                        INSERT INTO forms (
                            user_id, form_type, person_type,
                            company_name, razon_social, nit,
                            address, ciudad, departamento, pais,
                            phone, telefono_fijo, celular, email, pagina_web,
                            representante_nombre, representante_documento, representante_cargo,
                            representante_telefono, representante_email,
                            activity, codigo_ciiu,
                            ingresos_mensuales, egresos_mensuales, total_activos, total_pasivos, numero_empleados,
                            accionistas, referencias_comerciales,
                            es_pep, familiar_pep, cuentas_exterior,
                            opera_moneda_extranjera, paises_operacion,
                            approval_status, approval_token, created_at, updated_at
                        ) VALUES (
                            ?, 'cliente', 'juridica',
                            'EMPRESA EJEMPLO S.A.S.', 'EMPRESA EJEMPLO SOCIEDAD POR ACCIONES SIMPLIFICADA', '900123456-7',
                            'Calle 100 # 19-45 Oficina 501', 'Bogotá D.C.', 'Cundinamarca', 'Colombia',
                            '6013001234', '6013001234', '3001234567', 'contacto@empresa-ejemplo.com', 'www.empresa-ejemplo.com',
                            'Juan Carlos Pérez Rodríguez', '79123456', 'Gerente General',
                            '3101234567', 'jperez@empresa-ejemplo.com',
                            'Comercialización de productos alimenticios al por mayor', '4631',
                            50000000, 35000000, 500000000, 200000000, 25,
                            ?, ?,
                            'no', 'no', 'no',
                            'si', 'Ecuador, Perú',
                            'pending', MD5(CONCAT('test_', NOW(), RAND())), NOW(), NOW()
                        )
                    ");
                    $stmt->execute([$userId, $accionistas, $referencias]);
                    $formId = $db->lastInsertId();
                    
                    // 3. Insertar declaración
                    $stmt = $db->prepare("
                        INSERT INTO forms (
                            user_id, form_type, person_type, related_form_id,
                            nombre_declarante, tipo_documento, numero_documento, calidad,
                            company_name, nit, ciudad,
                            origen_fondos, origen_recursos,
                            es_pep, familiar_pep, 
                            cuentas_exterior, cuentas_exterior_detalle,
                            fecha_declaracion, ciudad_declaracion,
                            approval_status, approval_token, created_at, updated_at
                        ) VALUES (
                            ?, 'declaracion_fondos_clientes', 'declaracion', ?,
                            'Juan Carlos Pérez Rodríguez', 'CC', '79123456', 'Representante Legal',
                            'EMPRESA EJEMPLO S.A.S.', '900123456-7', 'Bogotá D.C.',
                            'Actividades comerciales lícitas de compra y venta de productos alimenticios. Los recursos provienen de las utilidades generadas por la operación comercial de la empresa.',
                            'Comercialización de productos alimenticios',
                            'no', 'no',
                            'si', 'Estados Unidos - Cuenta operativa para importaciones',
                            CURDATE(), 'Bogotá D.C.',
                            'pending', MD5(CONCAT('test_decl_', NOW(), RAND())), NOW(), NOW()
                        )
                    ");
                    $stmt->execute([$userId, $formId]);
                    
                    $db->commit();
                    
                    // Obtener token de aprobación
                    $stmt = $db->prepare("SELECT approval_token FROM forms WHERE id = ?");
                    $stmt->execute([$formId]);
                    $token = $stmt->fetchColumn();
                    
                    $appUrl = $_ENV['APP_URL'] ?? 'http://localhost/gestion-sagrilaft/public';
                    
                    ?>
                    <div class="result">
                        <h3>✅ Datos insertados correctamente</h3>
                        <pre><?php
                        echo "Usuario ID: {$userId}\n";
                        echo "Formulario ID: {$formId}\n";
                        echo "\n=== CREDENCIALES DE ACCESO ===\n";
                        echo "Email: test@empresa-ejemplo.com\n";
                        echo "Password: password\n";
                        echo "\n=== ENLACES ÚTILES ===\n";
                        echo "Ver formulario: {$appUrl}/forms/{$formId}/view\n";
                        echo "Aprobar formulario: {$appUrl}/approval/{$token}\n";
                        ?></pre>
                    </div>
                    
                    <div class="actions">
                        <a href="<?= $appUrl ?>/forms/<?= $formId ?>/view" class="btn" target="_blank">
                            📄 Ver Formulario Completo
                        </a>
                        <a href="<?= $appUrl ?>/approval/<?= $token ?>" class="btn" target="_blank">
                            ✓ Ir a Aprobación
                        </a>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">
                            🔄 Insertar Otro
                        </a>
                    </div>
                    <?php
                    
                } catch (Exception $e) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    ?>
                    <div class="result error">
                        <h3>❌ Error al insertar datos</h3>
                        <pre><?= htmlspecialchars($e->getMessage()) ?></pre>
                    </div>
                    <div class="actions">
                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">
                            ← Volver a Intentar
                        </a>
                    </div>
                    <?php
                }
            endif; ?>
        </div>
    </div>
</body>
</html>
