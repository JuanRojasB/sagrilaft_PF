<?php require_once __DIR__ . '/_helpers.php'; ?>

<!-- DECLARACIÓN DE ORIGEN DE FONDOS -->
<div class="form-section">
    <div class="section-title">DECLARACIÓN DE ORIGEN DE FONDOS</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">NOMBRE DECLARANTE:</div>
            <div class="fv"><?= rv($form,'nombre_declarante') ?></div>
            <div class="fl">TIPO DOC:</div>
            <div class="fv"><?= rv($form,'tipo_documento') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">NÚMERO DOCUMENTO:</div>
            <div class="fv"><?= rv($form,'numero_documento') ?></div>
            <div class="fl">CALIDAD:</div>
            <div class="fv"><?= rv($form,'calidad') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">EMPRESA:</div>
            <div class="fv"><?= rv($form,'empresa') ?></div>
            <div class="fl">NIT / CC EMPRESA:</div>
            <div class="fv"><?= rv($form,'nit_empresa') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">ORIGEN DE RECURSOS:</div>
            <div class="fv" style="align-items:flex-start; padding-top:10px;"><?= rv($form,'origen_recursos') ?></div>
        </div>
    </div>
</div>

<!-- PEP -->
<div class="form-section">
    <div class="section-title">CALIDAD DE PERSONA EXPUESTA POLÍTICAMENTE (PEP)</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">¿ES USTED PEP?</div>
            <div class="fv"><?= rv_bool($form,'es_pep') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">CARGO PEP:</div>
            <div class="fv"><?= rv($form,'cargo_pep') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">PERÍODO EN EL CARGO:</div>
            <div class="fv"><?= rv($form,'periodo_pep') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">¿FAMILIARES PEP?</div>
            <div class="fv"><?= rv_bool($form,'familiar_pep') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">DETALLE FAMILIAR PEP:</div>
            <div class="fv"><?= rv($form,'familiar_pep_detalle') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">¿VÍNCULOS CON PEP?</div>
            <div class="fv"><?= rv_bool($form,'vinculo_pep') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">DETALLE VÍNCULO PEP:</div>
            <div class="fv"><?= rv($form,'vinculo_pep_detalle') ?></div>
        </div>
    </div>
</div>

<!-- INFORMACIÓN FINANCIERA -->
<div class="form-section">
    <div class="section-title">INFORMACIÓN FINANCIERA</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">INGRESOS MENSUALES $</div>
            <div class="fv"><?= rv_money($form,'ingresos_mensuales') ?></div>
            <div class="fl">EGRESOS MENSUALES $</div>
            <div class="fv"><?= rv_money($form,'egresos_mensuales') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">TOTAL ACTIVOS $</div>
            <div class="fv"><?= rv_money($form,'total_activos') ?></div>
            <div class="fl">TOTAL PASIVOS $</div>
            <div class="fv"><?= rv_money($form,'total_pasivos') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">PATRIMONIO NETO $</div>
            <div class="fv"><?= rv_money($form,'patrimonio_neto') ?></div>
        </div>
    </div>
</div>

<!-- OPERACIONES INTERNACIONALES -->
<div class="form-section">
    <div class="section-title">OPERACIONES INTERNACIONALES</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">¿OPERA EN MONEDA EXTRANJERA?</div>
            <div class="fv"><?= rv_bool($form,'opera_moneda_extranjera') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">PAÍSES DE OPERACIÓN:</div>
            <div class="fv"><?= rv($form,'paises_operacion') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">¿CUENTAS EN EL EXTERIOR?</div>
            <div class="fv"><?= rv_bool($form,'cuentas_exterior') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">DETALLE CUENTAS EXTERIOR:</div>
            <div class="fv"><?= rv($form,'cuentas_exterior_detalle') ?></div>
        </div>
    </div>
</div>

<!-- FIRMA -->
<div class="form-section">
    <div class="section-title">DECLARACIÓN Y FIRMA</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">NOMBRE FIRMANTE:</div>
            <div class="fv"><?= rv($form,'nombre_firma_final') ?></div>
            <div class="fl">DOCUMENTO:</div>
            <div class="fv"><?= rv($form,'documento_firma') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">FECHA:</div>
            <div class="fv"><?= rv_date($form,'fecha_declaracion') ?></div>
            <div class="fl">CIUDAD:</div>
            <div class="fv"><?= rv($form,'ciudad_declaracion') ?></div>
        </div>
    </div>
</div>

<?php rv_observaciones($form); ?>
