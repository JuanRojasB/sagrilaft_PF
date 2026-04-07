-- ============================================================================
-- SCRIPT DE PRUEBA - DATOS COMPLETOS PARA SAGRILAFT
-- ============================================================================
-- Este script inserta datos de ejemplo completos para probar el sistema
-- Incluye: Usuario, Formulario Principal, Declaración y Adjuntos
-- ============================================================================

-- Limpiar datos de prueba anteriores (opcional)
-- DELETE FROM form_attachments WHERE form_id IN (SELECT id FROM forms WHERE nit = '900123456-7');
-- DELETE FROM forms WHERE nit = '900123456-7';
-- DELETE FROM users WHERE email = 'test@empresa-ejemplo.com';

-- ============================================================================
-- 1. INSERTAR USUARIO DE PRUEBA
-- ============================================================================
INSERT INTO users (
    email, 
    password, 
    role, 
    document_type, 
    document_number, 
    phone, 
    created_at
) VALUES (
    'test@empresa-ejemplo.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'user',
    'NIT',
    '900123456-7',
    '3001234567',
    NOW()
);

SET @user_id = LAST_INSERT_ID();

-- ============================================================================
-- 2. INSERTAR FORMULARIO PRINCIPAL - CLIENTE JURÍDICA
-- ============================================================================
INSERT INTO forms (
    user_id,
    form_type,
    person_type,
    
    -- Datos básicos de la empresa
    company_name,
    nit,
    razon_social,
    
    -- Ubicación
    address,
    ciudad,
    departamento,
    pais,
    
    -- Contacto
    phone,
    telefono_fijo,
    celular,
    email,
    pagina_web,
    
    -- Representante Legal
    representante_nombre,
    representante_documento,
    representante_cargo,
    representante_telefono,
    representante_email,
    
    -- Actividad económica
    activity,
    codigo_ciiu,
    descripcion_ciiu,
    
    -- Información financiera
    ingresos_mensuales,
    egresos_mensuales,
    total_activos,
    total_pasivos,
    numero_empleados,
    
    -- Accionistas (JSON)
    accionistas,
    
    -- Referencias comerciales (JSON)
    referencias_comerciales,
    
    -- Información PEP
    es_pep,
    cargo_pep,
    familiar_pep,
    familiar_pep_detalle,
    
    -- Cuentas en el exterior
    cuentas_exterior,
    pais_cuenta_exterior,
    banco_exterior,
    
    -- Operaciones internacionales
    operaciones_internacionales,
    paises_operacion,
    moneda_extranjera,
    
    -- Estado
    approval_status,
    approval_token,
    created_at,
    updated_at
) VALUES (
    @user_id,
    'cliente',
    'juridica',
    
    -- Datos básicos
    'EMPRESA EJEMPLO S.A.S.',
    '900123456-7',
    'EMPRESA EJEMPLO SOCIEDAD POR ACCIONES SIMPLIFICADA',
    
    -- Ubicación
    'Calle 100 # 19-45 Oficina 501',
    'Bogotá D.C.',
    'Cundinamarca',
    'Colombia',
    
    -- Contacto
    '6013001234',
    '6013001234',
    '3001234567',
    'contacto@empresa-ejemplo.com',
    'www.empresa-ejemplo.com',
    
    -- Representante Legal
    'Juan Carlos Pérez Rodríguez',
    '79123456',
    'Gerente General',
    '3101234567',
    'jperez@empresa-ejemplo.com',
    
    -- Actividad económica
    'Comercialización de productos alimenticios al por mayor',
    '4631',
    'Comercio al por mayor de productos alimenticios',
    
    -- Información financiera
    50000000,
    35000000,
    500000000,
    200000000,
    25,
    
    -- Accionistas (JSON)
    '[
        {
            "nombre": "María Fernanda López García",
            "documento": "52987654",
            "participacion": "60",
            "nacionalidad": "Colombiana",
            "cc": "1",
            "ce": "0"
        },
        {
            "nombre": "Carlos Alberto Martínez Silva",
            "documento": "80456789",
            "participacion": "40",
            "nacionalidad": "Colombiana",
            "cc": "1",
            "ce": "0"
        }
    ]',
    
    -- Referencias comerciales (JSON)
    '[
        {
            "empresa": "DISTRIBUIDORA NACIONAL S.A.",
            "contacto": "Ana María Gómez",
            "telefono": "6014567890",
            "email": "agomez@distribuidora.com"
        },
        {
            "empresa": "COMERCIALIZADORA DEL SUR LTDA",
            "contacto": "Roberto Sánchez",
            "telefono": "6027891234",
            "email": "rsanchez@comsur.com"
        }
    ]',
    
    -- Información PEP
    'no',
    NULL,
    'no',
    NULL,
    
    -- Cuentas en el exterior
    'no',
    NULL,
    NULL,
    
    -- Operaciones internacionales
    'si',
    'Ecuador, Perú',
    'USD',
    
    -- Estado
    'pending',
    MD5(CONCAT('test_', NOW(), RAND())),
    NOW(),
    NOW()
);

SET @form_id = LAST_INSERT_ID();

-- ============================================================================
-- 3. INSERTAR DECLARACIÓN DE ORIGEN DE FONDOS
-- ============================================================================
INSERT INTO forms (
    user_id,
    form_type,
    person_type,
    related_form_id,
    
    -- Datos del declarante
    nombre_declarante,
    tipo_documento,
    numero_documento,
    calidad,
    
    -- Referencia a la empresa
    company_name,
    nit,
    ciudad,
    
    -- Declaración
    origen_fondos,
    origen_recursos,
    
    -- PEP
    es_pep,
    cargo_pep,
    familiar_pep,
    familiar_pep_detalle,
    
    -- Cuentas exterior
    cuentas_exterior,
    pais_cuenta_exterior,
    
    -- Firma
    firma_declarante_data,
    fecha_declaracion,
    ciudad_declaracion,
    
    -- Estado
    approval_status,
    approval_token,
    created_at,
    updated_at
) VALUES (
    @user_id,
    'declaracion_fondos_clientes',
    'declaracion',
    @form_id,
    
    -- Datos del declarante
    'Juan Carlos Pérez Rodríguez',
    'CC',
    '79123456',
    'Representante Legal',
    
    -- Referencia a la empresa
    'EMPRESA EJEMPLO S.A.S.',
    '900123456-7',
    'Bogotá D.C.',
    
    -- Declaración
    'Actividades comerciales lícitas de compra y venta de productos alimenticios. Los recursos provienen de las utilidades generadas por la operación comercial de la empresa, la cual se dedica a la comercialización de productos alimenticios al por mayor en el territorio nacional.',
    'Comercialización de productos alimenticios',
    
    -- PEP
    'no',
    NULL,
    'no',
    NULL,
    
    -- Cuentas exterior
    'si',
    'Estados Unidos - Cuenta operativa para importaciones',
    
    -- Firma (base64 de ejemplo - en producción sería una firma real)
    'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
    CURDATE(),
    'Bogotá D.C.',
    
    -- Estado
    'pending',
    MD5(CONCAT('test_decl_', NOW(), RAND())),
    NOW(),
    NOW()
);

-- ============================================================================
-- 4. ACTUALIZAR FORMULARIO PRINCIPAL CON PDF GENERADO
-- ============================================================================
-- Nota: En producción, el PDF se genera automáticamente
-- Aquí solo marcamos que existe para simular el flujo completo
UPDATE forms 
SET 
    generated_pdf_filename = CONCAT('form_', @form_id, '_', DATE_FORMAT(NOW(), '%Y%m%d_%H%i%s'), '.pdf'),
    pdf_generated_at = NOW()
WHERE id = @form_id;

-- ============================================================================
-- 5. INSERTAR ADJUNTOS DE EJEMPLO
-- ============================================================================
-- Nota: En producción, estos serían archivos reales
-- Aquí insertamos registros de ejemplo sin contenido binario

INSERT INTO form_attachments (
    form_id,
    filename,
    original_filename,
    mime_type,
    file_size,
    file_data,
    uploaded_at
) VALUES 
(
    @form_id,
    'rut_empresa_ejemplo.pdf',
    'RUT_EMPRESA_EJEMPLO_SAS.pdf',
    'application/pdf',
    524288,
    NULL, -- En producción sería el contenido binario del archivo
    NOW()
),
(
    @form_id,
    'camara_comercio.pdf',
    'Certificado_Camara_Comercio_2024.pdf',
    'application/pdf',
    1048576,
    NULL,
    NOW()
),
(
    @form_id,
    'estados_financieros.pdf',
    'Estados_Financieros_2024.pdf',
    'application/pdf',
    2097152,
    NULL,
    NOW()
);

-- ============================================================================
-- RESUMEN DE DATOS INSERTADOS
-- ============================================================================
SELECT 
    '✓ Datos de prueba insertados correctamente' AS status,
    @user_id AS user_id,
    @form_id AS form_id,
    (SELECT COUNT(*) FROM forms WHERE user_id = @user_id) AS total_forms,
    (SELECT COUNT(*) FROM form_attachments WHERE form_id = @form_id) AS total_attachments;

-- ============================================================================
-- CONSULTAS ÚTILES PARA VERIFICAR
-- ============================================================================
-- Ver el usuario creado
SELECT id, email, document_type, document_number, role, created_at 
FROM users 
WHERE id = @user_id;

-- Ver el formulario principal
SELECT 
    id, 
    form_type, 
    person_type, 
    company_name, 
    nit, 
    approval_status,
    approval_token,
    created_at
FROM forms 
WHERE id = @form_id;

-- Ver la declaración
SELECT 
    id, 
    form_type, 
    related_form_id, 
    nombre_declarante, 
    origen_fondos,
    approval_status
FROM forms 
WHERE related_form_id = @form_id;

-- Ver los adjuntos
SELECT 
    id, 
    filename, 
    mime_type, 
    file_size, 
    uploaded_at
FROM form_attachments 
WHERE form_id = @form_id;

-- ============================================================================
-- INFORMACIÓN PARA ACCEDER AL SISTEMA
-- ============================================================================
SELECT 
    'INFORMACIÓN DE ACCESO' AS info,
    'Email: test@empresa-ejemplo.com' AS credenciales,
    'Password: password' AS password_info,
    CONCAT('Token de aprobación: ', approval_token) AS approval_link
FROM forms 
WHERE id = @form_id;
