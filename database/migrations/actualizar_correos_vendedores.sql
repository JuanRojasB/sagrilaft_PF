-- Script para actualizar correos de asesores comerciales
-- Reemplaza los valores según los correos reales de cada vendedor

-- EJEMPLO: Actualizar correo de un asesor
-- UPDATE asesores_comerciales 
-- SET email = 'asesor@pollo-fiesta.com',
--     jefe_email = 'gerente@pollo-fiesta.com'
-- WHERE nombre_completo = 'Nombre del Asesor';

-- PLANTILLA PARA COPIAR Y PEGAR:
-- Copia esta plantilla por cada vendedor que necesites actualizar

/*
UPDATE asesores_comerciales 
SET 
    email = 'correo_vendedor@pollo-fiesta.com',
    jefe_nombre = 'Nombre del Gerente',
    jefe_email = 'correo_gerente@pollo-fiesta.com',
    activo = 1
WHERE nombre_completo = 'Nombre Completo del Vendedor';
*/

-- EJEMPLOS DE GERENTES COMERCIALES COMUNES:
-- Hernan Mateo Benito: hernan.benito@pollo-fiesta.com
-- German Rodriguez: german.rodriguez@pollo-fiesta.com

-- ============================================
-- AGREGA TUS ACTUALIZACIONES AQUÍ:
-- ============================================

-- Vendedor 1
-- UPDATE asesores_comerciales 
-- SET email = 'vendedor1@pollo-fiesta.com',
--     jefe_nombre = 'Hernan Mateo Benito',
--     jefe_email = 'hernan.benito@pollo-fiesta.com'
-- WHERE id = 1;

-- Vendedor 2
-- UPDATE asesores_comerciales 
-- SET email = 'vendedor2@pollo-fiesta.com',
--     jefe_nombre = 'German Rodriguez',
--     jefe_email = 'german.rodriguez@pollo-fiesta.com'
-- WHERE id = 2;

-- Continúa agregando más vendedores...
