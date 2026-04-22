-- ============================================================================
-- MIGRACIÓN: Agregar campos de firma digital faltantes
-- ============================================================================
-- Fecha: 2024-12-22
-- Descripción: Agregar campos de firma digital y revisión faltantes
-- ============================================================================

USE sagrilaft;

-- Agregar campos de firma digital si no existen
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS signature_data LONGTEXT COMMENT 'Datos de la firma del usuario (base64)';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS firma_oficial_data LONGTEXT COMMENT 'Datos de la firma del oficial (base64)';

-- Agregar campos de revisión si no existen
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS reviewed_at DATETIME COMMENT 'Fecha de revisión';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS reviewed_by VARCHAR(255) COMMENT 'ID del revisor';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS reviewed_by_name VARCHAR(255) COMMENT 'Nombre completo del revisor';

-- Verificar estructura actualizada
DESCRIBE forms;