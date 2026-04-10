-- Aumentar el límite de paquetes MySQL para permitir archivos grandes
-- Este script debe ejecutarse en MySQL para aumentar el límite de max_allowed_packet

-- Aumentar a 64MB (suficiente para varios archivos de 10MB)
SET GLOBAL max_allowed_packet = 67108864;

-- Verificar el cambio
SELECT @@global.max_allowed_packet as 'Max Allowed Packet (bytes)', 
       @@global.max_allowed_packet / 1024 / 1024 as 'Max Allowed Packet (MB)';

-- NOTA: Este cambio es temporal y se perderá al reiniciar MySQL
-- Para hacerlo permanente, agregar en el archivo de configuración de MySQL:
-- 
-- Windows: C:\ProgramData\MySQL\MySQL Server X.X\my.ini
-- Linux: /etc/mysql/my.cnf o /etc/my.cnf
-- 
-- [mysqld]
-- max_allowed_packet = 64M
-- 
-- Luego reiniciar el servicio MySQL
