-- ============================================================================
-- MIGRACIÓN: Crear tabla separada para firmas y campos del revisor
-- ============================================================================
-- Fecha: 2024-12-22
-- Descripción: Crear tabla form_signatures para evitar límite de tamaño de fila
-- ============================================================================

USE sagrilaft;

-- Crear tabla para firmas y campos del revisor
CREATE TABLE IF NOT EXISTS form_signatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    
    -- Firmas digitales
    user_signature_data LONGTEXT COMMENT 'Firma del usuario (base64)',
    official_signature_data LONGTEXT COMMENT 'Firma del oficial (base64)',
    
    -- Campos del revisor
    vinculacion ENUM('nueva', 'actualizacion') COMMENT 'Tipo de vinculación',
    fecha_vinculacion DATE COMMENT 'Fecha de vinculación',
    actualizacion VARCHAR(255) COMMENT 'Descripción de actualización',
    
    -- Consultas
    consulta_ofac ENUM('negativa', 'positiva') COMMENT 'Resultado consulta OFAC',
    consulta_listas_nacionales ENUM('negativa', 'positiva') COMMENT 'Resultado listas nacionales',
    consulta_onu ENUM('negativa', 'positiva') COMMENT 'Resultado consulta ONU',
    consulta_interpol ENUM('negativa', 'positiva') COMMENT 'Resultado consulta INTERPOL',
    
    -- Personal interno
    recibe VARCHAR(255) COMMENT 'Persona que recibe',
    verificado_por VARCHAR(255) COMMENT 'Persona que verifica',
    preparo VARCHAR(255) COMMENT 'Persona que preparó',
    reviso VARCHAR(255) COMMENT 'Persona que revisó',
    nombre_oficial VARCHAR(255) COMMENT 'Nombre del oficial de cumplimiento',
    director_cartera VARCHAR(255) COMMENT 'Director de cartera',
    gerencia_comercial VARCHAR(255) COMMENT 'Gerencia comercial',
    
    -- Metadatos de revisión
    reviewed_at DATETIME COMMENT 'Fecha de revisión',
    reviewed_by VARCHAR(255) COMMENT 'ID del revisor',
    reviewed_by_name VARCHAR(255) COMMENT 'Nombre del revisor',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_form_signature (form_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrar datos existentes si existen campos en la tabla forms
INSERT IGNORE INTO form_signatures (
    form_id, vinculacion, fecha_vinculacion, actualizacion,
    consulta_ofac, consulta_listas_nacionales, consulta_onu, consulta_interpol,
    recibe, verificado_por, preparo, reviso, nombre_oficial, director_cartera, gerencia_comercial,
    reviewed_at, reviewed_by, reviewed_by_name
)
SELECT 
    id, vinculacion, fecha_vinculacion, actualizacion,
    consulta_ofac, consulta_listas_nacionales, consulta_onu, consulta_interpol,
    recibe, verificado_por, preparo, reviso, nombre_oficial, director_cartera, gerencia_comercial,
    reviewed_at, reviewed_by, reviewed_by_name
FROM forms 
WHERE id IS NOT NULL;

-- Verificar la nueva tabla
SELECT COUNT(*) as total_signatures FROM form_signatures;