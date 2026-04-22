-- ============================================================================
-- MIGRACIÓN: Corregir tabla firmas_digitales
-- ============================================================================
-- Fecha: 2024-12-22
-- Descripción: Agregar campo firma_data y corregir nombres de campos
-- ============================================================================

USE sagrilaft;

-- Agregar columna firma_data si no existe
ALTER TABLE firmas_digitales 
ADD COLUMN IF NOT EXISTS firma_data LONGBLOB COMMENT 'Datos binarios de la firma (imagen)';

-- Cambiar nombre de columna is_active a activa si existe
ALTER TABLE firmas_digitales 
CHANGE COLUMN is_active activa BOOLEAN DEFAULT TRUE COMMENT 'Solo una firma activa por usuario';

-- Hacer firma_path opcional (puede ser NULL si se usa firma_data)
ALTER TABLE firmas_digitales 
MODIFY COLUMN firma_path VARCHAR(500) NULL;

-- Hacer firma_filename opcional
ALTER TABLE firmas_digitales 
MODIFY COLUMN firma_filename VARCHAR(255) NULL;

-- Actualizar índice
DROP INDEX IF EXISTS idx_is_active ON firmas_digitales;
CREATE INDEX IF NOT EXISTS idx_activa ON firmas_digitales (activa);

-- Verificar estructura
DESCRIBE firmas_digitales;