<!-- FCO-02: CONOCIMIENTO DE PROVEEDORES NACIONAL PERSONA JURIDICA -->

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

<!-- DATOS GENERALES -->
<div class="form-section">
    <div class="section-title">DATOS GENERALES DE LA EMPRESA</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">RAZÓN SOCIAL:</div>
            <div class="fv"><input type="text" name="razon_social" required value="<?= htmlspecialchars($temp_data['company_name'] ?? '') ?>" placeholder="Razón social" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">NIT:</div>
            <div class="fv"><input type="text" name="nit" required value="<?= htmlspecialchars($temp_data['document_number'] ?? '') ?>" placeholder="NIT" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">D.V.:</div>
            <div class="fv"><input type="text" name="dv" required maxlength="1" placeholder="DV" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">RUT:</div>
            <div class="fv"><input type="text" name="rut" required placeholder="RUT" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c33">
            <div class="fl">TEL. FIJO:</div>
            <div class="fv"><input type="tel" name="telefono" required value="<?= htmlspecialchars($temp_data['phone'] ?? '') ?>" placeholder="Fijo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">FAX:</div>
            <div class="fv"><input type="tel" name="fax" required placeholder="Fax" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CORREO:</div>
            <div class="fv"><input type="email" name="email" required value="<?= htmlspecialchars($temp_data['email'] ?? '') ?>" placeholder="correo@ejemplo.com" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr dir-row">
            <div class="fl">DIRECCIÓN:</div>
            <div class="fv"><input type="text" id="direccion-proveedor-juridica" name="direccion" required placeholder="Dirección completa" autocomplete="off" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">CIUDAD:</div>
            <div class="fv"><input type="text" id="ciudad-proveedor-juridica" name="ciudad" required placeholder="Ciudad" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TIPO COMPAÑÍA:</div>
            <div class="fv">
                <label><input type="radio" name="tipo_compania" value="privada" required> PRIVADA</label>
                <label><input type="radio" name="tipo_compania" value="publica"> PÚBLICA</label>
                <label><input type="radio" name="tipo_compania" value="mixta"> MIXTA</label>
            </div>
        </div>
    </div>
</div>

<!-- ACTIVIDAD ECONÓMICA -->
<div class="form-section">
    <div class="section-title">ACTIVIDAD ECONÓMICA PRINCIPAL DE LA EMPRESA</div>
    <div class="section-content">
        <div class="info-box">Comercial, Industrial, Servicios, Transporte, Construcción, Agroindustria, etc. (código CIIU)</div>
        <div class="fr c1">
            <div class="fl">ACTIVIDAD:</div>
            <div class="fv"><select name="codigo_ciiu" required id="codigoCiiu_pj" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"><option value="">Seleccione actividad económica</option></select></div>
        </div>
        <div class="fr c1">
            <div class="fl">OBJETO SOCIAL:</div>
            <div class="fv" style="align-items:stretch;"><textarea name="objeto_social" required rows="2" placeholder="Describa el objeto social" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></textarea></div>
        </div>
    </div>
</div>

<!-- REPRESENTANTE LEGAL -->
<div class="form-section">
    <div class="section-title">DATOS DEL REPRESENTANTE LEGAL Y/O APODERADO</div>
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
                <label><input type="checkbox" name="representante_tipo_doc_cc" value="CC"> CC</label>
                <label><input type="checkbox" name="representante_tipo_doc_ce" value="CE"> CE</label>
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
            <div class="fv"><input type="text" name="representante_residencia" required placeholder="Dirección" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- COMPOSICIÓN ACCIONARIA -->
<div class="form-section">
    <div class="section-title">COMPOSICIÓN ACCIONARIA (Accionistas con participación mayor al 5%)</div>
    <div class="section-content">
        <table class="field-table" id="accionistasTable">
            <thead>
                <tr style="background: var(--bg-tertiary);">
                    <td class="field-label" style="width:50%;">NOMBRE / RAZÓN SOCIAL</td>
                    <td class="field-label" style="width:25%;">C.C</td>
                    <td class="field-label" style="width:20%;">C.E</td>
                    <td class="field-label" style="width:5%;"></td>
                </tr>
            </thead>
            <tbody id="accionistasBody">
                <tr>
                    <td><input type="text" name="accionista_nombre[]" required placeholder="Nombre completo o razón social" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td><input type="text" name="accionista_cc[]" required placeholder="CC" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td><input type="text" name="accionista_ce[]" placeholder="CE"></td>
                    <td style="text-align:center;"><button type="button" onclick="removeAccionistaPJ(this)" style="font-size:10px; padding:2px 6px; background:#ef4444; color:white; border:none; border-radius:3px; cursor:pointer;">Quitar</button></td>
                </tr>
            </tbody>
        </table>
        <div style="padding:6px; text-align:center;">
            <button type="button" onclick="addAccionistaPJ()" style="font-size:11px; padding:5px 14px; background:#10b981; color:white; border:none; border-radius:4px; cursor:pointer;">+ Agregar Accionista</button>
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

<!-- CERTIFICACIONES Y DATOS TRIBUTARIOS -->
<div class="form-section">
    <div class="section-title">CERTIFICACIONES Y DATOS TRIBUTARIOS</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">CERTIFICACIÓN:</div>
            <div class="fv"><input type="text" name="certificacion" required placeholder="Especifique certificación o certificado de origen" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">TIPO CONTRIBUYENTE:</div>
            <div class="fv">
                <label><input type="radio" name="tipo_contribuyente" value="persona_juridica" required> Persona Jurídica</label>
                <label><input type="radio" name="tipo_contribuyente" value="gran_contribuyente"> Gran Contribuyente</label>
            </div>
            <div class="fl">RÉGIMEN:</div>
            <div class="fv">
                <label><input type="radio" name="regimen_tributario" value="especial" required> Especial</label>
                <label><input type="radio" name="regimen_tributario" value="no_contribuyente"> No Contribuyente</label>
            </div>
        </div>
    </div>
</div>

<!-- AUTORIZACIÓN Y FIRMA -->
<div class="form-section">
    <div class="section-title">AUTORIZACIÓN Y FIRMA DEL REPRESENTANTE LEGAL</div>
    <div class="section-content">
        <div class="fr cfull">
            <div class="fv" style="border-right:none; gap:12px; padding:12px 14px;">
                <span style="font-size:12px;">Autorizo consulta en Centrales de Riesgo:</span>
                <label><input type="radio" name="autoriza_centrales" value="si" required checked> SÍ</label>
                <label><input type="radio" name="autoriza_centrales" value="no"> NO</label>
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><input type="text" name="nombre_firma_representante" required placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="info-box" style="margin:0; border-radius:0;">
            <strong>AVISO:</strong> Autorizo a Pollo Fiesta S.A. (NIT 860.032.450-9) para tratamiento de datos según Ley 1581/2012. Política en www.pollo-fiesta.com
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
        <div class="fr c22"><div class="fl">PREPARÓ:</div><div class="fv"><input type="text" name="preparo"></div><div class="fl">REVISÓ:</div><div class="fv"><input type="text" name="reviso"></div></div>
        <div class="fr c1"><div class="fl">NOMBRE OFICIAL:</div><div class="fv"><input type="text" name="nombre_oficial"></div></div>
        <div class="fr c1"><div class="fl">FIRMA PREPARÓ:</div><div class="fv"><input type="hidden" name="firma_preparo_data"></div></div>
        <div class="fr c1"><div class="fl">FIRMA OFICIAL:</div><div class="fv"><input type="hidden" name="firma_oficial_cumplimiento_data"></div></div>
    </div>
</div>
<?php endif; ?>
<?php if (false): ?>
<div class="form-section"><div class="section-title">OBSERVACIONES</div><div class="section-content"><div class="fr cfull"><div class="fv" style="border-right:none; padding:12px; align-items:stretch;"><textarea name="observaciones" rows="3" style="width:100%;"></textarea></div></div></div></div>
<?php endif; ?>

<script>
let _sigPrepPJ = null, _sigOficPJ = null;
document.addEventListener('DOMContentLoaded', () => {
    _sigPrepPJ = new SignatureModal({ modalId: 'sigModalPrepPJ', onSave: (d) => {} });
    _sigOficPJ = new SignatureModal({ modalId: 'sigModalOficPJ', onSave: (d) => {} });
    fetch('<?= $_ENV['APP_URL'] ?>/api/actividades-economicas.php')
        .then(r => r.json()).then(data => {
            const s = document.getElementById('codigoCiiu_pj');
            data.forEach(a => { const o = document.createElement('option'); o.value = a.codigo; o.textContent = `${a.codigo} - ${a.descripcion}`; s.appendChild(o); });
        }).catch(() => {});
});
function addAccionistaPJ() {
    const tbody = document.getElementById('accionistasBody');
    const row = document.createElement('tr');
    row.innerHTML = `<td><input type="text" name="accionista_nombre[]" required placeholder="Nombre completo o razón social" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td><input type="text" name="accionista_cc[]" required placeholder="CC" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td><input type="text" name="accionista_ce[]" placeholder="CE"></td><td style="text-align:center;"><button type="button" onclick="removeAccionistaPJ(this)" style="font-size:10px; padding:2px 6px; background:#ef4444; color:white; border:none; border-radius:3px; cursor:pointer;">Quitar</button></td>`;
    tbody.appendChild(row);
}
function removeAccionistaPJ(btn) {
    const tbody = document.getElementById('accionistasBody');
    if (tbody.children.length > 1) btn.closest('tr').remove();
    else alert('Debe haber al menos un accionista');
}
</script>
