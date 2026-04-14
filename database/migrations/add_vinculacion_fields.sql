-- ============================================================================
-- Migración: Agregar campos de vinculación a la tabla forms
-- Fecha: 2026-04-14
-- Descripción: Agrega los campos vinculacion, fecha_vinculacion y actualizacion
--              para la sección "ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO"
-- ============================================================================

-- Verificar si las columnas ya existen
SELECT 'Verificando columnas existentes...' AS status;

SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'forms' 
AND COLUMN_NAME IN ('vinculacion', 'fecha_vinculacion', 'actualizacion');

-- Agregar columna vinculacion si no existe
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS vinculacion ENUM('nueva', 'actualizacion') NULL 
COMMENT 'Tipo de vinculación: nueva o actualización';

-- Agregar columna fecha_vinculacion si no existe
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS fecha_vinculacion DATE NULL 
COMMENT 'Fecha de vinculación del cliente/proveedor';

-- Agregar columna actualizacion si no existe
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS actualizacion VARCHAR(255) NULL 
COMMENT 'Descripción de la actualización (ej: Primera actualización, Segunda actualización)';

-- Verificar que se agregaron correctamente
SELECT 'Verificando columnas agregadas...' AS status;

SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'forms' 
AND COLUMN_NAME IN ('vinculacion', 'fecha_vinculacion', 'actualizacion');

-- ============================================================================
-- NOTAS:
-- ============================================================================
-- Estos campos se usan en la sección:
-- "ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE CARTERA/COMPRAS"
-- 
-- - vinculacion: Indica si es una vinculación nueva o una actualización
-- - fecha_vinculacion: Fecha en que se vinculó el cliente/proveedor
-- - actualizacion: Descripción de qué actualización es (Primera, Segunda, etc.)
-- ============================================================================
