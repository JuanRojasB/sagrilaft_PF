<?php require_once __DIR__ . '/_helpers.php'; ?>

<!-- ESPACIO CARTERA -->
<div class="form-section">
    <div class="section-title">ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE CARTERA</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">VINCULACIÓN:</div>
            <div class="fv"><?= rv($form,'vinculacion') ?></div>
            <div class="fl">ACTUALIZACIÓN:</div>
            <div class="fv"><?= rv($form,'actualizacion') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">FECHA DE VINCULACIÓN:</div>
            <div class="fv"><?= rv_date($form,'fecha_vinculacion') ?></div>
        </div>
    </div>
</div>

<!-- DATOS GENERALES -->
<div class="form-section">
    <div class="section-title">DATOS GENERALES</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">PRINCIPAL:</div>
            <div class="fv"><?= rv($form,'principal') ?></div>
            <div class="fl">SUCURSAL:</div>
            <div class="fv"><?= rv($form,'sucursal') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><?= rv($form,'company_name') ?></div>
            <div class="fl">CÉDULA:</div>
            <div class="fv"><?= rv($form,'nit') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">ESTABLECIMIENTO:</div>
            <div class="fv"><?= rv($form,'nombre_establecimiento') ?></div>
        </div>
        <div class="fr dir-row">
            <div class="fl">DIRECCIÓN:</div>
            <div class="fv"><?= rv($form,'address') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CIUDAD:</div>
            <div class="fv"><?= rv($form,'ciudad') ?></div>
            <div class="fl">BARRIO:</div>
            <div class="fv"><?= rv($form,'barrio') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">LOCALIDAD:</div>
            <div class="fv"><?= rv($form,'localidad') ?></div>
            <div class="fl">LISTA PRECIOS:</div>
            <div class="fv"><?= rv($form,'lista_precios') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CÓD. VENDEDOR:</div>
            <div class="fv"><?= rv($form,'codigo_vendedor') ?></div>
            <div class="fl">TEL. FIJO:</div>
            <div class="fv"><?= rv($form,'telefono_fijo') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CELULAR:</div>
            <div class="fv"><?= rv($form,'phone') ?></div>
            <div class="fl">CORREO:</div>
            <div class="fv"><?= rv($form,'email') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">ACTIVIDAD ECONÓMICA:</div>
            <div class="fv"><?= rv($form,'codigo_ciiu') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">FORMA PAGO:</div>
            <div class="fv"><?= rv($form,'forma_pago') ?></div>
            <div class="fl">F. NACIMIENTO:</div>
            <div class="fv"><?= rv_date($form,'fecha_nacimiento') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">VENDEDOR:</div>
            <div class="fv"><?= rv($form,'nombre_vendedor') ?></div>
            <div class="fl">CLASE CLIENTE:</div>
            <div class="fv"><?= rv($form,'clase_cliente') ?></div>
        </div>
    </div>
</div>

<!-- INFORMACIÓN FINANCIERA -->
<div class="form-section">
    <div class="section-title">INFORMACIÓN FINANCIERA</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">ACTIVOS $</div>
            <div class="fv"><?= rv_money($form,'activos') ?></div>
            <div class="fl">INGRESOS $</div>
            <div class="fv"><?= rv_money($form,'ingresos') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">PASIVO $</div>
            <div class="fv"><?= rv_money($form,'pasivos') ?></div>
            <div class="fl">GASTOS $</div>
            <div class="fv"><?= rv_money($form,'gastos') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">PATRIMONIO $</div>
            <div class="fv"><?= rv_money($form,'patrimonio') ?></div>
            <div class="fl">OTROS INGRESOS $</div>
            <div class="fv"><?= rv_money($form,'otros_ingresos') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">DETALLE OTROS INGRESOS:</div>
            <div class="fv"><?= rv($form,'detalle_otros_ingresos') ?></div>
        </div>
    </div>
</div>

<!-- DATOS TRIBUTARIOS -->
<div class="form-section">
    <div class="section-title">DATOS TRIBUTARIOS</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">TIPO CONTRIBUYENTE:</div>
            <div class="fv"><?= rv($form,'tipo_contribuyente') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">RÉGIMEN:</div>
            <div class="fv"><?= rv($form,'regimen_tributario') ?></div>
        </div>
    </div>
</div>

<!-- AUTORIZACIÓN PARA CONSULTAS -->
<div class="form-section">
    <div class="section-title">AUTORIZACIÓN PARA CONSULTAS</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">LISTAS RESTRICTIVAS:</div>
            <div class="fv"><?= rv_bool($form,'autoriza_centrales_riesgo') ?></div>
            <div class="fl">CENTRALES DE RIESGO:</div>
            <div class="fv"><?= rv_bool($form,'autoriza_centrales') ?></div>
        </div>
    </div>
</div>

<?php rv_observaciones($form); ?>
