-- ============================================================================
-- LIMPIEZA DE BASE DE DATOS PARA PRODUCCIÓN
-- ============================================================================
-- Fecha: 2024-12-22
-- Descripción: Limpiar datos de prueba y dejar solo estructura y datos esenciales
-- ============================================================================

USE sagrilaft;

-- Deshabilitar verificaciones temporalmente
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_SAFE_UPDATES = 0;

-- ============================================================================
-- LIMPIAR DATOS DE PRUEBA
-- ============================================================================

-- Limpiar formularios de prueba (mantener solo estructura)
DELETE FROM form_consolidated_pdfs;
DELETE FROM form_attachments;
DELETE FROM form_signatures;
DELETE FROM form_empleados;
DELETE FROM forms;

-- Limpiar firmas digitales de prueba
DELETE FROM firmas_digitales;

-- Limpiar usuarios de prueba (mantener solo usuarios esenciales)
DELETE FROM users WHERE email NOT IN (
    'angie.martinez@pollofiesta.com',
    'admin@pollofiesta.com'
);

-- ============================================================================
-- RESETEAR AUTO_INCREMENT
-- ============================================================================

ALTER TABLE forms AUTO_INCREMENT = 1;
ALTER TABLE form_empleados AUTO_INCREMENT = 1;
ALTER TABLE form_attachments AUTO_INCREMENT = 1;
ALTER TABLE form_consolidated_pdfs AUTO_INCREMENT = 1;
ALTER TABLE form_signatures AUTO_INCREMENT = 1;
ALTER TABLE firmas_digitales AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;

-- ============================================================================
-- CREAR USUARIOS ESENCIALES PARA PRODUCCIÓN
-- ============================================================================

-- Usuario administrador principal
INSERT IGNORE INTO users (name, email, password, role, created_at) VALUES 
('Administrador', 'admin@pollofiesta.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());

-- Usuario revisor principal (Angie Paola Martínez Paredes)
INSERT IGNORE INTO users (name, email, password, role, created_at) VALUES 
('Angie Paola Martínez Paredes', 'angie.martinez@pollofiesta.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'revisor', NOW());

-- ============================================================================
-- VERIFICAR ESTRUCTURA DE TABLAS ESENCIALES
-- ============================================================================

-- Verificar que todas las tablas existen
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'sagrilaft' 
    AND TABLE_NAME IN (
        'users', 'forms', 'form_empleados', 'form_attachments', 
        'form_consolidated_pdfs', 'form_signatures', 'firmas_digitales',
        'asesores_comerciales'
    )
ORDER BY TABLE_NAME;

-- ============================================================================
-- VERIFICAR DATOS ESENCIALES
-- ============================================================================

-- Verificar usuarios creados
SELECT id, name, email, role, created_at FROM users ORDER BY id;

-- Verificar asesores comerciales (deben existir para el campo "Preparó")
SELECT COUNT(*) as total_asesores FROM asesores_comerciales WHERE activo = 1;

-- Reactivar verificaciones
SET FOREIGN_KEY_CHECKS = 1;
SET SQL_SAFE_UPDATES = 1;

-- ============================================================================
-- RESUMEN DE LIMPIEZA
-- ============================================================================

SELECT 'BASE DE DATOS LIMPIA PARA PRODUCCIÓN' as status,
       (SELECT COUNT(*) FROM forms) as formularios_total,
       (SELECT COUNT(*) FROM users) as usuarios_total,
       (SELECT COUNT(*) FROM firmas_digitales) as firmas_total,
       (SELECT COUNT(*) FROM form_signatures) as signatures_total,
       NOW() as fecha_limpieza;