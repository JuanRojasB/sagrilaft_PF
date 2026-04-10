<!-- FD-09: REGISTRO DE EMPLEADO -->

<!-- DATOS DEL EMPLEADO -->
<div class="form-section">
    <div class="section-title">DATOS DEL EMPLEADO</div>
    <div class="section-content">
        <table class="field-table">
            <tr>
                <td class="field-label" style="width: 25%;">NOMBRE COMPLETO</td>
                <td class="field-input" colspan="3">
                    <input type="text" 
                           name="empleado_nombre" 
                           id="empleado_nombre"
                           value="<?= htmlspecialchars($form_data['empleado_nombre'] ?? '') ?>"
                           placeholder="Ingrese el nombre completo del empleado"
                           required>
                </td>
            </tr>
            <tr>
                <td class="field-label">NÚMERO DE CÉDULA</td>
                <td class="field-input">
                    <input type="text" 
                           name="empleado_cedula" 
                           id="empleado_cedula"
                           value="<?= htmlspecialchars($form_data['empleado_cedula'] ?? '') ?>"
                           placeholder="Ej: 1234567890"
                           required>
                </td>
                <td class="field-label" style="width: 25%;">FECHA DE NACIMIENTO</td>
                <td class="field-input">
                    <input type="date" 
                           name="empleado_fecha_nacimiento" 
                           id="empleado_fecha_nacimiento"
                           value="<?= htmlspecialchars($form_data['empleado_fecha_nacimiento'] ?? '') ?>"
                           required>
                </td>
            </tr>
            <tr>
                <td class="field-label">CARGO</td>
                <td class="field-input" colspan="3">
                    <input type="text" 
                           name="empleado_cargo" 
                           id="empleado_cargo"
                           value="<?= htmlspecialchars($form_data['empleado_cargo'] ?? '') ?>"
                           placeholder="Ingrese el cargo del empleado"
                           required>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ADJUNTAR PDF DE CÉDULA (OPCIONAL) -->
<div class="form-section">
    <div class="section-title">ADJUNTAR PDF DE CÉDULA (OPCIONAL)</div>
    <div class="section-content">
        <table class="field-table">
            <tr>
                <td class="field-label" style="width: 25%;">PDF DE CÉDULA</td>
                <td class="field-input">
                    <div class="info-box" style="margin: 0.5rem 0;">
                        <strong>Nota:</strong> El PDF de la cédula es opcional. Si está enviando múltiples empleados 
                        que comparten el mismo documento, puede adjuntarlo una sola vez en el primer registro.
                    </div>
                    <input type="file" 
                           name="cedula_pdf" 
                           id="cedula_pdf"
                           accept=".pdf"
                           class="file-input">
                    <small style="display: block; margin-top: 0.5rem; color: #666;">
                        Formatos aceptados: PDF. Tamaño máximo: 5MB
                    </small>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- INFORMACIÓN ADICIONAL -->
<div class="form-section">
    <div class="section-title">OBSERVACIONES</div>
    <div class="section-content">
        <table class="field-table">
            <tr>
                <td class="field-label" style="width: 25%;">OBSERVACIONES</td>
                <td class="field-input">
                    <textarea name="observaciones" 
                              id="observaciones"
                              rows="3"
                              placeholder="Ingrese cualquier observación adicional (opcional)"><?= htmlspecialchars($form_data['observaciones'] ?? '') ?></textarea>
                </td>
            </tr>
        </table>
    </div>
</div>

<style>
    .file-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid var(--border-primary);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
    }
    
    @media print {
        .file-input {
            border: 0.5px solid #999 !important;
            padding: 1px !important;
            font-size: 5.5px !important;
        }
    }
</style>
