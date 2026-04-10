<?php require_once __DIR__ . '/_helpers.php'; ?>

<div class="form-section">
    <div class="section-title">INFORMACIÓN DEL EMPLEADO</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><?= rv($form,'empleado_nombre') ?></div>
            <div class="fl">CÉDULA:</div>
            <div class="fv"><?= rv($form,'empleado_cedula') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CARGO:</div>
            <div class="fv"><?= rv($form,'empleado_cargo') ?></div>
            <div class="fl">CELULAR:</div>
            <div class="fv"><?= rv($form,'empleado_celular') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CIUDAD VACANTE:</div>
            <div class="fv"><?= rv($form,'empleado_ciudad_vacante') ?></div>
            <div class="fl">CIUDAD NACIMIENTO:</div>
            <div class="fv"><?= rv($form,'empleado_ciudad_nacimiento') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">FECHA NACIMIENTO:</div>
            <div class="fv"><?= rv_date($form,'empleado_fecha_nacimiento') ?></div>
        </div>
    </div>
</div>
