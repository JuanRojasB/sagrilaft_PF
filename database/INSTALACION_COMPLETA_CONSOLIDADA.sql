-- ============================================================================
-- INSTALACIÓN COMPLETA: Sistema SAGRILAFT
-- ============================================================================
-- Este archivo contiene TODA la estructura de la base de datos consolidada
-- Incluye todas las migraciones aplicadas hasta la fecha
-- Versión: 2026-04-10
-- ============================================================================

CREATE DATABASE IF NOT EXISTS sagrilaft CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sagrilaft;

-- ============================================================================
-- TABLA: users
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'revisor', 'usuario', 'cliente', 'proveedor', 'transportista', 'otros') DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: actividades_economicas
-- ============================================================================
CREATE TABLE IF NOT EXISTS actividades_economicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(10) NOT NULL UNIQUE,
    descripcion TEXT NOT NULL,
    sector VARCHAR(100) DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_sector (sector)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: asesores_comerciales
-- ============================================================================
CREATE TABLE IF NOT EXISTS asesores_comerciales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_cc VARCHAR(20) NOT NULL UNIQUE COMMENT 'Cédula del empleado',
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    fecha_ingreso DATE,
    sede VARCHAR(100),
    descripcion_sede TEXT,
    jefe_nombre VARCHAR(255),
    jefe_email VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_empleado_cc (empleado_cc),
    INDEX idx_email (email),
    INDEX idx_activo (activo),
    INDEX idx_jefe_email (jefe_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: forms_sagrilaft (TABLA PRINCIPAL - RENOMBRADA DE forms)
-- ============================================================================
CREATE TABLE IF NOT EXISTS forms_sagrilaft (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    
    -- DATOS GENERALES
    company_name VARCHAR(255),
    sucursal VARCHAR(255),
    nombre_establecimiento VARCHAR(255),
    nit VARCHAR(50),
    address VARCHAR(500),
    ciudad VARCHAR(100),
    barrio VARCHAR(100),
    localidad VARCHAR(100),
    telefono_fijo VARCHAR(50),
    celular VARCHAR(50),
    phone VARCHAR(50),
    email VARCHAR(255),
    fax VARCHAR(50),
    pais VARCHAR(100) COMMENT 'Para proveedores internacionales',
    
    -- ACTIVIDAD ECONÓMICA
    activity TEXT,
    codigo_ciiu VARCHAR(20),
    objeto_social TEXT COMMENT 'Descripción breve del objeto social',
    
    -- INFORMACIÓN FINANCIERA
    activos DECIMAL(15,2),
    pasivos DECIMAL(15,2),
    patrimonio DECIMAL(15,2),
    ingresos DECIMAL(15,2),
    gastos DECIMAL(15,2),
    otros_ingresos DECIMAL(15,2),
    detalle_otros_ingresos TEXT,
    
    -- DATOS TRIBUTARIOS
    tipo_contribuyente ENUM('persona_juridica', 'gran_contribuyente'),
    regimen_tributario ENUM('especial', 'no_contribuyente'),
    
    -- REPRESENTANTE LEGAL / PROPIETARIO
    representante_nombre VARCHAR(255),
    representante_documento VARCHAR(50),
    representante_tipo_doc ENUM('cc', 'ce', 'otro'),
    representante_profesion VARCHAR(100),
    representante_nacimiento DATE,
    representante_telefono VARCHAR(50),
    representante_direccion VARCHAR(500),
    
    -- ACCIONISTAS/ASOCIADOS (JSON para múltiples)
    accionistas JSON COMMENT 'Array de accionistas con >5% capital',
    
    -- DATOS ESPECÍFICOS CLIENTES
    lista_precios VARCHAR(100),
    codigo_vendedor VARCHAR(50),
    tipo_pago ENUM('contado', 'credito'),
    cupo_credito DECIMAL(15,2),
    fecha_nacimiento DATE,
    
    -- DATOS ESPECÍFICOS PROVEEDORES
    tipo_compania ENUM('privada', 'publica', 'mixtas'),
    persona_contacto VARCHAR(255) COMMENT 'Para comercio exterior',
    tiene_certificacion ENUM('si', 'no'),
    cual_certificacion VARCHAR(255),
    
    -- INFORMACIÓN IMPORTACIÓN (Proveedores Internacionales)
    concepto_importacion TEXT,
    declaracion_importacion VARCHAR(255),
    certificado_origen VARCHAR(255),
    certificado_transporte VARCHAR(255),
    certificado_fitosanitario VARCHAR(255),
    copia_swift VARCHAR(255),
    
    -- DECLARACIÓN ORIGEN DE FONDOS
    origen_fondos TEXT COMMENT 'Actividad que genera el vínculo comercial',
    es_pep ENUM('si', 'no') COMMENT 'Persona Expuesta Políticamente',
    cargo_pep VARCHAR(255),
    fecha_vinculacion_pep DATE,
    fecha_desvinculacion_pep DATE,
    relacion_pep VARCHAR(255) COMMENT 'Parentesco con PEP',
    identificacion_pep VARCHAR(255),
    familiares_pep TEXT COMMENT '2do grado consanguinidad',
    tiene_cuentas_exterior ENUM('si', 'no'),
    pais_cuentas_exterior VARCHAR(100),
    
    -- AUTORIZACIONES
    autoriza_centrales_riesgo ENUM('si', 'no') DEFAULT 'si',
    consulta_ofac ENUM('negativa', 'positiva'),
    consulta_listas_nacionales ENUM('negativa', 'positiva'),
    consulta_onu ENUM('negativa', 'positiva'),
    
    -- FIRMA Y SELLO
    nombre_firmante VARCHAR(255),
    clase_cliente VARCHAR(100),
    descripcion_firma TEXT,
    
    -- CAMPOS INTERNOS
    director_cartera VARCHAR(255),
    gerencia_comercial VARCHAR(255),
    oficial_cumplimiento VARCHAR(255),
    fecha_oficial_cumplimiento DATE,
    
    -- PDF GENERADO
    generated_pdf_content LONGBLOB COMMENT 'Contenido del PDF generado',
    generated_pdf_filename VARCHAR(255) COMMENT 'Nombre del archivo PDF generado',
    generated_pdf_size INT COMMENT 'Tamaño del PDF en bytes',
    generated_pdf_path VARCHAR(500) COMMENT 'Ruta del PDF generado (legacy)',
    pdf_mime_type VARCHAR(100) DEFAULT 'application/pdf' COMMENT 'Tipo MIME del PDF',
    pdf_generated_at DATETIME COMMENT 'Fecha de generación del PDF',
    
    -- CONTROL Y ESTADO
    form_type ENUM('cliente', 'proveedor', 'transportista', 'declaracion_cliente', 'declaracion_proveedor', 'empleado') DEFAULT 'cliente',
    person_type ENUM('natural', 'juridica') DEFAULT 'natural',
    status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft',
    approval_status ENUM('pending', 'approved', 'rejected', 'approved_pending', 'corrected') DEFAULT 'pending',
    approval_token VARCHAR(255) UNIQUE,
    approval_date DATETIME,
    approved_by VARCHAR(255),
    reviewer_comments TEXT,
    
    -- RELACIONES
    related_form_id INT COMMENT 'ID del formulario relacionado (para declaraciones)',
    asesor_comercial_id INT NULL COMMENT 'ID del asesor comercial asignado',
    
    -- TIMESTAMPS
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- ÍNDICES
    INDEX idx_user_id (user_id),
    INDEX idx_nit (nit),
    INDEX idx_status (status),
    INDEX idx_approval_status (approval_status),
    INDEX idx_approval_token (approval_token),
    INDEX idx_form_type (form_type),
    INDEX idx_person_type (person_type),
    INDEX idx_created_at (created_at),
    INDEX idx_related_form (related_form_id),
    INDEX idx_pdf_generated (pdf_generated_at),
    INDEX idx_asesor_comercial (asesor_comercial_id),
    
    -- FOREIGN KEYS
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (related_form_id) REFERENCES forms_sagrilaft(id) ON DELETE SET NULL,
    FOREIGN KEY (asesor_comercial_id) REFERENCES asesores_comerciales(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: form_empleados (DATOS ESPECÍFICOS DE EMPLEADOS)
-- ============================================================================
CREATE TABLE IF NOT EXISTS form_empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    empleado_nombre VARCHAR(255) NOT NULL,
    empleado_cedula VARCHAR(50) NOT NULL,
    empleado_cargo VARCHAR(255) NOT NULL,
    empleado_ciudad_vacante VARCHAR(255) NULL,
    empleado_ciudad_nacimiento VARCHAR(255) NULL,
    empleado_fecha_nacimiento DATE NOT NULL,
    empleado_celular VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES forms_sagrilaft(id) ON DELETE CASCADE,
    INDEX idx_form_id (form_id),
    INDEX idx_cedula (empleado_cedula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: form_attachments (ADJUNTOS DE FORMULARIOS)
-- ============================================================================
CREATE TABLE IF NOT EXISTS form_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    file_content LONGBLOB COMMENT 'Contenido del archivo adjunto',
    filesize INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_form_id (form_id),
    FOREIGN KEY (form_id) REFERENCES forms_sagrilaft(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: form_consolidated_pdfs
-- ============================================================================
CREATE TABLE IF NOT EXISTS form_consolidated_pdfs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    filesize INT NOT NULL,
    page_count INT,
    is_signed BOOLEAN DEFAULT FALSE,
    signature_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_form_id (form_id),
    INDEX idx_is_signed (is_signed),
    FOREIGN KEY (form_id) REFERENCES forms_sagrilaft(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: firmas_digitales
-- ============================================================================
CREATE TABLE IF NOT EXISTS firmas_digitales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    firma_path VARCHAR(500) NOT NULL,
    firma_filename VARCHAR(255) NOT NULL,
    firma_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (is_active),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DATOS INICIALES: Usuarios
-- ============================================================================

INSERT INTO users (name, email, password, role) VALUES 
('Administrador', 'admin@sagrilaft.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Revisor', 'revisor@sagrilaft.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'revisor')
ON DUPLICATE KEY UPDATE name=name;

-- ============================================================================
-- DATOS INICIALES: Actividades Económicas (Códigos CIIU)
-- ============================================================================

-- AGRICULTURA, GANADERÍA, CAZA, SILVICULTURA Y PESCA
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('111', 'Cultivo de cereales (excepto arroz), legumbres y semillas oleaginosas', 'Agricultura'),
('112', 'Cultivo de arroz', 'Agricultura'),
('113', 'Cultivo de hortalizas, raíces y tubérculos', 'Agricultura'),
('114', 'Cultivo de tabaco', 'Agricultura'),
('115', 'Cultivo de plantas textiles', 'Agricultura'),
('119', 'Otros cultivos transitorios n.c.p.', 'Agricultura'),
('121', 'Cultivo de frutas tropicales y subtropicales', 'Agricultura'),
('122', 'Cultivo de plátano y banano', 'Agricultura'),
('123', 'Cultivo de café', 'Agricultura'),
('124', 'Cultivo de caña de azúcar', 'Agricultura'),
('125', 'Cultivo de flor de corte', 'Agricultura'),
('126', 'Cultivo de palma para aceite (palma africana) y otros frutos oleaginosos', 'Agricultura'),
('127', 'Cultivo de plantas con las que se preparan bebidas', 'Agricultura'),
('128', 'Cultivo de especias y de plantas aromáticas y medicinales', 'Agricultura'),
('129', 'Otros cultivos permanentes n.c.p.', 'Agricultura'),
('130', 'Propagación de plantas (actividades de los viveros, excepto viveros forestales)', 'Agricultura'),
('141', 'Cría de ganado bovino y bufalino', 'Agricultura'),
('142', 'Cría de caballos y otros equinos', 'Agricultura'),
('143', 'Cría de ovejas y cabras', 'Agricultura'),
('144', 'Cría de ganado porcino', 'Agricultura'),
('145', 'Cría de aves de corral', 'Agricultura'),
('149', 'Cría de otros animales n.c.p.', 'Agricultura'),
('150', 'Explotación mixta (agrícola y pecuaria)', 'Agricultura'),
('161', 'Actividades de apoyo a la agricultura', 'Agricultura'),
('162', 'Actividades de apoyo a la ganadería', 'Agricultura'),
('163', 'Actividades posteriores a la cosecha', 'Agricultura'),
('164', 'Tratamiento de semillas para propagación', 'Agricultura'),
('170', 'Caza ordinaria y mediante trampas y actividades de servicios conexas', 'Agricultura'),
('210', 'Silvicultura y otras actividades forestales', 'Agricultura'),
('220', 'Extracción de madera', 'Agricultura'),
('230', 'Recolección de productos forestales diferentes a la madera', 'Agricultura'),
('240', 'Servicios de apoyo a la silvicultura', 'Agricultura'),
('311', 'Pesca marítima', 'Agricultura'),
('312', 'Pesca de agua dulce', 'Agricultura'),
('321', 'Acuicultura marítima', 'Agricultura'),
('322', 'Acuicultura de agua dulce', 'Agricultura')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- MINERÍA
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('510', 'Extracción de hulla (carbón de piedra)', 'Minería'),
('520', 'Extracción de carbón lignito', 'Minería'),
('610', 'Extracción de petróleo crudo', 'Minería'),
('620', 'Extracción de gas natural', 'Minería'),
('710', 'Extracción de minerales de hierro', 'Minería'),
('721', 'Extracción de minerales de uranio y de torio', 'Minería'),
('722', 'Extracción de oro y otros metales preciosos', 'Minería'),
('723', 'Extracción de minerales de níquel', 'Minería'),
('729', 'Extracción de otros minerales metalíferos no ferrosos n.c.p.', 'Minería'),
('811', 'Extracción de piedra, arena, arcillas comunes, yeso y anhidrita', 'Minería'),
('812', 'Extracción de arcillas de uso industrial, caliza, caolín y bentonitas', 'Minería'),
('820', 'Extracción de esmeraldas, piedras preciosas y semipreciosas', 'Minería'),
('891', 'Extracción de minerales para la fabricación de abonos y productos químicos', 'Minería'),
('892', 'Extracción de halita (sal)', 'Minería'),
('899', 'Extracción de otros minerales no metálicos n.c.p.', 'Minería'),
('910', 'Actividades de apoyo para la extracción de petróleo y de gas natural', 'Minería'),
('990', 'Actividades de apoyo para otras actividades de explotación de minas y canteras', 'Minería')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- INDUSTRIA MANUFACTURERA (Selección representativa)
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('1011', 'Procesamiento y conservación de carne y productos cárnicos', 'Industria'),
('1012', 'Procesamiento y conservación de pescados, crustáceos y moluscos', 'Industria'),
('1020', 'Procesamiento y conservación de frutas, legumbres, hortalizas y tubérculos', 'Industria'),
('1030', 'Elaboración de aceites y grasas de origen vegetal y animal', 'Industria'),
('1040', 'Elaboración de productos lácteos', 'Industria'),
('1051', 'Elaboración de productos de molinería', 'Industria'),
('1061', 'Trilla de café', 'Industria'),
('1062', 'Descafeinado, tostión y molienda del café', 'Industria'),
('1071', 'Elaboración y refinación de azúcar', 'Industria'),
('1072', 'Elaboración de panela', 'Industria'),
('1081', 'Elaboración de productos de panadería', 'Industria'),
('1082', 'Elaboración de cacao, chocolate y productos de confitería', 'Industria'),
('1089', 'Elaboración de otros productos alimenticios n.c.p.', 'Industria'),
('1090', 'Elaboración de alimentos preparados para animales', 'Industria'),
('1101', 'Destilación, rectificación y mezcla de bebidas alcohólicas', 'Industria'),
('1103', 'Producción de malta, elaboración de cervezas y otras bebidas malteadas', 'Industria'),
('1104', 'Elaboración de bebidas no alcohólicas, producción de aguas minerales', 'Industria')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- CONSTRUCCIÓN
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('4111', 'Construcción de edificios residenciales', 'Construcción'),
('4112', 'Construcción de edificios no residenciales', 'Construcción'),
('4210', 'Construcción de carreteras y vías de ferrocarril', 'Construcción'),
('4220', 'Construcción de proyectos de servicio público', 'Construcción'),
('4290', 'Construcción de otras obras de ingeniería civil', 'Construcción'),
('4311', 'Demolición', 'Construcción'),
('4312', 'Preparación del terreno', 'Construcción'),
('4321', 'Instalaciones eléctricas', 'Construcción'),
('4322', 'Instalaciones de fontanería, calefacción y aire acondicionado', 'Construcción'),
('4329', 'Otras instalaciones especializadas', 'Construcción'),
('4330', 'Terminación y acabado de edificios y obras de ingeniería civil', 'Construcción'),
('4390', 'Otras actividades especializadas para la construcción', 'Construcción')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- COMERCIO
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('4511', 'Comercio de vehículos automotores nuevos', 'Comercio'),
('4512', 'Comercio de vehículos automotores usados', 'Comercio'),
('4520', 'Mantenimiento y reparación de vehículos automotores', 'Comercio'),
('4530', 'Comercio de partes, piezas y accesorios para vehículos automotores', 'Comercio'),
('4610', 'Comercio al por mayor a cambio de una retribución o por contrata', 'Comercio'),
('4620', 'Comercio al por mayor de materias primas agropecuarias', 'Comercio'),
('4631', 'Comercio al por mayor de productos alimenticios', 'Comercio'),
('4632', 'Comercio al por mayor de bebidas y tabaco', 'Comercio'),
('4641', 'Comercio al por mayor de productos textiles', 'Comercio'),
('4642', 'Comercio al por mayor de prendas de vestir', 'Comercio'),
('4643', 'Comercio al por mayor de calzado', 'Comercio'),
('4644', 'Comercio al por mayor de aparatos y equipo de uso doméstico', 'Comercio'),
('4645', 'Comercio al por mayor de productos farmacéuticos y medicinales', 'Comercio'),
('4651', 'Comercio al por mayor de computadores y programas de informática', 'Comercio'),
('4711', 'Comercio al por menor en establecimientos no especializados', 'Comercio'),
('4721', 'Comercio al por menor de productos agrícolas', 'Comercio'),
('4722', 'Comercio al por menor de leche, productos lácteos y huevos', 'Comercio'),
('4723', 'Comercio al por menor de carnes y productos cárnicos', 'Comercio'),
('4724', 'Comercio al por menor de bebidas y productos del tabaco', 'Comercio'),
('4731', 'Comercio al por menor de combustible para automotores', 'Comercio'),
('4741', 'Comercio al por menor de computadores y equipos periféricos', 'Comercio'),
('4751', 'Comercio al por menor de productos textiles', 'Comercio'),
('4752', 'Comercio al por menor de artículos de ferretería y pinturas', 'Comercio'),
('4771', 'Comercio al por menor de prendas de vestir', 'Comercio'),
('4773', 'Comercio al por menor de productos farmacéuticos', 'Comercio')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- TRANSPORTE Y ALMACENAMIENTO
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('4911', 'Transporte férreo de pasajeros', 'Transporte'),
('4912', 'Transporte férreo de carga', 'Transporte'),
('4921', 'Transporte de pasajeros', 'Transporte'),
('4922', 'Transporte mixto', 'Transporte'),
('4923', 'Transporte de carga por carretera', 'Transporte'),
('4930', 'Transporte por tuberías', 'Transporte'),
('5011', 'Transporte de pasajeros marítimo y de cabotaje', 'Transporte'),
('5012', 'Transporte de carga marítimo y de cabotaje', 'Transporte'),
('5021', 'Transporte fluvial de pasajeros', 'Transporte'),
('5022', 'Transporte fluvial de carga', 'Transporte'),
('5111', 'Transporte aéreo nacional de pasajeros', 'Transporte'),
('5112', 'Transporte aéreo internacional de pasajeros', 'Transporte'),
('5121', 'Transporte aéreo nacional de carga', 'Transporte'),
('5122', 'Transporte aéreo internacional de carga', 'Transporte'),
('5210', 'Almacenamiento y depósito', 'Transporte'),
('5221', 'Actividades de estaciones y vías para el transporte terrestre', 'Transporte'),
('5222', 'Actividades de puertos para el transporte acuático', 'Transporte'),
('5223', 'Actividades de aeropuertos y servicios de navegación aérea', 'Transporte'),
('5224', 'Manipulación de carga', 'Transporte'),
('5310', 'Actividades postales nacionales', 'Transporte'),
('5320', 'Actividades de mensajería', 'Transporte')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ALOJAMIENTO Y SERVICIOS DE COMIDA
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('5511', 'Alojamiento en hoteles', 'Servicios'),
('5512', 'Alojamiento en apartahoteles', 'Servicios'),
('5513', 'Alojamiento en centros vacacionales', 'Servicios'),
('5519', 'Otros tipos de alojamientos para visitantes', 'Servicios'),
('5611', 'Expendio a la mesa de comidas preparadas', 'Servicios'),
('5612', 'Expendio por autoservicio de comidas preparadas', 'Servicios'),
('5613', 'Expendio de comidas preparadas en cafeterías', 'Servicios'),
('5621', 'Catering para eventos', 'Servicios'),
('5630', 'Expendio de bebidas alcohólicas para el consumo dentro del establecimiento', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- INFORMACIÓN Y COMUNICACIONES
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('5811', 'Edición de libros', 'Servicios'),
('5813', 'Edición de periódicos, revistas y otras publicaciones periódicas', 'Servicios'),
('5820', 'Edición de programas de informática (software)', 'Servicios'),
('6010', 'Actividades de programación y transmisión en el servicio de radiodifusión sonora', 'Servicios'),
('6020', 'Actividades de programación y transmisión de televisión', 'Servicios'),
('6110', 'Actividades de telecomunicaciones alámbricas', 'Servicios'),
('6120', 'Actividades de telecomunicaciones inalámbricas', 'Servicios'),
('6190', 'Otras actividades de telecomunicaciones', 'Servicios'),
('6201', 'Actividades de desarrollo de sistemas informáticos', 'Servicios'),
('6202', 'Actividades de consultoría informática', 'Servicios'),
('6311', 'Procesamiento de datos, alojamiento (hosting) y actividades relacionadas', 'Servicios'),
('6312', 'Portales web', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ACTIVIDADES FINANCIERAS Y DE SEGUROS
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('6411', 'Banco Central', 'Servicios'),
('6412', 'Bancos comerciales', 'Servicios'),
('6421', 'Actividades de las corporaciones financieras', 'Servicios'),
('6422', 'Actividades de las compañías de financiamiento', 'Servicios'),
('6424', 'Actividades de las cooperativas financieras', 'Servicios'),
('6511', 'Seguros generales', 'Servicios'),
('6512', 'Seguros de vida', 'Servicios'),
('6521', 'Servicios de seguros sociales de salud', 'Servicios'),
('6522', 'Servicios de seguros sociales en riesgos laborales', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ACTIVIDADES INMOBILIARIAS
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('6810', 'Actividades inmobiliarias realizadas con bienes propios o arrendados', 'Servicios'),
('6820', 'Actividades inmobiliarias realizadas a cambio de una retribución o por contrata', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ACTIVIDADES PROFESIONALES, CIENTÍFICAS Y TÉCNICAS
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('6910', 'Actividades jurídicas', 'Servicios'),
('6920', 'Actividades de contabilidad, teneduría de libros y auditoría', 'Servicios'),
('7010', 'Actividades de administración empresarial', 'Servicios'),
('7020', 'Actividades de consultoría de gestión', 'Servicios'),
('7111', 'Actividades de arquitectura', 'Servicios'),
('7112', 'Actividades de ingeniería y consultoría técnica', 'Servicios'),
('7120', 'Ensayos y análisis técnicos', 'Servicios'),
('7210', 'Investigación y desarrollo en ciencias naturales e ingeniería', 'Servicios'),
('7310', 'Publicidad', 'Servicios'),
('7320', 'Estudios de mercado y realización de encuestas de opinión pública', 'Servicios'),
('7410', 'Actividades especializadas de diseño', 'Servicios'),
('7420', 'Actividades de fotografía', 'Servicios'),
('7500', 'Actividades veterinarias', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ACTIVIDADES DE SERVICIOS ADMINISTRATIVOS Y DE APOYO
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('7710', 'Alquiler y arrendamiento de vehículos automotores', 'Servicios'),
('7810', 'Actividades de agencias de gestión y colocación de empleo', 'Servicios'),
('7820', 'Actividades de empresas de servicios temporales', 'Servicios'),
('7911', 'Actividades de las agencias de viaje', 'Servicios'),
('7912', 'Actividades de operadores turísticos', 'Servicios'),
('8010', 'Actividades de seguridad privada', 'Servicios'),
('8020', 'Actividades de servicios de sistemas de seguridad', 'Servicios'),
('8110', 'Actividades combinadas de apoyo a instalaciones', 'Servicios'),
('8121', 'Limpieza general interior de edificios', 'Servicios'),
('8220', 'Actividades de centros de llamadas (Call center)', 'Servicios'),
('8230', 'Organización de convenciones y eventos comerciales', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- EDUCACIÓN
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('8511', 'Educación de la primera infancia', 'Servicios'),
('8512', 'Educación preescolar', 'Servicios'),
('8513', 'Educación básica primaria', 'Servicios'),
('8521', 'Educación básica secundaria', 'Servicios'),
('8522', 'Educación media académica', 'Servicios'),
('8530', 'Establecimientos que combinan diferentes niveles de educación', 'Servicios'),
('8541', 'Educación técnica profesional', 'Servicios'),
('8542', 'Educación tecnológica', 'Servicios'),
('8543', 'Educación de instituciones universitarias o de escuelas tecnológicas', 'Servicios'),
('8544', 'Educación de universidades', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ACTIVIDADES DE ATENCIÓN DE LA SALUD HUMANA
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('8610', 'Actividades de hospitales y clínicas, con internación', 'Servicios'),
('8621', 'Actividades de la práctica médica, sin internación', 'Servicios'),
('8622', 'Actividades de la práctica odontológica', 'Servicios'),
('8691', 'Actividades de apoyo diagnóstico', 'Servicios'),
('8692', 'Actividades de apoyo terapéutico', 'Servicios'),
('8710', 'Actividades de atención residencial medicalizada', 'Servicios'),
('8810', 'Actividades de asistencia social sin alojamiento', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ACTIVIDADES ARTÍSTICAS Y DE ENTRETENIMIENTO
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('9001', 'Creación literaria', 'Servicios'),
('9002', 'Creación musical', 'Servicios'),
('9003', 'Creación teatral', 'Servicios'),
('9004', 'Creación audiovisual', 'Servicios'),
('9101', 'Actividades de bibliotecas y archivos', 'Servicios'),
('9102', 'Actividades y funcionamiento de museos', 'Servicios'),
('9200', 'Actividades de juegos de azar y apuestas', 'Servicios'),
('9311', 'Gestión de instalaciones deportivas', 'Servicios'),
('9312', 'Actividades de clubes deportivos', 'Servicios'),
('9321', 'Actividades de parques de atracciones y parques temáticos', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- OTRAS ACTIVIDADES DE SERVICIOS
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('9511', 'Mantenimiento y reparación de computadores', 'Servicios'),
('9512', 'Mantenimiento y reparación de equipos de comunicación', 'Servicios'),
('9521', 'Mantenimiento y reparación de aparatos electrónicos de consumo', 'Servicios'),
('9601', 'Lavado y limpieza de productos textiles y de piel', 'Servicios'),
('9602', 'Peluquería y otros tratamientos de belleza', 'Servicios'),
('9603', 'Pompas fúnebres y actividades relacionadas', 'Servicios'),
('9700', 'Actividades de los hogares como empleadores de personal doméstico', 'Servicios')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ============================================================================
-- DATOS INICIALES: Asesores Comerciales
-- ============================================================================

INSERT INTO asesores_comerciales (empleado_cc, nombre_completo, email, fecha_ingreso, sede, descripcion_sede, jefe_nombre, jefe_email, activo) VALUES
-- Distribuidora Fiesta Toberin (Jefe: RODRIGUEZ ESDGAR GERMAN)
('53000676', 'VARGAS PINILLA ANA MARCELA', 'marcela.vargas@pollo-fiesta.com', '2016-11-01', 'D05', 'DISTRIBUIDORA FIESTA-TOBERIN', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('91472769', 'MONROY CORTES CARLOS MANUEL', 'carlos.monroy@pollo-fiesta.com', '2024-05-14', 'D05', 'DISTRIBUIDORA FIESTA-TOBERIN', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('1093803653', 'HERNANDEZ ARIZA JOHNNY ALIRIO', 'jonhnny.hernandez@pollo-fiesta.com', '2024-05-14', 'D05', 'DISTRIBUIDORA FIESTA-TOBERIN', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),

-- UND FUNCIONAL ASADERO - EMPRESARIAL (Jefe: RODRIGUEZ ESDGAR GERMAN)
('19482319', 'ARENAS URREA LUIS ALBEIRO', 'albeiro.arenas@pollo-fiesta.com', '1994-07-18', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('51631698', 'CALDAS PALACIOS MARIA NANCY', 'nancy.caldas@pollo-fiesta.com', '1990-03-15', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('79903450', 'GIL CHIQUIZA JAVIER GONZALO', 'javier.gil@pollo-fiesta.com', '2013-11-25', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('14279394', 'GONZALEZ CONTRERAS JUAN CARLOS', 'juan.gonzalez@pollo-fiesta.com', '2005-08-16', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('52159001', 'SALAMANCA CAMACHO DORYS ADRIANA', 'adriana.salamanca@pollo-fiesta.com', '2010-03-01', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('37943525', 'SALAS MORENO MARIA FANNY', 'fanny.salas@pollo-fiesta.com', '2013-12-09', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('15904268', 'URREA RESTREPO LUIS GUSTAVO', 'gustavo.urrea@pollo-fiesta.com', '1999-06-16', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('52583090', 'VILLAMIL BERMUDEZ MARIELA', 'mariela.villamil@pollo-fiesta.com', '2001-10-22', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('52213001', 'CARDONA RAMIREZ JANETH', 'janeth.cardona@pollo-fiesta.com', '2018-06-25', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('1101756357', 'FANDINO FANDINO JENY AIDA', 'jeny.fandino@pollo-fiesta.com', '2012-03-06', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('51845295', 'PUENTES CORREA ALICIA', 'alicia.puentes@pollo-fiesta.com', '2019-07-02', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('1023867208', 'GAMBA PIRAGUA LENITH', 'lenith.gamba@pollo-fiesta.com', '2020-11-09', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('1016009906', 'RODRIGUEZ RODRIGUEZ YENNY PAOLA', 'yenny.rodriguez@pollo-fiesta.com', '2020-11-09', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('39568107', 'SEGURA CLEVES SANDRA MONICA', 'sandra.segura@pollo-fiesta.com', '2021-02-01', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('1075666080', 'GARNICA RODRIGUEZ NUBIA YAMILE', 'nubia.garnica@pollo-fiesta.com', '2021-05-03', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('80469096', 'OVALLE OCHOA DIEGO FERNANDO', 'diego.ovalle@pollo-fiesta.com', '2025-10-07', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
('80545278', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', '2007-08-21', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),

-- UND FUNCIONAL MODERNO (S3) (Jefe: BENITO GUEVARA HERNAN MATEO)
('79837241', 'GARCIA CASTRO EFREN', 'efren.garcia@pollo-fiesta.com', '2000-04-10', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('51821727', 'JAIMES SILVA SANDRA ROCIO', 'sandra.jaimes@pollo-fiesta.com', '2005-05-10', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('39749700', 'MEJIA GARCIA PATRICIA DE LOS ANGELES', 'patricia.mejia@pollo-fiesta.com', '2013-01-14', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('28683054', 'NAVARRO CELIA', 'celia.navarro@pollo-fiesta.com', '2013-02-18', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('79844147', 'RAMIREZ MALAGON JOHN ALEXANDER', 'jhon.ramirez@pollo-fiesta.com', '2013-04-15', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('52161793', 'CASTILLO QUINONEZ SANDRA ARMIDA', 'sandra.castillo@pollo-fiesta.com', '2016-09-05', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('52464859', 'RAMIREZ HERRERA SANDRA MILENA', 'sandra.ramirez@pollo-fiesta.com', '2020-11-09', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('53155047', 'MORALES PARADA SANDRA PATRICIA', 'sandra.morales@pollo-fiesta.com', '2025-09-01', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('52173826', 'BERNAL VASQUEZ ELIZABETH', 'Elizabeth.Bernal@pollo-fiesta.com', '2025-10-06', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE),
('11410967', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', '2004-03-03', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE)
ON DUPLICATE KEY UPDATE nombre_completo=VALUES(nombre_completo);

-- ============================================================================
-- VERIFICACIÓN Y RESUMEN
-- ============================================================================

SELECT 'Instalación completa exitosa - Base de datos SAGRILAFT' AS mensaje;

SELECT 
    TABLE_NAME as 'Tabla',
    TABLE_ROWS as 'Filas Aprox.',
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as 'Tamaño (MB)'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'sagrilaft'
ORDER BY TABLE_NAME;

SELECT COUNT(*) AS total_actividades FROM actividades_economicas;
SELECT COUNT(*) AS total_asesores FROM asesores_comerciales;
SELECT COUNT(*) AS total_usuarios FROM users;

-- ============================================================================
-- NOTAS IMPORTANTES
-- ============================================================================
-- 
-- ESTRUCTURA CONSOLIDADA:
-- ✓ Tabla principal: forms_sagrilaft (con todos los campos necesarios)
-- ✓ Tabla empleados: form_empleados (datos específicos de empleados)
-- ✓ Tabla adjuntos: form_attachments (con original_filename y file_content BLOB)
-- ✓ Tabla asesores: asesores_comerciales (gestión de asesores comerciales)
-- ✓ Actividades económicas: códigos CIIU representativos
-- 
-- TIPOS DE FORMULARIO (form_type):
-- - cliente: Formularios de clientes (natural/jurídica)
-- - proveedor: Formularios de proveedores (natural/jurídica)
-- - transportista: Formularios de transportistas (usan PDFs de proveedores)
-- - declaracion_cliente: Declaración de origen de fondos para clientes
-- - declaracion_proveedor: Declaración de origen de fondos para proveedores
-- - empleado: Registro de empleados (datos en tabla form_empleados)
-- 
-- TIPOS DE PERSONA (person_type):
-- - natural: Persona natural
-- - juridica: Persona jurídica
-- 
-- ESTADOS DE APROBACIÓN (approval_status):
-- - pending: Pendiente de revisión
-- - approved: Aprobado completamente
-- - rejected: Rechazado
-- - approved_pending: Aprobado con observaciones (requiere corrección)
-- - corrected: Corregido por el usuario
-- 
-- NOMENCLATURA DE FORMULARIOS:
-- FD-01: Cliente Persona Natural
-- FD-02: Proveedor Persona Jurídica
-- FD-03: Declaración Origen de Fondos Cliente
-- FD-04: Declaración Origen de Fondos Proveedor
-- FD-05: Proveedor Persona Natural
-- FD-06: Cliente Persona Jurídica
-- FD-07: Transportista Persona Jurídica (usa PDF de FD-02)
-- FD-08: Transportista Persona Natural (usa PDF de FD-05)
-- FD-09: Registro de Empleado
-- 
-- USUARIOS POR DEFECTO:
-- Admin: admin@sagrilaft.com / password
-- Revisor: revisor@sagrilaft.com / password
-- 
-- VALIDACIONES IMPORTANTES:
-- - Cédula empleado: 6-10 dígitos
-- - Celular empleado: 10 dígitos
-- - Tamaño máximo PDF: 10MB
-- - Asesor comercial: solo requerido para CLIENTES
-- - Empleados: solo pueden ser APROBADOS o RECHAZADOS (no "con observaciones")
-- 
-- NOTIFICACIONES:
-- - Empleados: se notifica a angie.rodriguez@pollo-fiesta.com
-- - Otros formularios: se notifica según configuración del asesor comercial
-- - Emails incluyen adjuntos según tipo de formulario
-- 
-- MIGRACIONES APLICADAS:
-- ✓ add_person_type.sql - Agregado campo person_type
-- ✓ add_empleado_type.sql - Agregado tipo 'empleado' al ENUM
-- ✓ add_empleado_fields.sql - Creada tabla form_empleados
-- ✓ fix_form_attachments.sql - Agregado campo original_filename
-- 
-- ============================================================================
-- FIN DE LA INSTALACIÓN
-- ============================================================================
