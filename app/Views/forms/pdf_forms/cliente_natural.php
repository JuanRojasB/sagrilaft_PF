<!-- FGF-08: CREACIÓN DE CLIENTES - PERSONA NATURAL -->

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
            <div class="fv" style="border-right:none; padding:12px; font-size:10px; color:var(--text-muted); font-style:italic;">
                Declaro que la información corresponde con la realidad y responderé por cualquier perjuicio derivado de inexactitud de datos.
            </div>
        </div>
        <div class="fr c1">
            <div class="fl">FIRMA:</div>
            <div class="fv" style="padding:8px;">
                <input type="hidden" name="descripcion_firma" id="firma_data_cn" required oninvalid="this.setCustomValidity('Firma requerida')" oninput="this.setCustomValidity('')">
                <canvas id="firma_canvas_cn" width="400" height="80" style="border:1px solid #ccc;border-radius:4px;cursor:crosshair;background:#fff;max-width:100%;touch-action:none;"></canvas>
                <div style="margin-top:4px;display:flex;gap:8px;">
                    <button type="button" onclick="clearFirmaCN()" style="font-size:11px;padding:3px 10px;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:3px;cursor:pointer;">Limpiar</button>
                    <span style="font-size:10px;color:#64748b;align-self:center;">Firme con el mouse o dedo</span>
                </div>
            </div>
        </div>
        <script>
        (function(){
            const canvas = document.getElementById('firma_canvas_cn');
            const input  = document.getElementById('firma_data_cn');
            const ctx    = canvas.getContext('2d');
            let drawing  = false;
            function pos(e){ const r=canvas.getBoundingClientRect(); const t=e.touches?e.touches[0]:e; return {x:(t.clientX-r.left)*(canvas.width/r.width),y:(t.clientY-r.top)*(canvas.height/r.height)}; }
            canvas.addEventListener('mousedown',  e=>{drawing=true; ctx.beginPath(); const p=pos(e); ctx.moveTo(p.x,p.y);});
            canvas.addEventListener('mousemove',  e=>{if(!drawing)return; const p=pos(e); ctx.lineTo(p.x,p.y); ctx.stroke();});
            canvas.addEventListener('mouseup',    ()=>{drawing=false; input.value=canvas.toDataURL();});
            canvas.addEventListener('mouseleave', ()=>{drawing=false;});
            canvas.addEventListener('touchstart', e=>{e.preventDefault();drawing=true;ctx.beginPath();const p=pos(e);ctx.moveTo(p.x,p.y);},{passive:false});
            canvas.addEventListener('touchmove',  e=>{e.preventDefault();if(!drawing)return;const p=pos(e);ctx.lineTo(p.x,p.y);ctx.stroke();},{passive:false});
            canvas.addEventListener('touchend',   ()=>{drawing=false;input.value=canvas.toDataURL();});
            ctx.strokeStyle='#1e293b'; ctx.lineWidth=1.5; ctx.lineCap='round';
        })();
        function clearFirmaCN(){ const c=document.getElementById('firma_canvas_cn'); c.getContext('2d').clearRect(0,0,c.width,c.height); document.getElementById('firma_data_cn').value=''; }
        </script>
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
    _sigModalCN = new SignatureModal({ modalId: 'sigModalOficialCN', onSave: (d) => {} });

    fetch('/gestion-sagrilaft/public/api/actividades-economicas.php')
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
});
</script>
