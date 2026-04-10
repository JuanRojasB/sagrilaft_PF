-- ============================================================================
-- MIGRACIÓN: Agregar tipo de usuario "Empleado"
-- ============================================================================
-- Agrega el tipo "empleado" al sistema SAGRILAFT
-- Incluye campos específicos para empleados
-- ============================================================================

USE sagrilaft;

-- Modificar el ENUM de form_type para incluir 'empleado'
ALTER TABLE forms_sagrilaft 
MODIFY COLUMN form_type ENUM('cliente', 'proveedor', 'transportista', 'empleado') DEFAULT 'cliente';

-- Agregar campos específicos para empleados
ALTER TABLE forms_sagrilaft
ADD COLUMN empleado_nombre VARCHAR(255) COMMENT 'Nombre completo del empleado' AFTER related_form_id,
ADD COLUMN empleado_cedula VARCHAR(50) COMMENT 'Número de cédula del empleado' AFTER empleado_nombre,
ADD COLUMN empleado_cargo VARCHAR(255) COMMENT 'Cargo del empleado' AFTER empleado_cedula,
ADD COLUMN empleado_fecha_nacimiento DATE COMMENT 'Fecha de nacimiento del empleado' AFTER empleado_cargo,
ADD COLUMN empleado_pdf_cedula_required BOOLEAN DEFAULT FALSE COMMENT 'Indica si el PDF de cédula es requerido para este empleado' AFTER empleado_fecha_nacimiento;

-- Agregar índice para búsqueda por cédula de empleado
ALTER TABLE forms_sagrilaft
ADD INDEX idx_empleado_cedula (empleado_cedula);

-- Verificar cambios
SELECT 'Migración completada - Tipo empleado agregado exitosamente' AS mensaje;
SHOW COLUMNS FROM forms_sagrilaft LIKE 'form_type';
SHOW COLUMNS FROM forms_sagrilaft LIKE 'empleado_%';
