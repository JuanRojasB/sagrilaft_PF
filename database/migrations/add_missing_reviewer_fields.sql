-- ============================================================================
-- MIGRACIÓN: Agregar campos faltantes del revisor
-- ============================================================================
-- Fecha: 2024-12-22
-- Descripción: Agregar todos los campos que el revisor debe llenar
-- ============================================================================

USE sagrilaft;

-- Agregar campos de vinculación si no existen
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS vinculacion ENUM('nueva', 'actualizacion') COMMENT 'Tipo de vinculación';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS fecha_vinculacion DATE COMMENT 'Fecha de vinculación del cliente/proveedor';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS actualizacion VARCHAR(255) COMMENT 'Descripción de la actualización';

-- Agregar campo consulta_interpol si no existe
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS consulta_interpol ENUM('negativa', 'positiva') COMMENT 'Resultado consulta INTERPOL';

-- Agregar campos internos de Pollo Fiesta si no existen
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS recibe VARCHAR(255) COMMENT 'Persona que recibe en Pollo Fiesta';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS verificado_por VARCHAR(255) COMMENT 'Persona que verifica';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS preparo VARCHAR(255) COMMENT 'Persona que preparó';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS reviso VARCHAR(255) COMMENT 'Persona que revisó';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS nombre_oficial VARCHAR(255) COMMENT 'Nombre del oficial de cumplimiento';

-- Agregar campos adicionales para empleados si no existen
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS empleado_nombre VARCHAR(255) COMMENT 'Nombre completo del empleado';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS empleado_cedula VARCHAR(50) COMMENT 'Cédula del empleado';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS empleado_cargo VARCHAR(255) COMMENT 'Cargo del empleado';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS empleado_ciudad_vacante VARCHAR(255) COMMENT 'Ciudad de la vacante';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS empleado_ciudad_nacimiento VARCHAR(255) COMMENT 'Ciudad de nacimiento';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS empleado_fecha_nacimiento DATE COMMENT 'Fecha de nacimiento del empleado';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS empleado_celular VARCHAR(50) COMMENT 'Celular del empleado';

-- Agregar campos de firma digital si no existen
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS signature_data LONGTEXT COMMENT 'Datos de la firma del usuario (base64)';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS firma_oficial_data LONGTEXT COMMENT 'Datos de la firma del oficial (base64)';

-- Agregar campos de observaciones del revisor
ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS approval_observations TEXT COMMENT 'Observaciones del revisor';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS reviewed_at DATETIME COMMENT 'Fecha de revisión';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS reviewed_by VARCHAR(255) COMMENT 'Nombre del revisor';

ALTER TABLE forms 
ADD COLUMN IF NOT EXISTS reviewed_by_name VARCHAR(255) COMMENT 'Nombre completo del revisor';

-- Verificar estructura actualizada
DESCRIBE forms;