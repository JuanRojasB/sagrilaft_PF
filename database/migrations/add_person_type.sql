-- Agregar columna person_type a la tabla forms si no existe
ALTER TABLE forms
    ADD COLUMN IF NOT EXISTS person_type ENUM('natural', 'juridica') DEFAULT 'natural'
        AFTER form_type;
