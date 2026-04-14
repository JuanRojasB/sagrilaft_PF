-- Verificar correos de asesores comerciales
-- Este script muestra todos los asesores y sus correos actuales

SELECT 
    id,
    nombre_completo,
    email,
    jefe_nombre,
    jefe_email,
    activo
FROM asesores_comerciales
ORDER BY activo DESC, nombre_completo ASC;

-- Si necesitas actualizar correos, usa este formato:
-- UPDATE asesores_comerciales SET email = 'nuevo@pollo-fiesta.com' WHERE id = 1;
-- UPDATE asesores_comerciales SET jefe_email = 'gerente@pollo-fiesta.com' WHERE id = 1;
