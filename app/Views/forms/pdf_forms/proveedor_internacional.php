<!-- FCO-04: CONOCIMIENTO DE PROVEEDOR INTERNACIONAL -->

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
    <div class="section-title">DATOS GENERALES DEL PROVEEDOR</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">RAZÓN SOCIAL:</div>
            <div class="fv"><input type="text" name="razon_social" required value="<?= htmlspecialchars($temp_data['company_name'] ?? '') ?>" placeholder="Razón social" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">NÚMERO REGISTRO:</div>
            <div class="fv"><input type="text" name="numero_registro" required value="<?= htmlspecialchars($temp_data['document_number'] ?? '') ?>" placeholder="Número de registro o identificación fiscal" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">PAÍS:</div>
            <div class="fv"><input type="text" name="pais" required placeholder="País de origen" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CIUDAD:</div>
            <div class="fv"><input type="text" name="ciudad" required placeholder="Ciudad" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">DIRECCIÓN:</div>
            <div class="fv"><input type="text" id="direccion-proveedor-internacional" name="direccion" required placeholder="Dirección completa" autocomplete="off" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><input type="tel" name="telefono" required value="<?= htmlspecialchars($temp_data['phone'] ?? '') ?>" placeholder="Número con código de país" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">FAX:</div>
            <div class="fv"><input type="tel" name="fax" required placeholder="Número de fax" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">CORREO:</div>
            <div class="fv"><input type="email" name="email" required value="<?= htmlspecialchars($temp_data['email'] ?? '') ?>" placeholder="correo@ejemplo.com" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">SITIO WEB:</div>
            <div class="fv"><input type="url" name="sitio_web" required placeholder="https://www.ejemplo.com" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- ACTIVIDAD ECONÓMICA -->
<div class="form-section">
    <div class="section-title">ACTIVIDAD ECONÓMICA PRINCIPAL</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">ACTIVIDAD ECONÓMICA:</div>
            <div class="fv"><select name="codigo_ciiu" required id="codigoCiiu_pi" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"><option value="">Seleccione la actividad económica...</option></select></div>
        </div>
        <div class="fr c1">
            <div class="fl">OBJETO SOCIAL:</div>
            <div class="fv" style="align-items:stretch;"><textarea name="objeto_social" required rows="3" placeholder="Describa el objeto social o actividad principal..." oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></textarea></div>
        </div>
        <div class="fr c1">
            <div class="fl">PRODUCTOS O SERVICIOS:</div>
            <div class="fv" style="align-items:stretch;"><textarea name="productos_servicios" required rows="2" placeholder="Describa los productos o servicios que ofrece..." oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></textarea></div>
        </div>
    </div>
</div>

<!-- REPRESENTANTE LEGAL -->
<div class="form-section">
    <div class="section-title">DATOS DEL REPRESENTANTE LEGAL</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><input type="text" name="representante_nombre" required placeholder="Nombre completo del representante legal" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">NÚMERO DOCUMENTO:</div>
            <div class="fv"><input type="text" name="representante_documento" required placeholder="Número de documento o pasaporte" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">NACIONALIDAD:</div>
            <div class="fv"><input type="text" name="representante_nacionalidad" required placeholder="País de nacionalidad" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">CARGO:</div>
            <div class="fv"><input type="text" name="representante_cargo" required placeholder="Cargo en la empresa" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><input type="tel" name="representante_telefono" required placeholder="Número de contacto" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">CORREO:</div>
            <div class="fv"><input type="email" name="representante_email" required placeholder="correo@ejemplo.com" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
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
                    <td class="field-label" style="width:40%;">NOMBRE / RAZÓN SOCIAL</td>
                    <td class="field-label" style="width:30%;">DOCUMENTO</td>
                    <td class="field-label" style="width:25%;">NACIONALIDAD</td>
                    <td class="field-label" style="width:5%;"></td>
                </tr>
            </thead>
            <tbody id="accionistasBody">
                <tr>
                    <td><input type="text" name="accionista_nombre[]" required placeholder="Nombre completo o razón social" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td><input type="text" name="accionista_documento[]" required placeholder="Documento" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td><input type="text" name="accionista_nacionalidad[]" required placeholder="País" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td>
                    <td style="text-align:center;"><button type="button" onclick="removeAccionistaPI(this)" style="font-size:10px; padding:2px 6px; background:#ef4444; color:white; border:none; border-radius:3px; cursor:pointer;">Quitar</button></td>
                </tr>
            </tbody>
        </table>
        <div style="padding:6px; text-align:center;">
            <button type="button" onclick="addAccionistaPI()" style="font-size:11px; padding:5px 14px; background:#10b981; color:white; border:none; border-radius:4px; cursor:pointer;">+ Agregar Accionista</button>
        </div>
    </div>
</div>

<!-- INFORMACIÓN FINANCIERA -->
<div class="form-section">
    <div class="section-title">INFORMACIÓN FINANCIERA (En USD)</div>
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
        <div class="fr c1">
            <div class="fl">PATRIMONIO $</div>
            <div class="fv"><input type="number" name="patrimonio" required step="0.01" min="0" placeholder="0.00" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- INFORMACIÓN DE IMPORTACIÓN -->
<div class="form-section">
    <div class="section-title">INFORMACIÓN DE IMPORTACIÓN</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">INCOTERM:</div>
            <div class="fv">
                <select name="incoterm" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')" style="width:100%;">
                    <option value="">Seleccione...</option>
                    <option value="EXW">EXW - Ex Works</option>
                    <option value="FCA">FCA - Free Carrier</option>
                    <option value="CPT">CPT - Carriage Paid To</option>
                    <option value="CIP">CIP - Carriage and Insurance Paid</option>
                    <option value="DAP">DAP - Delivered At Place</option>
                    <option value="DPU">DPU - Delivered at Place Unloaded</option>
                    <option value="DDP">DDP - Delivered Duty Paid</option>
                    <option value="FAS">FAS - Free Alongside Ship</option>
                    <option value="FOB">FOB - Free On Board</option>
                    <option value="CFR">CFR - Cost and Freight</option>
                    <option value="CIF">CIF - Cost, Insurance and Freight</option>
                </select>
            </div>
            <div class="fl">FORMA DE PAGO:</div>
            <div class="fv"><input type="text" name="forma_pago_internacional" required placeholder="Carta de crédito, transferencia" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">TIEMPO ENTREGA:</div>
            <div class="fv"><input type="text" name="tiempo_entrega" required placeholder="Días o semanas" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">PUERTO ORIGEN:</div>
            <div class="fv"><input type="text" name="puerto_origen" required placeholder="Puerto de embarque" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">AGENTE ADUANAL:</div>
            <div class="fv"><input type="text" name="agente_aduanal" required placeholder="Nombre del agente aduanal" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
    </div>
</div>

<!-- CERTIFICACIONES -->
<div class="form-section">
    <div class="section-title">CERTIFICACIONES Y DOCUMENTOS</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">CERTIFICACIONES INTERNACIONALES:</div>
            <div class="fv"><input type="text" name="certificaciones" required placeholder="ISO, FDA, CE, etc." oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">CERTIFICADO DE ORIGEN:</div>
            <div class="fv">
                <label><input type="radio" name="certificado_origen" value="si" required> SÍ</label>
                <label><input type="radio" name="certificado_origen" value="no"> NO</label>
            </div>
        </div>
    </div>
</div>

<!-- AUTORIZACIÓN -->
<div class="form-section">
    <div class="section-title">AUTORIZACIÓN</div>
    <div class="section-content">
        <div class="info-box" style="margin:0 0 8px 0;">
            <strong>DECLARACIÓN:</strong> Declaro que la información suministrada en este formulario es veraz y completa. Autorizo a Pollo Fiesta S.A. para verificar la información aquí contenida y realizar las consultas que considere necesarias en listas restrictivas internacionales y bases de datos de cumplimiento.
        </div>
        <div class="fr c22">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><input type="text" name="nombre_firma" required placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CARGO:</div>
            <div class="fv"><input type="text" name="cargo_firma" required placeholder="Cargo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">FIRMA:</div>
            <div class="fv" style="flex-direction:column; align-items:flex-start; gap:8px; padding:12px;">
                <input type="hidden" id="signature_proveedor_pi" name="firma_proveedor">
                <img id="sig_prov_pi_preview" src="" alt="Firma" style="display:none; max-width:300px; max-height:80px; border:1px solid var(--border-secondary); padding:5px; border-radius:4px; background:white;">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="button" id="sig_prov_pi_add" class="btn btn-primary" onclick="sigProvPI('open')" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:8px 14px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path></svg>
                        Agregar Firma
                    </button>
                    <button type="button" id="sig_prov_pi_change" class="btn btn-secondary" onclick="sigProvPI('open')" style="display:none; font-size:12px; padding:8px 14px;">Cambiar</button>
                    <button type="button" id="sig_prov_pi_clear" class="btn btn-secondary" onclick="sigProvPI('clear')" style="display:none; font-size:12px; padding:8px 14px;">Limpiar</button>
                </div>
            </div>
            <div class="fl">FECHA:</div>
            <div class="fv"><input type="date" name="fecha_firma" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
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
        <div class="fr c1"><div class="fl">CONSULTA INTERPOL:</div><div class="fv"><label><input type="radio" name="consulta_interpol" value="negativa"> NEGATIVA</label><label><input type="radio" name="consulta_interpol" value="positiva"> POSITIVA</label></div></div>
        <div class="fr c1"><div class="fl">NOMBRE OFICIAL:</div><div class="fv"><input type="text" name="nombre_oficial"></div></div>
        <div class="fr c1"><div class="fl">FIRMA OFICIAL:</div><div class="fv"><input type="hidden" name="firma_oficial_data"></div></div>
    </div>
</div>
<?php endif; ?>
<?php if (false): ?>
<div class="form-section"><div class="section-title">OBSERVACIONES</div><div class="section-content"><div class="fr cfull"><div class="fv" style="border-right:none; padding:12px; align-items:stretch;"><textarea name="observaciones" rows="4" style="width:100%;"></textarea></div></div></div></div>
<?php endif; ?>

<script>
let _sigProvPI = null, _sigOficPI = null;
document.addEventListener('DOMContentLoaded', () => {
    _sigProvPI = new SignatureModal({ modalId: 'sigModalProvPI', onSave: (d) => {
        document.getElementById('signature_proveedor_pi').value = d;
        const p = document.getElementById('sig_prov_pi_preview'); p.src = d; p.style.display = 'block';
        document.getElementById('sig_prov_pi_add').style.display = 'none';
        document.getElementById('sig_prov_pi_change').style.display = 'inline-flex';
        document.getElementById('sig_prov_pi_clear').style.display = 'inline-flex';
    }});
    _sigOficPI = new SignatureModal({ modalId: 'sigModalOficPI', onSave: (d) => {} });
    fetch('<?= $_ENV['APP_URL'] ?>/api/actividades-economicas.php')
        .then(r => r.json()).then(data => {
            const s = document.getElementById('codigoCiiu_pi');
            data.forEach(a => { const o = document.createElement('option'); o.value = a.codigo; o.textContent = `${a.codigo} - ${a.descripcion}`; s.appendChild(o); });
        }).catch(() => {});
});
function sigProvPI(action) {
    if (action === 'open' && _sigProvPI) { _sigProvPI.open(); return; }
    document.getElementById('signature_proveedor_pi').value = '';
    document.getElementById('sig_prov_pi_preview').style.display = 'none';
    document.getElementById('sig_prov_pi_add').style.display = 'inline-flex';
    document.getElementById('sig_prov_pi_change').style.display = 'none';
    document.getElementById('sig_prov_pi_clear').style.display = 'none';
}
function addAccionistaPI() {
    const tbody = document.getElementById('accionistasBody');
    const row = document.createElement('tr');
    row.innerHTML = `<td><input type="text" name="accionista_nombre[]" required placeholder="Nombre completo o razón social" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td><input type="text" name="accionista_documento[]" required placeholder="Documento" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td><input type="text" name="accionista_nacionalidad[]" required placeholder="País" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></td><td style="text-align:center;"><button type="button" onclick="removeAccionistaPI(this)" style="font-size:10px; padding:2px 6px; background:#ef4444; color:white; border:none; border-radius:3px; cursor:pointer;">Quitar</button></td>`;
    tbody.appendChild(row);
}
function removeAccionistaPI(btn) {
    const tbody = document.getElementById('accionistasBody');
    if (tbody.children.length > 1) btn.closest('tr').remove();
    else alert('Debe haber al menos un accionista');
}
</script>
