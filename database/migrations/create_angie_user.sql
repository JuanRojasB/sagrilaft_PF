-- ============================================================================
-- Migración: Crear usuario para Angie Paola Martínez Paredes
-- Fecha: 2026-04-14
-- Descripción: Crea el usuario revisor para Angie (Oficial de Cumplimiento)
-- ============================================================================

-- Verificar si el usuario ya existe
SELECT 'Verificando si el usuario ya existe...' AS status;

-- Insertar usuario de Angie si no existe
INSERT INTO users (name, email, password, role, created_at, updated_at)
SELECT 
    'Angie Paola Martínez Paredes',
    'oficialdecumplimiento@pollo-fiesta.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Password: password (cambiar después)
    'revisor',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'oficialdecumplimiento@pollo-fiesta.com'
);

-- Verificar que se creó correctamente
SELECT 
    id,
    name,
    email,
    role,
    created_at
FROM users 
WHERE email = 'oficialdecumplimiento@pollo-fiesta.com';

-- ============================================================================
-- NOTAS IMPORTANTES:
-- ============================================================================
-- 1. Usuario: a.martinez
-- 2. Email: oficialdecumplimiento@pollo-fiesta.com
-- 3. Password: angie1404*
-- 4. Role: revisor
-- 5. Para cambiar la contraseña, usar el panel de administración o ejecutar:
--    UPDATE users SET password = '$2y$10$[nuevo_hash]' WHERE email = 'oficialdecumplimiento@pollo-fiesta.com';
-- ============================================================================

-- Para generar un nuevo hash de contraseña en PHP:
-- password_hash('nueva_contraseña', PASSWORD_DEFAULT);
