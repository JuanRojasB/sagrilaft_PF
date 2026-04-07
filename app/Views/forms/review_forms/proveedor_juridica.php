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
    <div class="section-title">DATOS GENERALES DE LA EMPRESA</div>
    <div class="section-content">
        <div class="fr c22">
            <div class="fl">RAZÓN SOCIAL:</div>
            <div class="fv"><?= rv($form,'company_name') ?></div>
            <div class="fl">NIT:</div>
            <div class="fv"><?= rv($form,'nit') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">D.V.:</div>
            <div class="fv"><?= rv($form,'dv') ?></div>
            <div class="fl">RUT:</div>
            <div class="fv"><?= rv($form,'rut') ?></div>
        </div>
        <div class="fr c33">
            <div class="fl">TEL. FIJO:</div>
            <div class="fv"><?= rv($form,'phone') ?></div>
            <div class="fl">FAX:</div>
            <div class="fv"><?= rv($form,'fax') ?></div>
            <div class="fl">CORREO:</div>
            <div class="fv"><?= rv($form,'email') ?></div>
        </div>
        <div class="fr dir-row">
            <div class="fl">DIRECCIÓN:</div>
            <div class="fv"><?= rv($form,'address') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">CIUDAD:</div>
            <div class="fv"><?= rv($form,'ciudad') ?></div>
            <div class="fl">TIPO COMPAÑÍA:</div>
            <div class="fv"><?= rv($form,'tipo_compania') ?></div>
        </div>
    </div>
</div>

<!-- ACTIVIDAD ECONÓMICA -->
<div class="form-section">
    <div class="section-title">ACTIVIDAD ECONÓMICA PRINCIPAL DE LA EMPRESA</div>
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

<!-- REPRESENTANTE LEGAL -->
<div class="form-section">
    <div class="section-title">DATOS DEL REPRESENTANTE LEGAL Y/O APODERADO</div>
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
                    <td class="field-label" style="width:50%;">NOMBRE / RAZÓN SOCIAL</td>
                    <td class="field-label" style="width:25%;">C.C</td>
                    <td class="field-label" style="width:25%;">C.E</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accionistas as $acc): ?>
                <tr>
                    <td><?= htmlspecialchars($acc['nombre'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($acc['cc'] ?? $acc['documento'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($acc['ce'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

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

<!-- CERTIFICACIONES Y DATOS TRIBUTARIOS -->
<div class="form-section">
    <div class="section-title">CERTIFICACIONES Y DATOS TRIBUTARIOS</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">CERTIFICACIÓN:</div>
            <div class="fv"><?= rv($form,'certificacion') ?></div>
        </div>
        <div class="fr c22">
            <div class="fl">TIPO CONTRIBUYENTE:</div>
            <div class="fv"><?= rv($form,'tipo_contribuyente') ?></div>
            <div class="fl">RÉGIMEN:</div>
            <div class="fv"><?= rv($form,'regimen_tributario') ?></div>
        </div>
    </div>
</div>

<!-- AUTORIZACIÓN -->
<div class="form-section">
    <div class="section-title">AUTORIZACIÓN Y FIRMA DEL REPRESENTANTE LEGAL</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">AUTORIZA CENTRALES:</div>
            <div class="fv"><?= rv_bool($form,'autoriza_centrales') ?></div>
        </div>
    </div>
</div>

<!-- FIRMA REPRESENTANTE LEGAL -->
<?php if (!empty($form['firma_representante_data']) || !empty($form['firma_data'])): ?>
<div class="form-section">
    <div class="section-title">FIRMA DEL REPRESENTANTE LEGAL</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">FIRMA:</div>
            <div class="fv"><?= rv_signature($form, !empty($form['firma_representante_data']) ? 'firma_representante_data' : 'firma_data') ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php rv_observaciones($form); ?>
