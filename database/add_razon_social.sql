-- ============================================================================
-- Agregar columna razon_social a la tabla forms
-- ============================================================================
-- La razón social es el nombre legal completo de la empresa
-- Diferente del nombre comercial (company_name)
-- ============================================================================

ALTER TABLE `forms` 
ADD COLUMN `razon_social` VARCHAR(500) NULL 
AFTER `company_name`
COMMENT 'Razón social completa de la empresa (nombre legal registrado)';

-- Actualizar registros existentes: copiar company_name a razon_social si está vacío
UPDATE `forms` 
SET `razon_social` = `company_name` 
WHERE `razon_social` IS NULL 
  AND `company_name` IS NOT NULL
  AND `person_type` = 'juridica';

-- Verificar que se agregó correctamente
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    CHARACTER_MAXIMUM_LENGTH, 
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'forms' 
  AND COLUMN_NAME = 'razon_social';
