-- Actualizar contraseñas de usuarios principales
-- admin2026* para el administrador
-- angie2026* para la revisora

UPDATE users 
SET password = '$2y$10$Mfi7WDbJvzh6RrK7FcbzX.74v.3fTvrQ2W6Vt9oRrLS4WQX8B9Vfy'
WHERE email = 'admin@pollofiesta.com';

UPDATE users 
SET password = '$2y$10$FrIaKeorCQ5/gc.QoTH1WOE4wMkZqBNCixxt8aaa8I35tIgtv1xUm'
WHERE name = 'Angie Paola Martínez Paredes' AND role = 'revisor';

-- Verificar
SELECT name, email, role FROM users WHERE role IN ('admin', 'revisor');
