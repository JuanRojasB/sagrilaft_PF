-- ============================================================================
-- Migración: Agregar columnas de firma digital a la tabla users
-- Fecha: 2026-04-13
-- Descripción: Agrega las columnas firma_digital y firma_mime_type para 
--              almacenar la firma digital de los revisores
-- ============================================================================

-- Agregar columna firma_digital (almacena la imagen en base64)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS firma_digital LONGTEXT NULL COMMENT 'Firma digital en base64';

-- Agregar columna firma_mime_type (almacena el tipo MIME de la imagen)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS firma_mime_type VARCHAR(50) NULL COMMENT 'Tipo MIME de la firma (image/png, image/jpeg)';

-- Verificar las columnas agregadas
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_COMMENT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'users' 
AND COLUMN_NAME IN ('firma_digital', 'firma_mime_type');
