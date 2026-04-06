-- ============================================================================
-- SCHEMA COMPLETO: Sistema SAGRILAFT
-- ============================================================================
-- Base de datos completa con todas las tablas necesarias
-- Tabla principal: forms_sagrilaft (renombrada de forms)
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
    role ENUM('admin', 'revisor', 'usuario') DEFAULT 'usuario',
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: forms_sagrilaft (TABLA PRINCIPAL)
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
    form_type ENUM('cliente', 'proveedor', 'transportista') DEFAULT 'cliente',
    status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft',
    approval_status ENUM('pending', 'approved', 'rejected', 'approved_pending', 'corrected') DEFAULT 'pending',
    approval_token VARCHAR(255) UNIQUE,
    approval_date DATETIME,
    approved_by VARCHAR(255),
    reviewer_comments TEXT,
    
    -- RELACIONES
    related_form_id INT COMMENT 'ID del formulario relacionado (para declaraciones)',
    
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
    INDEX idx_created_at (created_at),
    INDEX idx_related_form (related_form_id),
    INDEX idx_pdf_generated (pdf_generated_at),
    
    -- FOREIGN KEYS
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (related_form_id) REFERENCES forms_sagrilaft(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: form_attachments
-- ============================================================================
CREATE TABLE IF NOT EXISTS form_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
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
-- DATOS INICIALES
-- ============================================================================

-- Usuario administrador por defecto
INSERT INTO users (name, email, password, role) VALUES 
('Administrador', 'admin@sagrilaft.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE name=name;

-- Usuario revisor por defecto
INSERT INTO users (name, email, password, role) VALUES 
('Revisor', 'revisor@sagrilaft.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'revisor')
ON DUPLICATE KEY UPDATE name=name;

-- ============================================================================
-- VERIFICACIÓN
-- ============================================================================
SELECT 'Schema completo creado exitosamente' AS mensaje;
SELECT 
    TABLE_NAME as 'Tabla',
    TABLE_ROWS as 'Filas',
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as 'Tamaño (MB)'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'sagrilaft'
ORDER BY TABLE_NAME;
-- ============================================================================
-- TABLA: Actividades Económicas (Códigos CIIU) - COMPLETO
-- ============================================================================

USE sagrilaft;

-- Limpiar tabla si existe (comentado para no borrar en cada ejecución)
-- TRUNCATE TABLE actividades_economicas;

-- Insertar TODOS los códigos CIIU
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
('322', 'Acuicultura de agua dulce', 'Agricultura'),
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
('990', 'Actividades de apoyo para otras actividades de explotación de minas y canteras', 'Minería');


-- INDUSTRIA MANUFACTURERA
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('1011', 'Procesamiento y conservación de carne y productos cárnicos', 'Industria'),
('1012', 'Procesamiento y conservación de pescados, crustáceos y moluscos', 'Industria'),
('1020', 'Procesamiento y conservación de frutas, legumbres, hortalizas y tubérculos', 'Industria'),
('1030', 'Elaboración de aceites y grasas de origen vegetal y animal', 'Industria'),
('1040', 'Elaboración de productos lácteos', 'Industria'),
('1051', 'Elaboración de productos de molinería', 'Industria'),
('1052', 'Elaboración de almidones y productos derivados del almidón', 'Industria'),
('1061', 'Trilla de café', 'Industria'),
('1062', 'Descafeinado, tostión y molienda del café', 'Industria'),
('1063', 'Otros derivados del café', 'Industria'),
('1071', 'Elaboración y refinación de azúcar', 'Industria'),
('1072', 'Elaboración de panela', 'Industria'),
('1081', 'Elaboración de productos de panadería', 'Industria'),
('1082', 'Elaboración de cacao, chocolate y productos de confitería', 'Industria'),
('1083', 'Elaboración de macarrones, fideos, alcuzcuz y productos farináceos similares', 'Industria'),
('1084', 'Elaboración de comidas y platos preparados', 'Industria'),
('1089', 'Elaboración de otros productos alimenticios n.c.p.', 'Industria'),
('1090', 'Elaboración de alimentos preparados para animales', 'Industria'),
('1101', 'Destilación, rectificación y mezcla de bebidas alcohólicas', 'Industria'),
('1102', 'Elaboración de bebidas fermentadas no destiladas', 'Industria'),
('1103', 'Producción de malta, elaboración de cervezas y otras bebidas malteadas', 'Industria'),
('1104', 'Elaboración de bebidas no alcohólicas, producción de aguas minerales y de otras aguas embotelladas', 'Industria'),
('1200', 'Elaboración de productos de tabaco', 'Industria'),
('1311', 'Preparación e hilatura de fibras textiles', 'Industria'),
('1312', 'Tejeduría de productos textiles', 'Industria'),
('1313', 'Acabado de productos textiles', 'Industria'),
('1391', 'Fabricación de tejidos de punto y ganchillo', 'Industria'),
('1392', 'Confección de artículos con materiales textiles, excepto prendas de vestir', 'Industria'),
('1393', 'Fabricación de tapetes y alfombras para pisos', 'Industria'),
('1394', 'Fabricación de cuerdas, cordeles, cables, bramantes y redes', 'Industria'),
('1399', 'Fabricación de otros artículos textiles n.c.p.', 'Industria'),
('1410', 'Confección de prendas de vestir, excepto prendas de piel', 'Industria'),
('1420', 'Fabricación de artículos de piel', 'Industria'),
('1430', 'Fabricación de artículos de punto y ganchillo', 'Industria'),
('1511', 'Curtido y recurtido de cueros recurtido y teñido de pieles', 'Industria'),
('1512', 'Fabricación de artículos de viaje bolsos de mano y artículos similares elaborados en cuero y fabricación de artículos de talabartería y guarnicionería', 'Industria'),
('1513', 'Fabricación de artículos de viaje bolsos de mano y artículos similares artículos de talabartería y guarnicionería elaborados en otros materiales', 'Industria'),
('1521', 'Fabricación de calzado de cuero y piel, con cualquier tipo de suela', 'Industria'),
('1522', 'Fabricación de otros tipos de calzado, excepto calzado de cuero y piel', 'Industria'),
('1523', 'Fabricación de partes del calzado', 'Industria'),
('1610', 'Aserrado, acepillado e impregnación de la madera', 'Industria'),
('1620', 'Fabricación de hojas de madera para enchapado fabricación de tableros contrachapados tableros laminados tableros de partículas y otros tableros y paneles', 'Industria'),
('1630', 'Fabricación de partes y piezas de madera de carpintería y ebanistería para la construcción', 'Industria'),
('1640', 'Fabricación de recipientes de madera', 'Industria'),
('1690', 'Fabricación de otros productos de madera fabricación de artículos de corcho cestería y espartería', 'Industria'),
('1701', 'Fabricación de pulpas (pastas) celulósicas papel y cartón', 'Industria'),
('1702', 'Fabricación de papel y cartón ondulado (corrugado) fabricación de envases empaques y de embalajes de papel y cartón', 'Industria'),
('1709', 'Fabricación de otros artículos de papel y cartón', 'Industria'),
('1811', 'Actividades de impresión', 'Industria'),
('1812', 'Actividades de servicios relacionados con la impresión', 'Industria'),
('1820', 'Producción de copias a partir de grabaciones originales', 'Industria'),
('1910', 'Fabricación de productos de hornos de coque', 'Industria'),
('1921', 'Fabricación de productos de la refinación del petróleo', 'Industria'),
('1922', 'Actividad de mezcla de combustibles', 'Industria'),
('2011', 'Fabricación de sustancias y productos químicos básicos', 'Industria'),
('2012', 'Fabricación de abonos y compuestos inorgánicos nitrogenados', 'Industria'),
('2013', 'Fabricación de plásticos en formas primarias', 'Industria'),
('2014', 'Fabricación de caucho sintético en formas primarias', 'Industria'),
('2021', 'Fabricación de plaguicidas y otros productos químicos de uso agropecuario', 'Industria'),
('2022', 'Fabricación de pinturas barnices y revestimientos similares tintas para impresión y masillas', 'Industria'),
('2023', 'Fabricación de jabones y detergentes preparados para limpiar y pulir perfumes y preparados de tocador', 'Industria'),
('2029', 'Fabricación de otros productos químicos n.c.p.', 'Industria'),
('2030', 'Fabricación de fibras sintéticas y artificiales', 'Industria'),
('2100', 'Fabricación de productos farmacéuticos, sustancias químicas medicinales y productos botánicos de uso farmacéutico', 'Industria'),
('2211', 'Fabricación de llantas y neumáticos de caucho', 'Industria'),
('2212', 'Reencauche de llantas usadas', 'Industria'),
('2219', 'Fabricación de formas básicas de caucho y otros productos de caucho n.c.p.', 'Industria'),
('2221', 'Fabricación de formas básicas de plástico', 'Industria'),
('2229', 'Fabricación de artículos de plástico n.c.p.', 'Industria'),
('2310', 'Fabricación de vidrio y productos de vidrio', 'Industria'),
('2391', 'Fabricación de productos refractarios', 'Industria'),
('2392', 'Fabricación de materiales de arcilla para la construcción', 'Industria'),
('2393', 'Fabricación de otros productos de cerámica y porcelana', 'Industria'),
('2394', 'Fabricación de cemento, cal y yeso', 'Industria'),
('2395', 'Fabricación de artículos de hormigón, cemento y yeso', 'Industria'),
('2396', 'Corte, tallado y acabado de la piedra', 'Industria'),
('2399', 'Fabricación de otros productos minerales no metálicos n.c.p.', 'Industria'),
('2410', 'Industrias básicas de hierro y de acero', 'Industria'),
('2421', 'Industrias básicas de metales preciosos', 'Industria'),
('2429', 'Industrias básicas de otros metales no ferrosos', 'Industria'),
('2431', 'Fundición de hierro y de acero', 'Industria'),
('2432', 'Fundición de metales no ferrosos', 'Industria'),
('2511', 'Fabricación de productos metálicos para uso estructural', 'Industria'),
('2512', 'Fabricación de tanques, depósitos y recipientes de metal, excepto los utilizados para el envase o transporte de mercancías', 'Industria'),
('2513', 'Fabricación de generadores de vapor, excepto calderas de agua caliente para calefacción central', 'Industria'),
('2520', 'Fabricación de armas y municiones', 'Industria'),
('2591', 'Forja prensado estampado y laminado de metal pulvimetalurgia', 'Industria'),
('2592', 'Tratamiento y revestimiento de metales mecanizado', 'Industria'),
('2593', 'Fabricación de artículos de cuchillería herramientas de mano y artículos de ferretería', 'Industria'),
('2599', 'Fabricación de otros productos elaborados de metal n.c.p.', 'Industria'),
('2610', 'Fabricación de componentes y tableros electrónicos', 'Industria'),
('2620', 'Fabricación de computadoras y de equipo periférico', 'Industria'),
('2630', 'Fabricación de equipos de comunicación', 'Industria'),
('2640', 'Fabricación de aparatos electrónicos de consumo', 'Industria'),
('2651', 'Fabricación de equipo de medición, prueba, navegación y control', 'Industria'),
('2652', 'Fabricación de relojes', 'Industria'),
('2660', 'Fabricación de equipo de irradiación y equipo electrónico de uso médico y terapéutico', 'Industria'),
('2670', 'Fabricación de instrumentos ópticos y equipo fotográfico', 'Industria'),
('2680', 'Fabricación de medios magnéticos y ópticos para almacenamiento de datos', 'Industria'),
('2711', 'Fabricación de motores, generadores y transformadores eléctricos', 'Industria'),
('2712', 'Fabricación de aparatos de distribución y control de la energía eléctrica', 'Industria'),
('2720', 'Fabricación de pilas, baterías y acumuladores eléctricos', 'Industria'),
('2731', 'Fabricación de hilos y cables eléctricos y de fibra óptica', 'Industria'),
('2732', 'Fabricación de dispositivos de cableado', 'Industria'),
('2740', 'Fabricación de equipos eléctricos de iluminación', 'Industria'),
('2750', 'Fabricación de aparatos de uso doméstico', 'Industria'),
('2790', 'Fabricación de otros tipos de equipo eléctrico n.c.p.', 'Industria'),
('2811', 'Fabricación de motores, turbinas, y partes para motores de combustión interna', 'Industria'),
('2812', 'Fabricación de equipos de potencia hidráulica y neumática', 'Industria'),
('2813', 'Fabricación de otras bombas, compresores, grifos y válvulas', 'Industria'),
('2814', 'Fabricación de cojinetes, engranajes, trenes de engranajes y piezas de transmisión', 'Industria'),
('2815', 'Fabricación de hornos, hogares y quemadores industriales', 'Industria'),
('2816', 'Fabricación de equipo de elevación y manipulación', 'Industria'),
('2817', 'Fabricación de maquinaria y equipo de oficina (excepto computadoras y equipo periférico)', 'Industria'),
('2818', 'Fabricación de herramientas manuales con motor', 'Industria'),
('2819', 'Fabricación de otros tipos de maquinaria y equipo de uso general n.c.p.', 'Industria'),
('2821', 'Fabricación de maquinaria agropecuaria y forestal', 'Industria'),
('2822', 'Fabricación de máquinas formadoras de metal y de máquinas herramienta', 'Industria'),
('2823', 'Fabricación de maquinaria para la metalurgia', 'Industria'),
('2824', 'Fabricación de maquinaria para explotación de minas y canteras y para obras de construcción', 'Industria'),
('2825', 'Fabricación de maquinaria para la elaboración de alimentos, bebidas y tabaco', 'Industria'),
('2826', 'Fabricación de maquinaria para la elaboración de productos textiles, prendas de vestir y cueros', 'Industria'),
('2829', 'Fabricación de otros tipos de maquinaria y equipo de uso especial n.c.p.', 'Industria'),
('2910', 'Fabricación de vehículos automotores y sus motores', 'Industria'),
('2920', 'Fabricación de carrocerías para vehículos automotores fabricación de remolques y semirremolques', 'Industria'),
('2930', 'Fabricación de partes, piezas (autopartes) y accesorios (lujos) para vehículos automotores', 'Industria'),
('3011', 'Construcción de barcos y de estructuras flotantes', 'Industria'),
('3012', 'Construcción de embarcaciones de recreo y deporte', 'Industria'),
('3020', 'Fabricación de locomotoras y de material rodante para ferrocarriles', 'Industria'),
('3030', 'Fabricación de aeronaves, naves espaciales y de maquinaria conexa', 'Industria'),
('3040', 'Fabricación de vehículos militares de combate', 'Industria'),
('3091', 'Fabricación de motocicletas', 'Industria'),
('3092', 'Fabricación de bicicletas y de sillas de ruedas para personas con discapacidad', 'Industria'),
('3099', 'Fabricación de otros tipos de equipo de transporte n.c.p.', 'Industria'),
('3110', 'Fabricación de muebles', 'Industria'),
('3120', 'Fabricación de colchones y somieres', 'Industria'),
('3210', 'Fabricación de joyas, bisutería y artículos conexos', 'Industria'),
('3220', 'Fabricación de instrumentos musicales', 'Industria'),
('3230', 'Fabricación de artículos y equipo para la práctica del deporte', 'Industria'),
('3240', 'Fabricación de juegos, juguetes y rompecabezas', 'Industria'),
('3250', 'Fabricación de instrumentos, aparatos y materiales médicos y odontológicos (incluido mobiliario)', 'Industria'),
('3290', 'Otras industrias manufactureras n.c.p.', 'Industria'),
('3311', 'Mantenimiento y reparación especializado de productos elaborados en metal', 'Industria'),
('3312', 'Mantenimiento y reparación especializado de maquinaria y equipo', 'Industria'),
('3313', 'Mantenimiento y reparación especializado de equipo electrónico y óptico', 'Industria'),
('3314', 'Mantenimiento y reparación especializado de equipo eléctrico', 'Industria'),
('3315', 'Mantenimiento y reparación especializado de equipo de transporte, excepto los vehículos automotores, motocicletas y bicicletas', 'Industria'),
('3319', 'Mantenimiento y reparación de otros tipos de equipos y sus componentes n.c.p.', 'Industria'),
('3320', 'Instalación especializada de maquinaria y equipo industrial', 'Industria');


-- SUMINISTRO DE ELECTRICIDAD, GAS, VAPOR Y AIRE ACONDICIONADO
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('3511', 'Generación de energía eléctrica', 'Servicios'),
('3512', 'Transmisión de energía eléctrica', 'Servicios'),
('3513', 'Distribución de energía eléctrica', 'Servicios'),
('3514', 'Comercialización de energía eléctrica', 'Servicios'),
('3520', 'Producción de gas distribución de combustibles gaseosos por tuberías', 'Servicios'),
('3530', 'Suministro de vapor y aire acondicionado', 'Servicios'),
('3600', 'Captación, tratamiento y distribución de agua', 'Servicios'),
('3700', 'Evacuación y tratamiento de aguas residuales', 'Servicios'),
('3811', 'Recolección de desechos no peligrosos', 'Servicios'),
('3812', 'Recolección de desechos peligrosos', 'Servicios'),
('3821', 'Tratamiento y disposición de desechos no peligrosos', 'Servicios'),
('3822', 'Tratamiento y disposición de desechos peligrosos', 'Servicios'),
('3830', 'Recuperación de materiales', 'Servicios'),
('3900', 'Actividades de saneamiento ambiental y otros servicios de gestión de desechos', 'Servicios');

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
('4390', 'Otras actividades especializadas para la construcción de edificios y obras de ingeniería civil', 'Construcción');

-- COMERCIO AL POR MAYOR Y AL POR MENOR
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('4511', 'Comercio de vehículos automotores nuevos', 'Comercio'),
('4512', 'Comercio de vehículos automotores usados', 'Comercio'),
('4520', 'Mantenimiento y reparación de vehículos automotores', 'Comercio'),
('4530', 'Comercio de partes, piezas (autopartes) y accesorios (lujos) para vehículos automotores', 'Comercio'),
('4541', 'Comercio de motocicletas y de sus partes, piezas y accesorios', 'Comercio'),
('4542', 'Mantenimiento y reparación de motocicletas y de sus partes y piezas', 'Comercio'),
('4610', 'Comercio al por mayor a cambio de una retribución o por contrata', 'Comercio'),
('4620', 'Comercio al por mayor de materias primas agropecuarias animales vivos', 'Comercio'),
('4631', 'Comercio al por mayor de productos alimenticios', 'Comercio'),
('4632', 'Comercio al por mayor de bebidas y tabaco', 'Comercio'),
('4641', 'Comercio al por mayor de productos textiles, productos confeccionados para uso doméstico', 'Comercio'),
('4642', 'Comercio al por mayor de prendas de vestir', 'Comercio'),
('4643', 'Comercio al por mayor de calzado', 'Comercio'),
('4644', 'Comercio al por mayor de aparatos y equipo de uso doméstico', 'Comercio'),
('4645', 'Comercio al por mayor de productos farmacéuticos, medicinales, cosméticos y de tocador', 'Comercio'),
('4649', 'Comercio al por mayor de otros utensilios domésticos n.c.p.', 'Comercio'),
('4651', 'Comercio al por mayor de computadores, equipo periférico y programas de informática', 'Comercio'),
('4652', 'Comercio al por mayor de equipo, partes y piezas electrónicos y de telecomunicaciones', 'Comercio'),
('4653', 'Comercio al por mayor de maquinaria y equipo agropecuarios', 'Comercio'),
('4659', 'Comercio al por mayor de otros tipos de maquinaria y equipo n.c.p.', 'Comercio'),
('4661', 'Comercio al por mayor de combustibles sólidos, líquidos, gaseosos y productos conexos', 'Comercio'),
('4662', 'Comercio al por mayor de metales y productos metalíferos', 'Comercio'),
('4663', 'Comercio al por mayor de materiales de construcción, artículos de ferretería, pinturas, productos de vidrio, equipo y materiales de fontanería y calefacción', 'Comercio'),
('4664', 'Comercio al por mayor de productos químicos básicos, cauchos y plásticos en formas primarias y productos químicos de uso agropecuario', 'Comercio'),
('4665', 'Comercio al por mayor de desperdicios, desechos y chatarra', 'Comercio'),
('4669', 'Comercio al por mayor de otros productos n.c.p.', 'Comercio'),
('4690', 'Comercio al por mayor no especializado', 'Comercio'),
('4711', 'Comercio al por menor en establecimientos no especializados con surtido compuesto principalmente por alimentos, bebidas (alcoholicas y no alcoholicas) o tabaco', 'Comercio'),
('4719', 'Comercio al por menor en establecimientos no especializados, con surtido compuesto principalmente por productos diferentes de alimentos (viveres en general), bebidas (alcoholicas y no alcoholicas) y tabaco', 'Comercio'),
('4721', 'Comercio al por menor de productos agrícolas para el consumo en establecimientos especializados', 'Comercio'),
('4722', 'Comercio al por menor de leche, productos lácteos y huevos, en establecimientos especializados', 'Comercio'),
('4723', 'Comercio al por menor de carnes (incluye aves de corral), productos cárnicos, pescados y productos de mar, en establecimientos especializados', 'Comercio'),
('4724', 'Comercio al por menor de bebidas y productos del tabaco, en establecimientos especializados', 'Comercio'),
('4729', 'Comercio al por menor de otros productos alimenticios n.c.p., en establecimientos especializados', 'Comercio'),
('4731', 'Comercio al por menor de combustible para automotores', 'Comercio'),
('4732', 'Comercio al por menor de lubricantes (aceites, grasas), aditivos y productos de limpieza para vehículos automotores', 'Comercio'),
('4741', 'Comercio al por menor de computadores, equipos periféricos, programas de informática y equipos de telecomunicaciones en establecimientos especializados', 'Comercio'),
('4742', 'Comercio al por menor de equipos y aparatos de sonido y de video, en establecimientos especializados', 'Comercio'),
('4751', 'Comercio al por menor de productos textiles en establecimientos especializados', 'Comercio'),
('4752', 'Comercio al por menor de artículos de ferretería, pinturas y productos de vidrio en establecimientos especializados', 'Comercio'),
('4753', 'Comercio al por menor de tapices, alfombras y recubrimientos para paredes y pisos en establecimientos especializados', 'Comercio'),
('4754', 'Comercio al por menor de electrodomesticos y gasodomesticos de uso domestico, muebles y equipos de iluminacion en establecimientos especializados', 'Comercio'),
('4755', 'Comercio al por menor de articulos y utensilios de uso domestico en establecimientos especializados', 'Comercio'),
('4759', 'Comercio al por menor de otros artículos domésticos en establecimientos especializados', 'Comercio'),
('4761', 'Comercio al por menor de libros, periódicos, materiales y artículos de papelería y escritorio, en establecimientos especializados', 'Comercio'),
('4762', 'Comercio al por menor de artículos deportivos, en establecimientos especializados', 'Comercio'),
('4769', 'Comercio al por menor de otros artículos culturales y de entretenimiento n.c.p. en establecimientos especializados', 'Comercio'),
('4771', 'Comercio al por menor de prendas de vestir y sus accesorios (incluye artículos de piel) en establecimientos especializados', 'Comercio'),
('4772', 'Comercio al por menor de todo tipo de calzado y artículos de cuero y sucedáneos del cuero en establecimientos especializados', 'Comercio'),
('4773', 'Comercio al por menor de productos farmacéuticos y medicinales, cosméticos y artículos de tocador en establecimientos especializados', 'Comercio'),
('4774', 'Comercio al por menor de otros productos nuevos en establecimientos especializados', 'Comercio'),
('4775', 'Comercio al por menor de artículos de segunda mano', 'Comercio'),
('4781', 'Comercio al por menor de alimentos, bebidas y tabaco, en puestos de venta móviles', 'Comercio'),
('4782', 'Comercio al por menor de productos textiles, prendas de vestir y calzado, en puestos de venta móviles', 'Comercio'),
('4789', 'Comercio al por menor de otros productos en puestos de venta móviles', 'Comercio'),
('4791', 'Comercio al por menor realizado a través de Internet', 'Comercio'),
('4792', 'Comercio al por menor realizado a través de casas de venta o por correo', 'Comercio'),
('4799', 'Otros tipos de comercio al por menor no realizado en establecimientos, puestos de venta o mercados', 'Comercio');


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
('5221', 'Actividades de estaciones, vías y servicios complementarios para el transporte terrestre', 'Transporte'),
('5222', 'Actividades de puertos y servicios complementarios para el transporte acuático', 'Transporte'),
('5223', 'Actividades de aeropuertos, servicios de navegación aérea y demás actividades conexas al transporte aéreo', 'Transporte'),
('5224', 'Manipulación de carga', 'Transporte'),
('5229', 'Otras actividades complementarias al transporte', 'Transporte'),
('5310', 'Actividades postales nacionales', 'Transporte'),
('5320', 'Actividades de mensajería', 'Transporte');

-- ALOJAMIENTO Y SERVICIOS DE COMIDA
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('5511', 'Alojamiento en hoteles', 'Servicios'),
('5512', 'Alojamiento en apartahoteles', 'Servicios'),
('5513', 'Alojamiento en centros vacacionales', 'Servicios'),
('5514', 'Alojamiento rural', 'Servicios'),
('5519', 'Otros tipos de alojamientos para visitantes', 'Servicios'),
('5520', 'Actividades de zonas de camping y parques para vehículos recreacionales', 'Servicios'),
('5530', 'Servicio de estancia por horas', 'Servicios'),
('5590', 'Otros tipos de alojamiento n.c.p.', 'Servicios'),
('5611', 'Expendio a la mesa de comidas preparadas', 'Servicios'),
('5612', 'Expendio por autoservicio de comidas preparadas', 'Servicios'),
('5613', 'Expendio de comidas preparadas en cafeterías', 'Servicios'),
('5619', 'Otros tipos de expendio de comidas preparadas n.c.p.', 'Servicios'),
('5621', 'Catering para eventos', 'Servicios'),
('5629', 'Actividades de otros servicios de comidas', 'Servicios'),
('5630', 'Expendio de bebidas alcohólicas para el consumo dentro del establecimiento', 'Servicios');

-- INFORMACIÓN Y COMUNICACIONES
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('5811', 'Edición de libros', 'Servicios'),
('5812', 'Edición de directorios y listas de correo', 'Servicios'),
('5813', 'Edición de periódicos, revistas y otras publicaciones periódicas', 'Servicios'),
('5819', 'Otros trabajos de edición', 'Servicios'),
('5820', 'Edición de programas de informática (software)', 'Servicios'),
('5911', 'Actividades de producción de películas cinematográficas, videos, programas, anuncios y comerciales de televisión', 'Servicios'),
('5912', 'Actividades de posproducción de películas cinematográficas, videos, programas, anuncios y comerciales de televisión', 'Servicios'),
('5913', 'Actividades de distribución de películas cinematográficas, videos, programas, anuncios y comerciales de televisión', 'Servicios'),
('5914', 'Actividades de exhibición de películas cinematográficas y videos', 'Servicios'),
('5920', 'Actividades de grabación de sonido y edición de música', 'Servicios'),
('6010', 'Actividades de programación y transmisión en el servicio de radiodifusión sonora', 'Servicios'),
('6020', 'Actividades de programación y transmisión de televisión', 'Servicios'),
('6110', 'Actividades de telecomunicaciones alámbricas', 'Servicios'),
('6120', 'Actividades de telecomunicaciones inalámbricas', 'Servicios'),
('6130', 'Actividades de telecomunicación satelital', 'Servicios'),
('6190', 'Otras actividades de telecomunicaciones', 'Servicios'),
('6201', 'Actividades de desarrollo de sistemas informáticos (planificación, análisis, diseño, programación, pruebas)', 'Servicios'),
('6202', 'Actividades de consultoría informática y actividades de administración de instalaciones informáticas', 'Servicios'),
('6209', 'Otras actividades de tecnologías de información y actividades de servicios informáticos', 'Servicios'),
('6311', 'Procesamiento de datos, alojamiento (hosting) y actividades relacionadas', 'Servicios'),
('6312', 'Portales web', 'Servicios'),
('6391', 'Actividades de agencias de noticias', 'Servicios'),
('6399', 'Otras actividades de servicio de información n.c.p.', 'Servicios');

-- ACTIVIDADES FINANCIERAS Y DE SEGUROS
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('6411', 'Banco Central', 'Servicios'),
('6412', 'Bancos comerciales', 'Servicios'),
('6421', 'Actividades de las corporaciones financieras', 'Servicios'),
('6422', 'Actividades de las compañías de financiamiento', 'Servicios'),
('6423', 'Banca de segundo piso', 'Servicios'),
('6424', 'Actividades de las cooperativas financieras', 'Servicios'),
('6431', 'Fideicomisos, fondos y entidades financieras similares', 'Servicios'),
('6432', 'Fondos de cesantías', 'Servicios'),
('6491', 'Leasing financiero (arrendamiento financiero)', 'Servicios'),
('6492', 'Actividades financieras de fondos de empleados y otras formas asociativas del sector solidario', 'Servicios'),
('6493', 'Actividades de compra de cartera o factoring', 'Servicios'),
('6494', 'Otras actividades de distribución de fondos', 'Servicios'),
('6495', 'Instituciones especiales oficiales', 'Servicios'),
('6496', 'Capitalizacion', 'Servicios'),
('6499', 'Otras actividades de servicio financiero, excepto las de seguros y pensiones n.c.p.', 'Servicios'),
('6511', 'Seguros generales', 'Servicios'),
('6512', 'Seguros de vida', 'Servicios'),
('6513', 'Reaseguros', 'Servicios'),
('6515', 'Seguros de salud', 'Servicios'),
('6521', 'Servicios de seguros sociales de salud', 'Servicios'),
('6522', 'Servicios de seguros sociales en riesgos laborales', 'Servicios'),
('6523', 'Servicios de seguros sociales en riesgos familia', 'Servicios'),
('6531', 'Régimen de prima media con prestación definida (RPM)', 'Servicios'),
('6532', 'Regimen de ahorro individual con solidaridad (RAIS)', 'Servicios'),
('6611', 'Administración de mercados financieros', 'Servicios'),
('6612', 'Corretaje de valores y de contratos de productos básicos', 'Servicios'),
('6613', 'Otras actividades relacionadas con el mercado de valores', 'Servicios'),
('6614', 'Actividades de las sociedades de intermediacion cambiaria y de servicios financieros especiales', 'Servicios'),
('6615', 'Actividades de los profesionales de compra y venta de divisas', 'Servicios'),
('6619', 'Otras actividades auxiliares de las actividades de servicios financieros n.c.p.', 'Servicios'),
('6621', 'Actividades de agentes y corredores de seguros', 'Servicios'),
('6629', 'Evaluación de riesgos y daños, y otras actividades de servicios auxiliares', 'Servicios'),
('6630', 'Actividades de administración de fondos', 'Servicios');

-- ACTIVIDADES INMOBILIARIAS
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('6810', 'Actividades inmobiliarias realizadas con bienes propios o arrendados', 'Servicios'),
('6820', 'Actividades inmobiliarias realizadas a cambio de una retribución o por contrata', 'Servicios');

-- ACTIVIDADES PROFESIONALES, CIENTÍFICAS Y TÉCNICAS
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('6910', 'Actividades jurídicas', 'Servicios'),
('6920', 'Actividades de contabilidad, teneduría de libros, auditoría financiera y asesoría tributaria', 'Servicios'),
('7010', 'Actividades de administración empresarial', 'Servicios'),
('7020', 'Actividades de consultaría de gestión', 'Servicios'),
('7111', 'Actividades de arquitectura', 'Servicios'),
('7112', 'Actividades de ingeniería y otras actividades conexas de consultoría técnica', 'Servicios'),
('7120', 'Ensayos y análisis técnicos', 'Servicios'),
('7210', 'Investigaciones y desarrollo experimental en el campo de las ciencias naturales y la ingeniería', 'Servicios'),
('7220', 'Investigaciones y desarrollo experimental en el campo de las ciencias sociales y las humanidades', 'Servicios'),
('7310', 'Publicidad', 'Servicios'),
('7320', 'Estudios de mercado y realización de encuestas de opinión pública', 'Servicios'),
('7410', 'Actividades especializadas de diseño', 'Servicios'),
('7420', 'Actividades de fotografía', 'Servicios'),
('7490', 'Otras actividades profesionales, científicas y técnicas n.c.p.', 'Servicios'),
('7500', 'Actividades veterinarias', 'Servicios');

-- ACTIVIDADES DE SERVICIOS ADMINISTRATIVOS Y DE APOYO
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('7710', 'Alquiler y arrendamiento de vehículos automotores', 'Servicios'),
('7721', 'Alquiler y arrendamiento de equipo recreativo y deportivo', 'Servicios'),
('7722', 'Alquiler de videos y discos', 'Servicios'),
('7729', 'Alquiler y arrendamiento de otros efectos personales y enseres domésticos n.c.p.', 'Servicios'),
('7730', 'Alquiler y arrendamiento de otros tipos de maquinaria, equipo y bienes tangibles n.c.p.', 'Servicios'),
('7740', 'Arrendamiento de propiedad intelectual y productos similares, excepto obras protegidas por derechos de autor', 'Servicios'),
('7810', 'Actividades de agencias de gestion y colocacion de empleo', 'Servicios'),
('7820', 'Actividades de empresas de servicios temporales', 'Servicios'),
('7830', 'Otras actividades de provision de talento humano', 'Servicios'),
('7911', 'Actividades de las agencias de viaje', 'Servicios'),
('7912', 'Actividades de operadores turísticos', 'Servicios'),
('7990', 'Otros servicios de reserva y actividades relacionadas', 'Servicios'),
('8010', 'Actividades de seguridad privada', 'Servicios'),
('8020', 'Actividades de servicios de sistemas de seguridad', 'Servicios'),
('8030', 'Actividades de detectives e investigadores privados', 'Servicios'),
('8110', 'Actividades combinadas de apoyo a instalaciones', 'Servicios'),
('8121', 'Limpieza general interior de edificios', 'Servicios'),
('8129', 'Otras actividades de limpieza de edificios e instalaciones industriales', 'Servicios'),
('8130', 'Actividades de paisajismo y servicios de mantenimiento conexos', 'Servicios'),
('8211', 'Actividades combinadas de servicios administrativos de oficina', 'Servicios'),
('8219', 'Fotocopiado, preparación de documentos y otras actividades especializadas de apoyo a oficina', 'Servicios'),
('8220', 'Actividades de centros de llamadas (Call center)', 'Servicios'),
('8230', 'Organización de convenciones y eventos comerciales', 'Servicios'),
('8291', 'Actividades de agencias de cobranza y oficinas de calificación crediticia', 'Servicios'),
('8292', 'Actividades de envase y empaque', 'Servicios'),
('8299', 'Otras actividades de servicio de apoyo a las empresas n.c.p.', 'Servicios');

-- ADMINISTRACIÓN PÚBLICA Y DEFENSA
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('8411', 'Actividades legislativas de la administración pública', 'Servicios'),
('8412', 'Actividades ejecutivas de la administración pública', 'Servicios'),
('8413', 'Regulación de las actividades de organismos que prestan servicios de salud, educativos, culturales y otros servicios sociales, excepto servicios de seguridad social', 'Servicios'),
('8414', 'Actividades reguladoras y facilitadoras de la actividad económica', 'Servicios'),
('8415', 'Actividades de los organos de control y otras instituciones', 'Servicios'),
('8421', 'Relaciones exteriores', 'Servicios'),
('8422', 'Actividades de defensa', 'Servicios'),
('8423', 'Orden público y actividades de seguridad', 'Servicios'),
('8424', 'Administración de justicia', 'Servicios'),
('8430', 'Actividades de planes de seguridad social de afiliación obligatoria', 'Servicios');

-- EDUCACIÓN
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('8511', 'Educación de la primera infancia', 'Servicios'),
('8512', 'Educación preescolar', 'Servicios'),
('8513', 'Educación básica primaria', 'Servicios'),
('8521', 'Educación básica secundaria', 'Servicios'),
('8522', 'Educación media académica', 'Servicios'),
('8523', 'Educacion media tecnica', 'Servicios'),
('8530', 'Establecimientos que combinan diferentes niveles de educación', 'Servicios'),
('8541', 'Educación técnica profesional', 'Servicios'),
('8542', 'Educación tecnológica', 'Servicios'),
('8543', 'Educación de instituciones universitarias o de escuelas tecnológicas', 'Servicios'),
('8544', 'Educación de universidades', 'Servicios'),
('8551', 'Formacion para el trabajo', 'Servicios'),
('8552', 'Enseñanza deportiva y recreativa', 'Servicios'),
('8553', 'Enseñanza cultural', 'Servicios'),
('8559', 'Otros tipos de educación n.c.p.', 'Servicios'),
('8560', 'Actividades de apoyo a la educación', 'Servicios');

-- ACTIVIDADES DE ATENCIÓN DE LA SALUD HUMANA Y DE ASISTENCIA SOCIAL
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('8610', 'Actividades de hospitales y clínicas, con internación', 'Servicios'),
('8621', 'Actividades de la práctica médica, sin internación', 'Servicios'),
('8622', 'Actividades de la práctica odontológica', 'Servicios'),
('8691', 'Actividades de apoyo diagnóstico', 'Servicios'),
('8692', 'Actividades de apoyo terapéutico', 'Servicios'),
('8699', 'Otras actividades de atención de la salud humana', 'Servicios'),
('8710', 'Actividades de atención residencial medicalizada de tipo general', 'Servicios'),
('8720', 'Actividades de atención residencial, para el cuidado de pacientes con retardo mental, enfermedad mental y consumo de sustancias psicoactivas', 'Servicios'),
('8730', 'Actividades de atención en instituciones para el cuidado de personas mayores y/o discapacitadas', 'Servicios'),
('8790', 'Otras actividades de atención en instituciones con alojamiento', 'Servicios'),
('8810', 'Actividades de asistencia social sin alojamiento para personas mayores y discapacitadas', 'Servicios'),
('8891', 'Actividades de guarderias para ninos y ninas', 'Servicios'),
('8899', 'Otras actividades de asistencia social n.c.p.', 'Servicios');

-- ACTIVIDADES ARTÍSTICAS, DE ENTRETENIMIENTO Y RECREACIÓN
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('9001', 'Creación literaria', 'Servicios'),
('9002', 'Creación musical', 'Servicios'),
('9003', 'Creación teatral', 'Servicios'),
('9004', 'Creación audiovisual', 'Servicios'),
('9005', 'Artes plásticas y visuales', 'Servicios'),
('9006', 'Actividades teatrales', 'Servicios'),
('9007', 'Actividades de espectáculos musicales en vivo', 'Servicios'),
('9008', 'Otras actividades de espectaculos en vivo n.c.p.', 'Servicios'),
('9101', 'Actividades de bibliotecas y archivos', 'Servicios'),
('9102', 'Actividades y funcionamiento de museos, conservación de edificios y sitios históricos', 'Servicios'),
('9103', 'Actividades de jardines botánicos, zoológicos y reservas naturales', 'Servicios'),
('9200', 'Actividades de juegos de azar y apuestas', 'Servicios'),
('9311', 'Gestión de instalaciones deportivas', 'Servicios'),
('9312', 'Actividades de clubes deportivos', 'Servicios'),
('9319', 'Otras actividades deportivas', 'Servicios'),
('9321', 'Actividades de parques de atracciones y parques temáticos', 'Servicios'),
('9329', 'Otras actividades recreativas y de esparcimiento n.c.p.', 'Servicios');

-- OTRAS ACTIVIDADES DE SERVICIOS
INSERT INTO actividades_economicas (codigo, descripcion, sector) VALUES
('9411', 'Actividades de asociaciones empresariales y de empleadores', 'Servicios'),
('9412', 'Actividades de asociaciones profesionales', 'Servicios'),
('9420', 'Actividades de sindicatos de empleados', 'Servicios'),
('9491', 'Actividades de asociaciones religiosas', 'Servicios'),
('9492', 'Actividades de asociaciones políticas', 'Servicios'),
('9499', 'Actividades de otras asociaciones n.c.p.', 'Servicios'),
('9511', 'Mantenimiento y reparación de computadores y de equipo periférico', 'Servicios'),
('9512', 'Mantenimiento y reparación de equipos de comunicación', 'Servicios'),
('9521', 'Mantenimiento y reparación de aparatos electrónicos de consumo', 'Servicios'),
('9522', 'Mantenimiento y reparación de aparatos y equipos domésticos y de jardinería', 'Servicios'),
('9523', 'Reparación de calzado y artículos de cuero', 'Servicios'),
('9524', 'Reparación de muebles y accesorios para el hogar', 'Servicios'),
('9529', 'Mantenimiento y reparación de otros efectos personales y enseres domésticos', 'Servicios'),
('9601', 'Lavado y limpieza, incluso la limpieza en seco, de productos textiles y de piel', 'Servicios'),
('9602', 'Peluquería y otros tratamientos de belleza', 'Servicios'),
('9603', 'Pompas fúnebres y actividades relacionadas', 'Servicios'),
('9609', 'Otras actividades de servicios personales n.c.p.', 'Servicios'),
('9700', 'Actividades de los hogares individuales como empleadores de personal doméstico', 'Servicios'),
('9810', 'Actividades no diferenciadas de los hogares individuales como productores de bienes para uso propio', 'Servicios'),
('9820', 'Actividades no diferenciadas de los hogares individuales como productores de servicios para uso propio', 'Servicios'),
('9900', 'Actividades de organizaciones y entidades extraterritoriales', 'Servicios');

-- Mensaje final
SELECT 'Migración completada - Todos los códigos CIIU insertados' AS mensaje;
SELECT COUNT(*) AS total_codigos FROM actividades_economicas;
-- ============================================================================
-- MIGRACIÓN: Asesores Comerciales
-- ============================================================================
-- Tabla para gestionar asesores comerciales y sus jefes
-- Permite asignar asesor al formulario y enviar notificaciones
-- ============================================================================

USE sagrilaft;

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
    
    -- Información del jefe
    jefe_nombre VARCHAR(255),
    jefe_email VARCHAR(255),
    
    -- Control
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_empleado_cc (empleado_cc),
    INDEX idx_email (email),
    INDEX idx_activo (activo),
    INDEX idx_jefe_email (jefe_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INSERTAR ASESORES COMERCIALES
-- ============================================================================

INSERT IGNORE INTO asesores_comerciales (empleado_cc, nombre_completo, email, fecha_ingreso, sede, descripcion_sede, jefe_nombre, jefe_email, activo) VALUES
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
('1016009906', 'SANCHEZ MERCHAN WILLIAM EDILSON', 'william.sanchez@pollo-fiesta.com', '2020-11-09', 'U01', 'UND FUNCIONAL ASADERO - EMPRESARIAL', 'RODRIGUEZ ESDGAR GERMAN', 'gerenciacomercial1@pollo-fiesta.com', TRUE),
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
('11410967', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', '2004-03-03', 'U03', 'UND FUNCIONAL MODERNO (S3)', 'BENITO GUEVARA HERNAN MATEO', 'gerenciacomercial3@pollo-fiesta.com', TRUE);

-- ============================================================================
-- AGREGAR COLUMNA A forms
-- ============================================================================
ALTER TABLE forms
ADD COLUMN IF NOT EXISTS asesor_comercial_id INT NULL COMMENT 'ID del asesor comercial asignado',
ADD INDEX IF NOT EXISTS idx_asesor_comercial (asesor_comercial_id),
ADD CONSTRAINT fk_asesor_comercial FOREIGN KEY (asesor_comercial_id) REFERENCES asesores_comerciales(id) ON DELETE SET NULL;

-- ============================================================================
-- VERIFICACIÓN
-- ============================================================================
SELECT 'Migración completada - Asesores comerciales agregados' AS mensaje;
SELECT COUNT(*) as total_asesores FROM asesores_comerciales;
SELECT sede, COUNT(*) as cantidad FROM asesores_comerciales GROUP BY sede;
