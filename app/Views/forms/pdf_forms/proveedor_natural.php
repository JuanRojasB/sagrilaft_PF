<!-- FD-05: CONOCIMIENTO DE PROVEEDOR NACIONAL - PERSONA NATURAL -->

<!-- ESPACIO COMPRAS -->
<div class="form-section">
    <div class="section-title">ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE COMPRAS</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">VINCULACIÓN:</div>
            <div class="fv"><input type="text" name="vinculacion" required placeholder="Tipo de vinculación" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">ACTUALIZACIÓN:</div>
            <div class="fv"><input type="text" name="actualizacion" required placeholder="Fecha o tipo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">FECHA DE VINCULACIÓN:</div>
            <div class="fv"><input type="date" name="fecha_vinculacion" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- DATOS DEL PROVEEDOR -->
<div class="form-section">
    <div class="section-title">DATOS DEL PROVEEDOR</div>
    <div class="section-content">
        <div class="fr c322">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><input type="text" name="nombre_proveedor" required value="<?= htmlspecialchars($temp_data['company_name'] ?? '') ?>" placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CÉDULA:</div>
            <div class="fv"><input type="text" name="numero_documento" required value="<?= htmlspecialchars($temp_data['document_number'] ?? '') ?>" placeholder="Número" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">RUT:</div>
            <div class="fv"><input type="text" name="rut" required placeholder="Número de RUT" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr dir-row">
            <div class="fl">DIRECCIÓN:</div>
            <div class="fv"><input type="text" id="direccion-proveedor-natural" name="direccion" required placeholder="Dirección completa" autocomplete="off" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">CIUDAD:</div>
            <div class="fv"><input type="text" id="ciudad-proveedor-natural" name="ciudad" required placeholder="Ciudad" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><input type="tel" name="telefono" required value="<?= htmlspecialchars($temp_data['phone'] ?? '') ?>" placeholder="Teléfono" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">FAX:</div>
            <div class="fv"><input type="tel" name="fax" required placeholder="Fax" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CORREO:</div>
            <div class="fv"><input type="email" name="email" required value="<?= htmlspecialchars($temp_data['email'] ?? '') ?>" placeholder="correo@ejemplo.com" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- ACTIVIDAD ECONÓMICA -->
<div class="form-section">
    <div class="section-title">ACTIVIDAD ECONÓMICA PRINCIPAL</div>
    <div class="section-content">
        <div class="info-box">Comercial, Industrial, Servicios, Transporte, Construcción, Agroindustria, etc. (código CIIU)</div>
        <div class="fr c1">
            <div class="fl">ACTIVIDAD:</div>
            <div class="fv"><select name="codigo_ciiu" required id="codigoCiiu_pn" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"><option value="">Seleccione actividad económica</option></select></div>
        </div>
        <div class="fr c1">
            <div class="fl">OBJETO SOCIAL:</div>
            <div class="fv" style="align-items:stretch;"><textarea name="objeto_social" required rows="2" placeholder="Describa el objeto social" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></textarea></div>
        </div>
    </div>
</div>

<!-- DECLARACIÓN ORIGEN DE FONDOS -->
<div class="form-section">
    <div class="section-title">DECLARACIÓN ORIGEN DE LOS FONDOS QUE PERCIBE</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><input type="text" name="representante_nombre" required placeholder="Nombre" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">DOCUMENTO:</div>
            <div class="fv"><input type="text" name="representante_documento" required placeholder="Número" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">TIPO DOC:</div>
            <div class="fv">
                <label><input type="radio" name="representante_tipo_doc" value="CC" required> CC</label>
                <label><input type="radio" name="representante_tipo_doc" value="CE"> CE</label>
            </div>
            <div class="fl">PROFESIÓN:</div>
            <div class="fv"><input type="text" name="representante_profesion" required placeholder="Profesión" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">NACIMIENTO:</div>
            <div class="fv"><input type="text" name="representante_lugar_nacimiento" required placeholder="Ciudad - DD/MM/AAAA" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><input type="tel" name="representante_telefono" required placeholder="Celular" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">RESIDENCIA:</div>
            <div class="fv"><input type="text" name="representante_residencia" required placeholder="Dirección de residencia" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- INFORMACIÓN FINANCIERA -->
<div class="form-section">
    <div class="section-title">INFORMACIÓN FINANCIERA</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">ACTIVOS $</div>
            <div class="fv"><input type="number" name="activos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">PASIVO $</div>
            <div class="fv"><input type="number" name="pasivos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">PATRIMONIO $</div>
            <div class="fv"><input type="number" name="patrimonio" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">INGRESOS $</div>
            <div class="fv"><input type="number" name="ingresos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">GASTOS $</div>
            <div class="fv"><input type="number" name="gastos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">OTROS ING. $</div>
            <div class="fv"><input type="number" name="otros_ingresos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">DETALLE OTROS INGRESOS:</div>
            <div class="fv" style="align-items:stretch;"><textarea name="detalle_otros_ingresos" required rows="2" placeholder="Describa otros ingresos" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></textarea></div>
        </div>
    </div>
</div>

<!-- CERTIFICACIONES Y AUTORIZACIÓN -->
<div class="form-section">
    <div class="section-title">CERTIFICACIONES Y AUTORIZACIÓN</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">CERTIFICACIÓN:</div>
            <div class="fv">
                <label><input type="radio" name="tiene_certificacion" value="si" required> SÍ</label>
                <label><input type="radio" name="tiene_certificacion" value="no"> NO</label>
            </div>
            <div class="fl">CUÁL:</div>
            <div class="fv"><input type="text" name="cual_certificacion" required placeholder="Especifique" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr cfull">
            <div class="fv" style="border-right:none; gap:12px; padding:12px 14px;">
                <span style="font-size:12px;">Autorizo consulta en Centrales de Riesgo:</span>
                <label><input type="radio" name="autoriza_centrales" value="si" required checked> SÍ</label>
                <label><input type="radio" name="autoriza_centrales" value="no"> NO</label>
            </div>
        </div>
        <div class="info-box" style="margin:0; border-radius:0;">
            <strong>AVISO:</strong> Autorizo a Pollo Fiesta S.A. (NIT 860.032.450-9) para tratamiento de datos según Ley 1581/2012. Política en www.pollo-fiesta.com
        </div>
    </div>
</div>

<!-- FIRMA DEL PROVEEDOR -->
<div class="form-section">
    <div class="section-title">FIRMA DEL PROVEEDOR</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><input type="text" name="nombre_firma_proveedor" required placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">FIRMA:</div>
            <div class="fv" style="flex-direction:column; align-items:flex-start; gap:8px; padding:12px;">
                <input type="hidden" id="signature_proveedor_pn" name="firma_proveedor_data">
                <img id="sig_prov_pn_preview" src="" alt="Firma" style="display:none; max-width:300px; max-height:80px; border:1px solid var(--border-secondary); padding:5px; border-radius:4px; background:white;">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="button" id="sig_prov_pn_add" class="btn btn-primary" onclick="sigProvPN('open')" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:8px 14px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path></svg>
                        Agregar Firma
                    </button>
                    <button type="button" id="sig_prov_pn_change" class="btn btn-secondary" onclick="sigProvPN('open')" style="display:none; font-size:12px; padding:8px 14px;">Cambiar</button>
                    <button type="button" id="sig_prov_pn_clear" class="btn btn-secondary" onclick="sigProvPN('clear')" style="display:none; font-size:12px; padding:8px 14px;">Limpiar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (false): ?>
<!-- ESPACIO EXCLUSIVO POLLO FIESTA — lo llena Angie -->
<div class="form-section internal-section">
    <div class="section-title">ESPACIO EXCLUSIVO PARA POLLO FIESTA</div>
    <div class="section-content">
        <div class="fr c1"><div class="fl">CONSULTA OFAC:</div><div class="fv"><label><input type="radio" name="consulta_ofac" value="negativa"> NEGATIVA</label><label><input type="radio" name="consulta_ofac" value="positiva"> POSITIVA</label></div></div>
        <div class="fr c1"><div class="fl">CONSULTA ONU:</div><div class="fv"><label><input type="radio" name="consulta_onu" value="negativa"> NEGATIVA</label><label><input type="radio" name="consulta_onu" value="positiva"> POSITIVA</label></div></div>
        <div class="fr c1"><div class="fl">NOMBRE OFICIAL:</div><div class="fv"><input type="text" name="nombre_oficial"></div></div>
        <div class="fr c1"><div class="fl">FIRMA OFICIAL:</div><div class="fv"><input type="hidden" name="firma_oficial_data"></div></div>
    </div>
</div>
<?php endif; ?>
<?php if (false): ?>
<div class="form-section"><div class="section-title">OBSERVACIONES</div><div class="section-content"><div class="fr cfull"><div class="fv" style="border-right:none; padding:12px; align-items:stretch;"><textarea name="observaciones" rows="3" style="width:100%;"></textarea></div></div></div></div>
<?php endif; ?>

<script>
let _sigProvPN = null, _sigOficPN = null;
document.addEventListener('DOMContentLoaded', () => {
    _sigProvPN = new SignatureModal({ modalId: 'sigModalProvPN', onSave: (d) => {
        document.getElementById('signature_proveedor_pn').value = d;
        const p = document.getElementById('sig_prov_pn_preview'); p.src = d; p.style.display = 'block';
        document.getElementById('sig_prov_pn_add').style.display = 'none';
        document.getElementById('sig_prov_pn_change').style.display = 'inline-flex';
        document.getElementById('sig_prov_pn_clear').style.display = 'inline-flex';
    }});
    _sigOficPN = new SignatureModal({ modalId: 'sigModalOficPN', onSave: (d) => {} });
    fetch('<?= $_ENV['APP_URL'] ?>/api/actividades-economicas.php')
        .then(r => r.json()).then(data => {
            const s = document.getElementById('codigoCiiu_pn');
            data.forEach(a => { const o = document.createElement('option'); o.value = a.codigo; o.textContent = `${a.codigo} - ${a.descripcion}`; s.appendChild(o); });
        }).catch(() => {});
});
function sigProvPN(action) {
    if (action === 'open' && _sigProvPN) { _sigProvPN.open(); return; }
    document.getElementById('signature_proveedor_pn').value = '';
    document.getElementById('sig_prov_pn_preview').style.display = 'none';
    document.getElementById('sig_prov_pn_add').style.display = 'inline-flex';
    document.getElementById('sig_prov_pn_change').style.display = 'none';
    document.getElementById('sig_prov_pn_clear').style.display = 'none';
}
</script>
