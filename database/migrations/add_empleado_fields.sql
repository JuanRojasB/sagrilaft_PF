-- Crear tabla separada para datos de empleados
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
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
    INDEX idx_form_id (form_id),
    INDEX idx_cedula (empleado_cedula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar 'empleado' al ENUM de form_type
ALTER TABLE forms 
MODIFY COLUMN form_type ENUM('cliente','proveedor','transportista','declaracion_cliente','declaracion_proveedor','empleado') DEFAULT 'cliente';

-- Si la tabla ya existe, agregar las columnas faltantes
ALTER TABLE form_empleados 
ADD COLUMN IF NOT EXISTS empleado_ciudad_vacante VARCHAR(255) NULL AFTER empleado_cargo,
ADD COLUMN IF NOT EXISTS empleado_ciudad_nacimiento VARCHAR(255) NULL AFTER empleado_ciudad_vacante,
ADD COLUMN IF NOT EXISTS empleado_celular VARCHAR(50) NULL AFTER empleado_fecha_nacimiento;
