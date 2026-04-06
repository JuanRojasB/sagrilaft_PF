<?php require_once __DIR__ . '/_helpers.php'; ?>

<!-- ESPACIO COMPRAS -->
<div class="form-section">
    <div class="section-title">ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE COMPRAS</div>
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

<!-- DATOS DEL PROVEEDOR -->
<div class="form-section">
    <div class="section-title">DATOS DEL PROVEEDOR</div>
    <div class="section-content">
        <div class="fr c322">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><?= rv($form,'company_name') ?></div>
            <div class="fl">CÉDULA:</div>
            <div class="fv"><?= rv($form,'nit') ?></div>
            <div class="fl">RUT:</div>
            <div class="fv"><?= rv($form,'rut') ?></div>
        </div>
        <div class="fr dir-row">
            <div class="fl">DIRECCIÓN:</div>
            <div class="fv"><?= rv($form,'address') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CIUDAD:</div>
            <div class="fv"><?= rv($form,'ciudad') ?></div>
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><?= rv($form,'phone') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">FAX:</div>
            <div class="fv"><?= rv($form,'fax') ?></div>
            <div class="fl">CORREO:</div>
            <div class="fv"><?= rv($form,'email') ?></div>
        </div>
    </div>
</div>

<!-- ACTIVIDAD ECONÓMICA -->
<div class="form-section">
    <div class="section-title">ACTIVIDAD ECONÓMICA PRINCIPAL</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">ACTIVIDAD:</div>
            <div class="fv"><?= rv($form,'codigo_ciiu') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">OBJETO SOCIAL:</div>
            <div class="fv"><?= rv($form,'objeto_social') ?></div>
        </div>
    </div>
</div>

<!-- DECLARACIÓN ORIGEN DE FONDOS -->
<div class="form-section">
    <div class="section-title">DECLARACIÓN ORIGEN DE LOS FONDOS QUE PERCIBE</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><?= rv($form,'representante_nombre') ?></div>
            <div class="fl">DOCUMENTO:</div>
            <div class="fv"><?= rv($form,'representante_documento') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">TIPO DOC:</div>
            <div class="fv"><?= rv($form,'representante_tipo_doc') ?></div>
            <div class="fl">PROFESIÓN:</div>
            <div class="fv"><?= rv($form,'representante_profesion') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">NACIMIENTO:</div>
            <div class="fv"><?= rv($form,'representante_lugar_nacimiento') ?></div>
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><?= rv($form,'representante_telefono') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">RESIDENCIA:</div>
            <div class="fv"><?= rv($form,'representante_residencia') ?></div>
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
            <div class="fl">PASIVO $</div>
            <div class="fv"><?= rv_money($form,'pasivos') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">PATRIMONIO $</div>
            <div class="fv"><?= rv_money($form,'patrimonio') ?></div>
            <div class="fl">INGRESOS $</div>
            <div class="fv"><?= rv_money($form,'ingresos') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">GASTOS $</div>
            <div class="fv"><?= rv_money($form,'gastos') ?></div>
            <div class="fl">OTROS ING. $</div>
            <div class="fv"><?= rv_money($form,'otros_ingresos') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">DETALLE OTROS INGRESOS:</div>
            <div class="fv"><?= rv($form,'detalle_otros_ingresos') ?></div>
        </div>
    </div>
</div>

<!-- CERTIFICACIONES Y AUTORIZACIÓN -->
<div class="form-section">
    <div class="section-title">CERTIFICACIONES Y AUTORIZACIÓN</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">CERTIFICACIÓN:</div>
            <div class="fv"><?= rv_bool($form,'tiene_certificacion') ?></div>
            <div class="fl">CUÁL:</div>
            <div class="fv"><?= rv($form,'cual_certificacion') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">AUTORIZA CENTRALES:</div>
            <div class="fv"><?= rv_bool($form,'autoriza_centrales') ?></div>
        </div>
    </div>
</div>

<?php rv_observaciones($form); ?>
