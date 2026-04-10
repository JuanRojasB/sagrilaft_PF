<!-- FD-09: REGISTRO DE EMPLEADO -->

<!-- DATOS DEL EMPLEADO -->
<div class="form-section">
    <div class="section-title">DATOS DEL EMPLEADO</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">NOMBRE COMPLETO:</div>
            <div class="fv">
                <input type="text" 
                       name="empleado_nombre" 
                       id="empleado_nombre"
                       value="<?= htmlspecialchars($form_data['empleado_nombre'] ?? '') ?>"
                       placeholder="Nombre completo"
                       required
                       oninvalid="this.setCustomValidity('Requerido')" 
                       oninput="this.setCustomValidity('')">
            </div>
        </div>
        <div class="fr c22">
            <div class="fl">NÚMERO DE CÉDULA:</div>
            <div class="fv">
                <input type="text" 
                       name="empleado_cedula" 
                       id="empleado_cedula"
                       value="<?= htmlspecialchars($form_data['empleado_cedula'] ?? '') ?>"
                       placeholder="Ej: 1234567890"
                       required
                       oninvalid="this.setCustomValidity('Requerido')" 
                       oninput="this.setCustomValidity('')">
            </div>
            <div class="fl">FECHA DE NACIMIENTO:</div>
            <div class="fv">
                <input type="date" 
                       name="empleado_fecha_nacimiento" 
                       id="empleado_fecha_nacimiento"
                       value="<?= htmlspecialchars($form_data['empleado_fecha_nacimiento'] ?? '') ?>"
                       required
                       oninvalid="this.setCustomValidity('Requerido')" 
                       oninput="this.setCustomValidity('')">
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">CARGO:</div>
            <div class="fv">
                <input type="text" 
                       name="empleado_cargo" 
                       id="empleado_cargo"
                       value="<?= htmlspecialchars($form_data['empleado_cargo'] ?? '') ?>"
                       placeholder="Cargo del empleado"
                       required
                       oninvalid="this.setCustomValidity('Requerido')" 
                       oninput="this.setCustomValidity('')">
            </div>
        </div>
    </div>
</div>

<!-- ADJUNTAR PDF DE CÉDULA (OPCIONAL) -->
<div class="form-section">
    <div class="section-title">ADJUNTAR PDF DE CÉDULA (OPCIONAL)</div>
    <div class="section-content">
        <div class="info-box" style="margin: 15px;">
            <strong>Nota:</strong> El PDF de la cédula es opcional. Si está enviando múltiples empleados 
            que comparten el mismo documento, puede adjuntarlo una sola vez en el primer registro.
        </div>
        <div class="fr c1">
            <div class="fl">PDF DE CÉDULA:</div>
            <div class="fv" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                <input type="file" 
                       name="cedula_pdf" 
                       id="cedula_pdf"
                       accept=".pdf"
                       style="display: none;">
                <button type="button" 
                        onclick="document.getElementById('cedula_pdf').click()" 
                        style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-primary); padding: 8px 16px; border-radius: var(--radius-sm); cursor: pointer; font-size: 14px; font-family: var(--font-primary);">
                    + Adjuntar PDF de Cédula
                </button>
                <p style="color: var(--text-muted); font-size: 12px; margin: 0;">
                    Solo PDF. Máximo 10MB.
                </p>
                <div id="cedulaFilePreview" style="display: none; margin-top: 8px; padding: 8px 12px; background: rgba(241, 245, 249, 0.8); border: 1px solid rgba(71, 85, 105, 0.3); border-radius: 4px; width: 100%;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="cedulaFileName" style="color: #334155; font-size: 13px;"></span>
                        <button type="button" 
                                onclick="clearCedulaFile()" 
                                style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">
                            Quitar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Manejo del archivo de cédula
    document.getElementById('cedula_pdf').addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            // Validar tipo
            if (file.type !== 'application/pdf') {
                alert('Solo se permiten archivos PDF');
                this.value = '';
                return;
            }
            
            // Validar tamaño (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('El archivo no debe superar 10MB');
                this.value = '';
                return;
            }
            
            // Mostrar preview
            const size = (file.size / 1024 / 1024).toFixed(2);
            document.getElementById('cedulaFileName').textContent = `${file.name} (${size} MB)`;
            document.getElementById('cedulaFilePreview').style.display = 'block';
        }
    });
    
    function clearCedulaFile() {
        document.getElementById('cedula_pdf').value = '';
        document.getElementById('cedulaFilePreview').style.display = 'none';
    }
</script>

<!-- INFORMACIÓN ADICIONAL -->
<div class="form-section">
    <div class="section-title">OBSERVACIONES</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">OBSERVACIONES:</div>
            <div class="fv" style="align-items: stretch;">
                <textarea name="observaciones" 
                          id="observaciones"
                          rows="3"
                          placeholder="Observaciones adicionales (opcional)"><?= htmlspecialchars($form_data['observaciones'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
</div>
