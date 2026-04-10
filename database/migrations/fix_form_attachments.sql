-- ============================================================================
-- MIGRACIÓN: Agregar columna original_filename si no existe
-- ============================================================================

USE sagrilaft;

-- Verificar y agregar columna original_filename si no existe
SET @dbname = DATABASE();
SET @tablename = 'form_attachments';
SET @columnname = 'original_filename';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " VARCHAR(255) NOT NULL AFTER filename")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SELECT 'Migración completada - Columna original_filename verificada/agregada' AS mensaje;
