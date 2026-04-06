<!-- FCO-03: DECLARACIÓN ORIGEN DE FONDOS - PROVEEDORES -->
<?php
$fd = $form_data ?? [];
$pre_nombre     = htmlspecialchars($fd['company_name'] ?? $temp_data['company_name'] ?? '');
$pre_documento  = htmlspecialchars($fd['nit'] ?? $temp_data['document_number'] ?? '');
$pre_ciudad     = htmlspecialchars($fd['ciudad'] ?? '');
$pre_rep_nombre = htmlspecialchars($fd['representante_nombre'] ?? $fd['company_name'] ?? $temp_data['company_name'] ?? '');
$pre_rep_doc    = htmlspecialchars($fd['representante_documento'] ?? $fd['nit'] ?? $temp_data['document_number'] ?? '');
$pre_tipo_doc   = htmlspecialchars($fd['representante_tipo_doc'] ?? $fd['document_type'] ?? $temp_data['document_type'] ?? '');
$pre_ingresos   = htmlspecialchars($fd['ingresos'] ?? '');
$pre_gastos     = htmlspecialchars($fd['gastos'] ?? '');
$pre_activos    = htmlspecialchars($fd['activos'] ?? '');
$pre_pasivos    = htmlspecialchars($fd['pasivos'] ?? '');
?>

<!-- DECLARACIÓN -->
<div class="form-section">
    <div class="section-title">DECLARACIÓN DE ORIGEN DE FONDOS</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">NOMBRE DECLARANTE:</div>
            <div class="fv"><input type="text" name="nombre_declarante" required value="<?= $pre_rep_nombre ?>" placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TIPO DOC:</div>
            <div class="fv"><input type="text" name="tipo_documento" required value="<?= $pre_tipo_doc ?>" placeholder="CC / CE" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">NÚMERO DOCUMENTO:</div>
            <div class="fv"><input type="text" name="numero_documento" required value="<?= $pre_rep_doc ?>" placeholder="Número de documento" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CALIDAD:</div>
            <div class="fv"><input type="text" name="calidad" required placeholder="Representante legal / Propietario" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">EMPRESA:</div>
            <div class="fv"><input type="text" name="empresa" required value="<?= $pre_nombre ?>" placeholder="Nombre de la empresa" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">NIT EMPRESA:</div>
            <div class="fv"><input type="text" name="nit_empresa" required value="<?= $pre_documento ?>" placeholder="NIT" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr cfull">
            <div class="fv" style="border-right:none; padding:14px; font-size:12px; font-weight:600; color:var(--text-primary);">
                DECLARO BAJO LA GRAVEDAD DE JURAMENTO:
            </div>
        </div>
        <div class="fr cfull">
            <div class="fv" style="border-right:none; padding:10px 14px; font-size:11px; color:var(--text-secondary);">
                1. Que los recursos económicos que poseo y que utilizo en mis actividades comerciales provienen de:
            </div>
        </div>
        <div class="fr cfull">
            <div class="fv" style="border-right:none; padding:8px 14px; align-items:stretch;">
                <textarea name="origen_recursos" required rows="4" style="width:100%;" placeholder="Describa detalladamente el origen de los recursos (actividad económica, fuentes de ingreso, etc.)" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></textarea>
            </div>
        </div>
        <div class="fr cfull">
            <div class="fv" style="border-right:none; padding:10px 14px; font-size:11px; color:var(--text-secondary); line-height:1.7;">
                2. Que los recursos antes mencionados NO provienen de ninguna actividad ilícita.<br>
                3. Que la información aquí suministrada es veraz y verificable, y me comprometo a actualizarla anualmente.<br>
                4. Que autorizo a POLLO FIESTA S.A. para verificar la información aquí contenida.
            </div>
        </div>
    </div>
</div>

<!-- PEP -->
<div class="form-section">
    <div class="section-title">CALIDAD DE PERSONA EXPUESTA POLÍTICAMENTE (PEP)</div>
    <div class="section-content">
        <div class="fr cfull">
            <div class="fv" style="border-right:none; padding:10px 14px; font-size:11px; color:var(--text-secondary); text-align:justify;">
                De conformidad con lo establecido en la normatividad vigente, declaro si tengo o he tenido la calidad de Persona Expuesta Políticamente (PEP).
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">¿ES USTED PEP?</div>
            <div class="fv">
                <label><input type="radio" name="es_pep" value="si" required> SÍ</label>
                <label><input type="radio" name="es_pep" value="no" checked> NO</label>
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">SI SÍ, INDIQUE EL CARGO:</div>
            <div class="fv"><input type="text" name="cargo_pep" placeholder="Especifique el cargo público"></div>
        </div>
        <div class="fr c1">
            <div class="fl">¿FAMILIARES PEP (2° GRADO)?</div>
            <div class="fv">
                <label><input type="radio" name="familiar_pep" value="si" required> SÍ</label>
                <label><input type="radio" name="familiar_pep" value="no" checked> NO</label>
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">SI SÍ, NOMBRE Y PARENTESCO:</div>
            <div class="fv"><input type="text" name="familiar_pep_detalle" placeholder="Nombre completo y parentesco"></div>
        </div>
    </div>
</div>

<!-- INFORMACIÓN ADICIONAL -->
<div class="form-section">
    <div class="section-title">INFORMACIÓN ADICIONAL</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">INGRESOS MENSUALES $</div>
            <div class="fv"><input type="number" name="ingresos_mensuales" required step="0.01" min="0" value="<?= $pre_ingresos ?>" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">EGRESOS MENSUALES $</div>
            <div class="fv"><input type="number" name="egresos_mensuales" required step="0.01" min="0" value="<?= $pre_gastos ?>" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">TOTAL ACTIVOS $</div>
            <div class="fv"><input type="number" name="total_activos" required step="0.01" min="0" value="<?= $pre_activos ?>" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TOTAL PASIVOS $</div>
            <div class="fv"><input type="number" name="total_pasivos" required step="0.01" min="0" value="<?= $pre_pasivos ?>" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- DECLARACIÓN Y FIRMA -->
<div class="form-section">
    <div class="section-title">DECLARACIÓN Y FIRMA</div>
    <div class="section-content">
        <div class="fr cfull">
            <div class="fv" style="border-right:none; padding:10px 14px; font-size:11px; color:var(--text-secondary); text-align:justify;">
                Declaro que la información contenida en este documento es veraz y completa. Me comprometo a informar a POLLO FIESTA S.A. sobre cualquier cambio que se presente en la información aquí suministrada.
            </div>
        </div>
        <div class="info-box" style="margin:0; border-radius:0;">
            <strong>AVISO DE PRIVACIDAD:</strong> Autorizo a Pollo Fiesta S.A. (NIT 860.032.450-9) para tratamiento de datos según Ley 1581/2012, Decreto 1377/2013 y Ley 1266/2008. Política en <a href="http://www.pollo-fiesta.com/" target="_blank" style="color:var(--accent-primary);">www.pollo-fiesta.com</a>
        </div>
        <div class="fr c22">
            <div class="fl">NOMBRE COMPLETO:</div>
            <div class="fv"><input type="text" name="nombre_firma_final" required value="<?= $pre_rep_nombre ?>" placeholder="Nombre completo del declarante" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">DOCUMENTO:</div>
            <div class="fv"><input type="text" name="documento_firma" required value="<?= $pre_rep_doc ?>" placeholder="Número de documento" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">FECHA:</div>
            <div class="fv"><input type="date" name="fecha_declaracion" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CIUDAD:</div>
            <div class="fv"><input type="text" name="ciudad_declaracion" required value="<?= $pre_ciudad ?>" placeholder="Ciudad donde se firma" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">FIRMA:</div>
            <div class="fv" style="flex-direction:column; align-items:flex-start; gap:8px; padding:12px;">
                <input type="hidden" id="signature_declarante_fp" name="firma_declarante">
                <img id="sig_dec_fp_preview" src="" alt="Firma" style="display:none; max-width:300px; max-height:80px; border:1px solid var(--border-secondary); padding:5px; border-radius:4px; background:white;">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="button" id="sig_dec_fp_add" class="btn btn-primary" onclick="sigDecFP('open')" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:8px 14px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path></svg>
                        Firmar
                    </button>
                    <button type="button" id="sig_dec_fp_change" class="btn btn-secondary" onclick="sigDecFP('open')" style="display:none; font-size:12px; padding:8px 14px;">Cambiar</button>
                    <button type="button" id="sig_dec_fp_clear" class="btn btn-secondary" onclick="sigDecFP('clear')" style="display:none; font-size:12px; padding:8px 14px;">Limpiar</button>
                </div>
                <p id="sig-error" style="display:none; color:#fca5a5; font-size:11px; margin:0;">La firma es obligatoria</p>
            </div>
        </div>
    </div>
</div>

<?php if (false): ?>
<!-- ESPACIO EXCLUSIVO POLLO FIESTA — lo llena Angie -->
<div class="form-section internal-section">
    <div class="section-title">ESPACIO EXCLUSIVO PARA POLLO FIESTA</div>
    <div class="section-content">
        <div class="fr c22"><div class="fl">VERIFICACIÓN REALIZADA POR:</div><div class="fv"><input type="text" name="verificado_por"></div><div class="fl">FECHA VERIFICACIÓN:</div><div class="fv"><input type="date" name="fecha_verificacion"></div></div>
        <div class="fr c1"><div class="fl">FIRMA OFICIAL:</div><div class="fv"><input type="hidden" name="firma_oficial"></div></div>
    </div>
</div>
<?php endif; ?>
<?php if (false): ?>
<div class="form-section"><div class="section-title">OBSERVACIONES</div><div class="section-content"><div class="fr cfull"><div class="fv" style="border-right:none; padding:12px; align-items:stretch;"><textarea name="observaciones" rows="4" style="width:100%;"></textarea></div></div></div></div>
<?php endif; ?>

<script>
let _sigDecFP = null, _sigOficFP = null;
document.addEventListener('DOMContentLoaded', () => {
    _sigDecFP = new SignatureModal({ modalId: 'sigModalDecFP', onSave: (d) => {
        document.getElementById('signature_declarante_fp').value = d;
        const p = document.getElementById('sig_dec_fp_preview'); p.src = d; p.style.display = 'block';
        document.getElementById('sig_dec_fp_add').style.display = 'none';
        document.getElementById('sig_dec_fp_change').style.display = 'inline-flex';
        document.getElementById('sig_dec_fp_clear').style.display = 'inline-flex';
        const e = document.getElementById('sig-error'); if (e) e.style.display = 'none';
    }});
    _sigOficFP = new SignatureModal({ modalId: 'sigModalOficFP', onSave: (d) => {} });
});
function sigDecFP(action) {
    if (action === 'open' && _sigDecFP) { _sigDecFP.open(); return; }
    document.getElementById('signature_declarante_fp').value = '';
    document.getElementById('sig_dec_fp_preview').style.display = 'none';
    document.getElementById('sig_dec_fp_add').style.display = 'inline-flex';
    document.getElementById('sig_dec_fp_change').style.display = 'none';
    document.getElementById('sig_dec_fp_clear').style.display = 'none';
}
</script>
