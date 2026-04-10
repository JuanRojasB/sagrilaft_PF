<!-- FD-08: CREACIÓN DE CLIENTES - PERSONA NATURAL -->

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
            <div class="fv"><input type="text" name="sucursal" required placeholder="Datos sucursal" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><input type="text" name="nombre_cliente" required value="<?= htmlspecialchars($temp_data['company_name'] ?? '') ?>" placeholder="Nombre completo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CÉDULA:</div>
            <div class="fv"><input type="text" name="cc" required value="<?= htmlspecialchars($temp_data['document_number'] ?? '') ?>" placeholder="Número" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">ESTABLECIMIENTO:</div>
            <div class="fv"><input type="text" name="nombre_establecimiento" required placeholder="Nombre del establecimiento" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr dir-row">
            <div class="fl">DIRECCIÓN:</div>
            <div class="fv"><input type="text" id="direccion-cliente-natural" name="direccion" required placeholder="Dirección completa" autocomplete="off" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">CIUDAD:</div>
            <div class="fv"><input type="text" id="ciudad-cliente-natural" name="ciudad" required placeholder="Ciudad" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">BARRIO:</div>
            <div class="fv"><input type="text" name="barrio" required placeholder="Barrio" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">LOCALIDAD:</div>
            <div class="fv"><input type="text" name="localidad" required placeholder="Localidad" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">LISTA PRECIOS:</div>
            <div class="fv"><input type="text" name="lista_precios" required placeholder="Lista" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">CÓD. VENDEDOR:</div>
            <div class="fv"><input type="text" name="codigo_vendedor" required placeholder="Código" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">TEL. FIJO:</div>
            <div class="fv"><input type="tel" name="telefono_fijo" required placeholder="Fijo" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c22">
            <div class="fl">CELULAR:</div>
            <div class="fv"><input type="tel" name="celular" required value="<?= htmlspecialchars($temp_data['phone'] ?? '') ?>" placeholder="Celular" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CORREO:</div>
            <div class="fv"><input type="email" name="email" required value="<?= htmlspecialchars($temp_data['email'] ?? '') ?>" placeholder="correo@ejemplo.com" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c1">
            <div class="fl">ACTIVIDAD ECONÓMICA:</div>
            <div class="fv"><select name="codigo_ciiu" required id="codigoCiiu_cn" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"><option value="">Seleccione...</option></select></div>
        </div>
        <div class="fr c22">
            <div class="fl">FORMA PAGO:</div>
            <div class="fv">
                <label><input type="radio" name="forma_pago" value="contado" required> Contado</label>
                <label><input type="radio" name="forma_pago" value="credito"> Crédito</label>
            </div>
            <div class="fl">F. NACIMIENTO:</div>
            <div class="fv"><input type="date" name="fecha_nacimiento" required id="fechaNac_cn" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr c3fx">
            <div class="fl">MES / DÍA:</div>
            <div class="fv" style="gap:6px;">
                <input type="text" name="mes_nacimiento" id="mesNac_cn" readonly placeholder="Mes" style="max-width:130px;">
                <input type="text" name="dia_nacimiento" id="diaNac_cn" readonly placeholder="Día" style="max-width:70px;">
            </div>
            <div class="fl">VENDEDOR:</div>
            <div class="fv"><input type="text" name="nombre_vendedor" required placeholder="Nombre" value="<?= htmlspecialchars($asesor_nombre ?? '') ?>" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
            <div class="fl">CLASE CLIENTE:</div>
            <div class="fv"><input type="text" name="clase_cliente" required placeholder="Clase" oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></div>
        </div>
        <div class="fr cfull">
            <div class="fv" style="border-right:none; padding:12px; font-size:13px; color:var(--text-muted); font-style:italic;">
                Declaro que la información corresponde con la realidad y responderé por cualquier perjuicio derivado de inexactitud de datos.
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">FIRMA:</div>
            <div class="fv" style="flex-direction:column; align-items:flex-start; gap:8px; padding:12px;">
                <input type="hidden" id="signature_cn" name="descripcion_firma" required oninvalid="this.setCustomValidity('Firma requerida')" oninput="this.setCustomValidity('')">
                <img id="sig_cn_preview" src="" alt="Firma" style="display:none; max-width:300px; max-height:80px; border:1px solid var(--border-secondary); padding:5px; border-radius:4px; background:white;">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="button" id="sig_cn_add" class="btn btn-primary" onclick="sigCN('open')" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:8px 14px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path></svg>
                        Agregar Firma
                    </button>
                    <button type="button" id="sig_cn_change" class="btn btn-secondary" onclick="sigCN('open')" style="display:none; font-size:12px; padding:8px 14px;">Cambiar</button>
                    <button type="button" id="sig_cn_clear" class="btn btn-secondary" onclick="sigCN('clear')" style="display:none; font-size:12px; padding:8px 14px;">Limpiar</button>
                </div>
            </div>
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
        <div class="fr c1">
            <div class="fl">DETALLE OTROS INGRESOS:</div>
            <div class="fv" style="align-items:stretch;"><textarea name="detalle_otros_ingresos" required rows="2" placeholder="Describa..." oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')"></textarea></div>
        </div>
    </div>
</div>

<!-- DATOS TRIBUTARIOS -->
<div class="form-section">
    <div class="section-title">DATOS TRIBUTARIOS</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">TIPO CONTRIBUYENTE EN RENTA:</div>
            <div class="fv">
                <label><input type="radio" name="tipo_contribuyente" value="persona_juridica" required> Persona Jurídica</label>
                <label><input type="radio" name="tipo_contribuyente" value="gran_contribuyente"> Gran Contribuyente</label>
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">RÉGIMEN:</div>
            <div class="fv">
                <label><input type="radio" name="regimen_tributario" value="especial" required> Régimen Tributario Especial</label>
                <label><input type="radio" name="regimen_tributario" value="no_contribuyente"> No Contribuyente</label>
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

<?php if (false): ?>
<!-- ESPACIO EXCLUSIVO POLLO FIESTA — lo llena Angie -->
<div class="form-section internal-section">
    <div class="section-title">ESPACIO EXCLUSIVO PARA POLLO FIESTA</div>
    <div class="section-content">
        <div class="fr c1"><div class="fl">CONSULTA OFAC:</div><div class="fv"><label><input type="radio" name="consulta_ofac" value="negativa"> NEGATIVA</label><label><input type="radio" name="consulta_ofac" value="positiva"> POSITIVA</label></div></div>
        <div class="fr c1"><div class="fl">LISTAS NACIONALES:</div><div class="fv"><label><input type="radio" name="consulta_listas_nacionales" value="negativa"> NEGATIVA</label><label><input type="radio" name="consulta_listas_nacionales" value="positiva"> POSITIVA</label></div></div>
        <div class="fr c1"><div class="fl">RECIBE:</div><div class="fv"><input type="text" name="recibe" placeholder="Nombre de quien recibe"></div></div>
        <div class="fr c1"><div class="fl">FIRMA OFICIAL:</div><div class="fv"><input type="hidden" name="firma_oficial_data"></div></div>
        <div class="fr c22"><div class="fl">DIRECTOR DE CARTERA:</div><div class="fv"><input type="text" name="director_cartera"></div><div class="fl">GERENCIA COMERCIAL:</div><div class="fv"><input type="text" name="gerencia_comercial"></div></div>
    </div>
</div>
<?php endif; ?>
<?php if (false): ?>
<!-- OBSERVACIONES — lo llena Angie -->
<div class="form-section"><div class="section-title">OBSERVACIONES</div><div class="section-content"><div class="fr cfull"><div class="fv" style="border-right:none; padding:12px; align-items:stretch;"><textarea name="observaciones" rows="3" style="width:100%;"></textarea></div></div></div></div>
<?php endif; ?>

<script>
let _sigModalCN = null;
document.addEventListener('DOMContentLoaded', () => {
    fetch('<?= $_ENV['APP_URL'] ?>/api/actividades-economicas.php')
        .then(r => r.json()).then(data => {
            const s = document.getElementById('codigoCiiu_cn');
            data.forEach(a => { const o = document.createElement('option'); o.value = a.codigo; o.textContent = `${a.codigo} - ${a.descripcion}`; s.appendChild(o); });
        }).catch(() => {});

    const fn = document.getElementById('fechaNac_cn');
    if (fn) fn.addEventListener('change', function() {
        if (!this.value) return;
        const d = new Date(this.value + 'T00:00:00');
        const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        document.getElementById('mesNac_cn').value = meses[d.getMonth()];
        document.getElementById('diaNac_cn').value = d.getDate();
    });

    // Inicializar modal de firma
    _sigModalCN = new SignatureModal({ modalId: 'sigModalCN', onSave: (d) => {
        document.getElementById('signature_cn').value = d;
        const p = document.getElementById('sig_cn_preview'); p.src = d; p.style.display = 'block';
        document.getElementById('sig_cn_add').style.display = 'none';
        document.getElementById('sig_cn_change').style.display = 'inline-flex';
        document.getElementById('sig_cn_clear').style.display = 'inline-flex';
    }});

    // Botón de auto-llenado para pruebas
    const autoFillBtn = document.createElement('button');
    autoFillBtn.type = 'button';
    autoFillBtn.textContent = '🧪 Auto-llenar para Prueba';
    autoFillBtn.style.cssText = 'position:fixed;top:10px;right:10px;z-index:9999;background:#10b981;color:white;border:none;padding:10px 20px;border-radius:6px;font-weight:700;cursor:pointer;box-shadow:0 4px 6px rgba(0,0,0,0.1);';
    autoFillBtn.onclick = function() {
        // Espacio Cartera
        document.querySelector('[name="vinculacion"]').value = 'Nueva';
        document.querySelector('[name="actualizacion"]').value = 'Primera vez';
        document.querySelector('[name="fecha_vinculacion"]').value = '2026-04-07';
        
        // Datos Generales
        document.querySelector('[name="principal"]').value = 'Sede Principal';
        document.querySelector('[name="sucursal"]').value = 'Sucursal Centro';
        document.querySelector('[name="nombre_cliente"]').value = 'Carlos Alberto Rodríguez Pérez';
        document.querySelector('[name="cc"]').value = '1234567890';
        document.querySelector('[name="nombre_establecimiento"]').value = 'Restaurante El Buen Sabor';
        document.querySelector('[name="direccion"]').value = 'Calle 45 # 23-67';
        document.querySelector('[name="ciudad"]').value = 'Bogotá';
        document.querySelector('[name="barrio"]').value = 'Chapinero';
        document.querySelector('[name="localidad"]').value = 'Localidad 2';
        document.querySelector('[name="lista_precios"]').value = 'Lista A';
        document.querySelector('[name="codigo_vendedor"]').value = 'V001';
        document.querySelector('[name="telefono_fijo"]').value = '6012345678';
        document.querySelector('[name="celular"]').value = '3101234567';
        document.querySelector('[name="email"]').value = 'carlos.rodriguez@ejemplo.com';
        
        // Actividad Económica
        const ciiu = document.querySelector('[name="codigo_ciiu"]');
        if (ciiu.options.length > 1) ciiu.selectedIndex = 1;
        
        // Forma de Pago
        const contado = document.querySelector('[name="forma_pago"][value="contado"]');
        if (contado) contado.checked = true;
        
        // Fecha de Nacimiento
        const fechaNac = document.querySelector('[name="fecha_nacimiento"]');
        fechaNac.value = '1985-03-15';
        fechaNac.dispatchEvent(new Event('change'));
        
        document.querySelector('[name="nombre_vendedor"]').value = 'María González';
        document.querySelector('[name="clase_cliente"]').value = 'Premium';
        
        // Información Financiera
        document.querySelector('[name="activos"]').value = '150000000';
        document.querySelector('[name="ingresos"]').value = '80000000';
        document.querySelector('[name="pasivos"]').value = '50000000';
        document.querySelector('[name="gastos"]').value = '60000000';
        document.querySelector('[name="patrimonio"]').value = '100000000';
        document.querySelector('[name="otros_ingresos"]').value = '5000000';
        document.querySelector('[name="detalle_otros_ingresos"]').value = 'Ingresos por arrendamiento de local comercial';
        
        // Datos Tributarios
        const tipoContrib = document.querySelector('[name="tipo_contribuyente"]');
        if (tipoContrib) tipoContrib.value = 'persona_juridica';
        
        const regimen = document.querySelector('[name="regimen_tributario"]');
        if (regimen) regimen.value = 'ordinario';
        
        // Autorizaciones
        const autorizaListas = document.querySelector('[name="autoriza_centrales_riesgo"][value="si"]');
        if (autorizaListas) autorizaListas.checked = true;
        
        const autorizaCentrales = document.querySelector('[name="autoriza_centrales"][value="si"]');
        if (autorizaCentrales) autorizaCentrales.checked = true;
        
        alert('✅ Formulario auto-llenado con datos de prueba!\n\nAhora solo necesitas:\n1. Hacer clic en "Agregar Firma"\n2. Dibujar la firma en el modal\n3. Hacer clic en "Enviar Formulario"');
    };
    document.body.appendChild(autoFillBtn);
});

function sigCN(action) {
    if (action === 'open' && _sigModalCN) { _sigModalCN.open(); return; }
    document.getElementById('signature_cn').value = '';
    document.getElementById('sig_cn_preview').style.display = 'none';
    document.getElementById('sig_cn_add').style.display = 'inline-flex';
    document.getElementById('sig_cn_change').style.display = 'none';
    document.getElementById('sig_cn_clear').style.display = 'none';
}
</script>
