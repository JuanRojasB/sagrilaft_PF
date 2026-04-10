<!-- FD-16: CREACIÓN DE CLIENTES - PERSONA JURÍDICA -->

<!-- ESPACIO CARTERA -->
<div class="form-section">
    <div class="section-title">ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE CARTERA</div>
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
    <div class="section-title">DATOS GENERALES</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">PRINCIPAL:</div>
            <div class="fv"><input type="text" name="principal" required placeholder="Datos principal" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">SUCURSAL:</div>
            <div class="fv"><input type="text" name="sucursal_campo" required placeholder="Datos sucursal" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
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
        <div class="fr dir-row">
            <div class="fl">DOMICILIO:</div>
            <div class="fv"><input type="text" id="direccion-cliente-juridica" name="direccion" required placeholder="Dirección completa" autocomplete="off" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">CIUDAD:</div>
            <div class="fv"><input type="text" id="ciudad-cliente-juridica" name="ciudad" required placeholder="Ciudad" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">BARRIO:</div>
            <div class="fv"><input type="text" name="barrio" required placeholder="Barrio" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">LOCALIDAD:</div>
            <div class="fv"><input type="text" name="localidad" required placeholder="Localidad" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">SUCURSAL DIR.:</div>
            <div class="fv"><input type="text" name="sucursal" required placeholder="Dirección sucursal" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c33">
            <div class="fl">TEL. FIJO:</div>
            <div class="fv"><input type="tel" name="telefono" required value="<?= htmlspecialchars($temp_data['phone'] ?? '') ?>" placeholder="Fijo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CELULAR:</div>
            <div class="fv"><input type="tel" name="celular" required placeholder="Celular" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CORREO:</div>
            <div class="fv"><input type="email" name="email" required value="<?= htmlspecialchars($temp_data['email'] ?? '') ?>" placeholder="correo@ejemplo.com" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">ASESOR / VENDEDOR:</div>
            <div class="fv"><input type="text" name="nombre_vendedor" required placeholder="Nombre del asesor" value="<?= htmlspecialchars($asesor_nombre ?? '') ?>" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CÓD. VENDEDOR:</div>
            <div class="fv"><input type="text" name="codigo_vendedor" required placeholder="Código" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">LISTA PRECIOS:</div>
            <div class="fv"><input type="text" name="lista_precios" required placeholder="Lista" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">ACTIVIDAD ECON.:</div>
            <div class="fv"><select name="codigo_ciiu" required id="codigoCiiu_cj" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"><option value="">Seleccione...</option></select></div>
        </div>
        <div class="fr c1">
            <div class="fl">FORMA PAGO:</div>
            <div class="fv">
                <label><input type="radio" name="forma_pago" value="contado" required> Contado</label>
                <label><input type="radio" name="forma_pago" value="credito"> Crédito</label>
            </div>
        </div>
    </div>
</div>

<!-- REPRESENTANTE LEGAL -->
<div class="form-section">
    <div class="section-title">DATOS DEL REPRESENTANTE LEGAL</div>
    <div class="section-content">
        <div class="fr c33">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><input type="text" name="representante_nombre" required placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TIPO DOC:</div>
            <div class="fv">
                <select name="representante_tipo_doc" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')">
                    <option value="">--</option><option value="CC">CC</option><option value="CE">CE</option><option value="PA">PA</option>
                </select>
            </div>
            <div class="fl">NÚMERO:</div>
            <div class="fv"><input type="text" name="representante_documento" required placeholder="Número" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c33">
            <div class="fl">PROFESIÓN:</div>
            <div class="fv"><input type="text" name="representante_profesion" required placeholder="Profesión" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">F. NACIMIENTO:</div>
            <div class="fv"><input type="date" name="representante_nacimiento" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><input type="tel" name="representante_telefono" required placeholder="Teléfono" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">CORREO:</div>
            <div class="fv"><input type="email" name="representante_email" required placeholder="correo@ejemplo.com" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
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
            <div class="fl">INGRESOS $</div>
            <div class="fv"><input type="number" name="ingresos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">PASIVO $</div>
            <div class="fv"><input type="number" name="pasivos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">GASTOS $</div>
            <div class="fv"><input type="number" name="gastos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">PATRIMONIO $</div>
            <div class="fv"><input type="number" name="patrimonio" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">OTROS INGRESOS $</div>
            <div class="fv"><input type="number" name="otros_ingresos" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- COMPOSICIÓN ACCIONARIA -->
<div class="form-section">
    <div class="section-title">COMPOSICIÓN ACCIONARIA (Accionistas con participación mayor al 5%)</div>
    <div class="section-content">
        <table class="field-table" id="accionistasTable_cj">
            <thead>
                <tr>
                    <td class="field-label" style="width:35%;">NOMBRE / RAZÓN SOCIAL</td>
                    <td class="field-label" style="width:20%;">DOCUMENTO</td>
                    <td class="field-label" style="width:15%;">% PARTICIPACIÓN</td>
                    <td class="field-label" style="width:20%;">NACIONALIDAD</td>
                    <td class="field-label" style="width:10%;"></td>
                </tr>
            </thead>
            <tbody id="accionistasBody_cj">
                <tr>
                    <td><input type="text" name="accionista_nombre[]" required placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td><input type="text" name="accionista_documento[]" required placeholder="CC/NIT" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td><input type="number" name="accionista_participacion[]" required step="0.01" min="5" max="100" placeholder="5.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td><input type="text" name="accionista_nacionalidad[]" required placeholder="País" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td style="text-align:center;"><button type="button" onclick="removeAcc_cj(this)" style="font-size:10px; padding:2px 8px; background:#ef4444; color:white; border:none; border-radius:3px; cursor:pointer;">Quitar</button></td>
                </tr>
            </tbody>
        </table>
        <div style="padding:8px; text-align:center;">
            <button type="button" onclick="addAcc_cj()" style="font-size:11px; padding:6px 16px; background:#10b981; color:white; border:none; border-radius:4px; cursor:pointer;">+ Agregar Accionista</button>
        </div>
    </div>
</div>

<!-- DATOS TRIBUTARIOS -->
<div class="form-section">
    <div class="section-title">DATOS TRIBUTARIOS</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">TIPO DE CONTRIBUYENTE:</div>
            <div class="fv">
                <label><input type="radio" name="tipo_contribuyente" value="persona_juridica" required> Persona Jurídica</label>
                <label><input type="radio" name="tipo_contribuyente" value="gran_contribuyente"> Gran Contribuyente</label>
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">RÉGIMEN:</div>
            <div class="fv">
                <label><input type="radio" name="regimen_tributario" value="especial" required> Régimen Tributario Especial</label>
                <label><input type="radio" name="regimen_tributario" value="ordinario"> Régimen Ordinario</label>
            </div>
        </div>
    </div>
</div>

<!-- AUTORIZACIÓN PARA CONSULTAS -->
<div class="form-section">
    <div class="section-title">AUTORIZACIÓN PARA CONSULTAS</div>
    <div class="section-content">
        <div class="fr cfull">
            <div class="fv" style="border-right:none; flex-wrap:wrap; gap:12px; padding:12px 14px;">
                <span style="font-size:12px; color:var(--text-primary);">Autorizo consulta en listas restrictivas:</span>
                <label><input type="radio" name="autoriza_centrales_riesgo" value="si" required checked> SÍ</label>
                <label><input type="radio" name="autoriza_centrales_riesgo" value="no"> NO</label>
                <span style="color:var(--border-secondary); margin:0 4px;">|</span>
                <span style="font-size:12px; color:var(--text-primary);">Centrales de Riesgo:</span>
                <label><input type="radio" name="autoriza_centrales" value="si" required checked> SÍ</label>
                <label><input type="radio" name="autoriza_centrales" value="no"> NO</label>
            </div>
        </div>
        <div class="info-box" style="margin:0; border-radius:0;">
            <strong>AVISO:</strong> Autorizo a Pollo Fiesta S.A. (NIT 860.032.450-9) para tratamiento de datos según Ley 1581/2012. Política en www.pollo-fiesta.com
        </div>
    </div>
</div>

<!-- FIRMA -->
<div class="form-section">
    <div class="section-content">
        <div class="fr cfull">
            <div class="fv" style="border-right:none; padding:12px; font-size:13px; color:var(--text-muted); font-style:italic;">
                Declaro que la información corresponde con la realidad y responderé por cualquier perjuicio derivado de inexactitud de datos.
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">FIRMA:</div>
            <div class="fv" style="flex-direction:column; align-items:flex-start; gap:8px; padding:12px;">
                <input type="hidden" id="signature_cj" name="descripcion_firma" required oninvalid="this.setCustomValidity('Firma requerida')" oninput="this.setCustomValidity('')">
                <img id="sig_cj_preview" src="" alt="Firma" style="display:none; max-width:300px; max-height:80px; border:1px solid var(--border-secondary); padding:5px; border-radius:4px; background:white;">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="button" id="sig_cj_add" class="btn btn-primary" onclick="sigCJ('open')" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:8px 14px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path></svg>
                        Agregar Firma
                    </button>
                    <button type="button" id="sig_cj_change" class="btn btn-secondary" onclick="sigCJ('open')" style="display:none; font-size:12px; padding:8px 14px;">Cambiar</button>
                    <button type="button" id="sig_cj_clear" class="btn btn-secondary" onclick="sigCJ('clear')" style="display:none; font-size:12px; padding:8px 14px;">Limpiar</button>
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
        <div class="fr c1"><div class="fl">LISTAS NACIONALES:</div><div class="fv"><label><input type="radio" name="consulta_listas_nacionales" value="negativa"> NEGATIVA</label><label><input type="radio" name="consulta_listas_nacionales" value="positiva"> POSITIVA</label></div></div>
        <div class="fr c1"><div class="fl">RECIBE:</div><div class="fv"><input type="text" name="recibe"></div></div>
        <div class="fr c1"><div class="fl">FIRMA OFICIAL:</div><div class="fv"><input type="hidden" name="firma_oficial_data"></div></div>
        <div class="fr c22"><div class="fl">DIRECTOR DE CARTERA:</div><div class="fv"><input type="text" name="director_cartera"></div><div class="fl">GERENCIA COMERCIAL:</div><div class="fv"><input type="text" name="gerencia_comercial"></div></div>
    </div>
</div>
<?php endif; ?>
<?php if (false): ?>
<div class="form-section"><div class="section-title">OBSERVACIONES</div><div class="section-content"><div class="fr cfull"><div class="fv" style="border-right:none; padding:12px; align-items:stretch;"><textarea name="observaciones" rows="3" style="width:100%;"></textarea></div></div></div></div>
<?php endif; ?>

<script>
let _sigModalCJ = null;
document.addEventListener('DOMContentLoaded', () => {
    _sigModalCJ = new SignatureModal({ modalId: 'sigModalCJ', onSave: (d) => {
        document.getElementById('signature_cj').value = d;
        const p = document.getElementById('sig_cj_preview'); p.src = d; p.style.display = 'block';
        document.getElementById('sig_cj_add').style.display = 'none';
        document.getElementById('sig_cj_change').style.display = 'inline-flex';
        document.getElementById('sig_cj_clear').style.display = 'inline-flex';
    }});
    fetch('<?= $_ENV['APP_URL'] ?>/api/actividades-economicas.php')
        .then(r => r.json()).then(data => {
            const s = document.getElementById('codigoCiiu_cj');
            data.forEach(a => { const o = document.createElement('option'); o.value = a.codigo; o.textContent = `${a.codigo} - ${a.descripcion}`; s.appendChild(o); });
        }).catch(() => {});
});
function sigCJ(action) {
    if (action === 'open' && _sigModalCJ) { _sigModalCJ.open(); return; }
    document.getElementById('signature_cj').value = '';
    document.getElementById('sig_cj_preview').style.display = 'none';
    document.getElementById('sig_cj_add').style.display = 'inline-flex';
    document.getElementById('sig_cj_change').style.display = 'none';
    document.getElementById('sig_cj_clear').style.display = 'none';
}
function addAcc_cj() {
    const tbody = document.getElementById('accionistasBody_cj');
    const row = document.createElement('tr');
    row.innerHTML = `<td><input type="text" name="accionista_nombre[]" required placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td><input type="text" name="accionista_documento[]" required placeholder="CC/NIT" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td><input type="number" name="accionista_participacion[]" required step="0.01" min="5" max="100" placeholder="5.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td><input type="text" name="accionista_nacionalidad[]" required placeholder="País" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td style="text-align:center;"><button type="button" onclick="removeAcc_cj(this)" style="font-size:10px; padding:2px 8px; background:#ef4444; color:white; border:none; border-radius:3px; cursor:pointer;">Quitar</button></td>`;
    tbody.appendChild(row);
}
function removeAcc_cj(btn) {
    const tbody = document.getElementById('accionistasBody_cj');
    if (tbody.children.length > 1) btn.closest('tr').remove();
    else alert('Debe haber al menos un accionista');
}
</script>
