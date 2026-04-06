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

<!-- DATOS GENERALES -->
<div class="form-section">
    <div class="section-title">DATOS GENERALES DEL PROVEEDOR</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">RAZÓN SOCIAL:</div>
            <div class="fv"><?= rv($form,'company_name') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">NÚMERO REGISTRO:</div>
            <div class="fv"><?= rv($form,'nit') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">PAÍS:</div>
            <div class="fv"><?= rv($form,'pais') ?></div>
            <div class="fl">CIUDAD:</div>
            <div class="fv"><?= rv($form,'ciudad') ?></div>
        </div>
        <div class="fr dir-row">
            <div class="fl">DIRECCIÓN:</div>
            <div class="fv"><?= rv($form,'address') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><?= rv($form,'phone') ?></div>
            <div class="fl">FAX:</div>
            <div class="fv"><?= rv($form,'fax') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CORREO:</div>
            <div class="fv"><?= rv($form,'email') ?></div>
            <div class="fl">SITIO WEB:</div>
            <div class="fv"><?= rv($form,'pagina_web') ?></div>
        </div>
    </div>
</div>

<!-- ACTIVIDAD ECONÓMICA -->
<div class="form-section">
    <div class="section-title">ACTIVIDAD ECONÓMICA PRINCIPAL</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">ACTIVIDAD ECONÓMICA:</div>
            <div class="fv"><?= rv($form,'codigo_ciiu') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">OBJETO SOCIAL:</div>
            <div class="fv"><?= rv($form,'objeto_social') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">PRODUCTOS O SERVICIOS:</div>
            <div class="fv"><?= rv($form,'productos_servicios') ?></div>
        </div>
    </div>
</div>

<!-- REPRESENTANTE LEGAL -->
<div class="form-section">
    <div class="section-title">DATOS DEL REPRESENTANTE LEGAL</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><?= rv($form,'representante_nombre') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">NÚMERO DOCUMENTO:</div>
            <div class="fv"><?= rv($form,'representante_documento') ?></div>
            <div class="fl">NACIONALIDAD:</div>
            <div class="fv"><?= rv($form,'representante_nacionalidad') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CARGO:</div>
            <div class="fv"><?= rv($form,'representante_cargo') ?></div>
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><?= rv($form,'representante_telefono') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">CORREO:</div>
            <div class="fv"><?= rv($form,'representante_email') ?></div>
        </div>
    </div>
</div>

<!-- COMPOSICIÓN ACCIONARIA -->
<?php
$accionistas = !empty($form['accionistas']) ? json_decode($form['accionistas'], true) : [];
if (is_array($accionistas) && count($accionistas)):
?>
<div class="form-section">
    <div class="section-title">COMPOSICIÓN ACCIONARIA (Accionistas con participación mayor al 5%)</div>
    <div class="section-content">
        <table class="field-table">
            <thead>
                <tr>
                    <td class="field-label" style="width:40%;">NOMBRE / RAZÓN SOCIAL</td>
                    <td class="field-label" style="width:30%;">DOCUMENTO</td>
                    <td class="field-label" style="width:25%;">NACIONALIDAD</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accionistas as $acc): ?>
                <tr>
                    <td><?= htmlspecialchars($acc['nombre'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($acc['documento'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($acc['nacionalidad'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- INFORMACIÓN FINANCIERA -->
<div class="form-section">
    <div class="section-title">INFORMACIÓN FINANCIERA (En USD)</div>
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
        <div class="fr c1">
            <div class="fl">PATRIMONIO $</div>
            <div class="fv"><?= rv_money($form,'patrimonio') ?></div>
        </div>
    </div>
</div>

<!-- INFORMACIÓN DE IMPORTACIÓN -->
<div class="form-section">
    <div class="section-title">INFORMACIÓN DE IMPORTACIÓN</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">INCOTERM:</div>
            <div class="fv"><?= rv($form,'incoterm') ?></div>
            <div class="fl">FORMA DE PAGO:</div>
            <div class="fv"><?= rv($form,'forma_pago_internacional') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">TIEMPO ENTREGA:</div>
            <div class="fv"><?= rv($form,'tiempo_entrega') ?></div>
            <div class="fl">PUERTO ORIGEN:</div>
            <div class="fv"><?= rv($form,'puerto_origen') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">AGENTE ADUANAL:</div>
            <div class="fv"><?= rv($form,'agente_aduanal') ?></div>
        </div>
    </div>
</div>

<!-- CERTIFICACIONES -->
<div class="form-section">
    <div class="section-title">CERTIFICACIONES Y DOCUMENTOS</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">CERTIFICACIONES:</div>
            <div class="fv"><?= rv($form,'certificaciones') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">CERTIFICADO DE ORIGEN:</div>
            <div class="fv"><?= rv_bool($form,'certificado_origen') ?></div>
        </div>
    </div>
</div>

<?php rv_observaciones($form); ?>
