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
            <div class="fv"><?= rv($form,'sucursal_campo') ?></div>
        </div>
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
        <div class="fr dir-row">
            <div class="fl">DOMICILIO:</div>
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
            <div class="fl">SUCURSAL DIR.:</div>
            <div class="fv"><?= rv($form,'sucursal') ?></div>
        </div>
        <div class="fr c33">
            <div class="fl">TEL. FIJO:</div>
            <div class="fv"><?= rv($form,'phone') ?></div>
            <div class="fl">CELULAR:</div>
            <div class="fv"><?= rv($form,'celular') ?></div>
            <div class="fl">CORREO:</div>
            <div class="fv"><?= rv($form,'email') ?></div>
        </div>
        <div class="fr c33">
            <div class="fl">LISTA PRECIOS:</div>
            <div class="fv"><?= rv($form,'lista_precios') ?></div>
            <div class="fl">CÓD. VENDEDOR:</div>
            <div class="fv"><?= rv($form,'codigo_vendedor') ?></div>
            <div class="fl">ACTIVIDAD ECON.:</div>
            <div class="fv"><?= rv($form,'codigo_ciiu') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">FORMA PAGO:</div>
            <div class="fv"><?= rv($form,'forma_pago') ?></div>
        </div>
    </div>
</div>

<!-- REPRESENTANTE LEGAL -->
<div class="form-section">
    <div class="section-title">DATOS DEL REPRESENTANTE LEGAL</div>
    <div class="section-content">
        <div class="fr c33">
            <div class="fl">NOMBRE:</div>
            <div class="fv"><?= rv($form,'representante_nombre') ?></div>
            <div class="fl">TIPO DOC:</div>
            <div class="fv"><?= rv($form,'representante_tipo_doc') ?></div>
            <div class="fl">NÚMERO:</div>
            <div class="fv"><?= rv($form,'representante_documento') ?></div>
        </div>
        <div class="fr c33">
            <div class="fl">PROFESIÓN:</div>
            <div class="fv"><?= rv($form,'representante_profesion') ?></div>
            <div class="fl">F. NACIMIENTO:</div>
            <div class="fv"><?= rv_date($form,'representante_nacimiento') ?></div>
            <div class="fl">TELÉFONO:</div>
            <div class="fv"><?= rv($form,'representante_telefono') ?></div>
        </div>
        <div class="fr c1">
            <div class="fl">CORREO:</div>
            <div class="fv"><?= rv($form,'representante_email') ?></div>
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
                    <td class="field-label" style="width:35%;">NOMBRE / RAZÓN SOCIAL</td>
                    <td class="field-label" style="width:20%;">DOCUMENTO</td>
                    <td class="field-label" style="width:15%;">% PARTICIPACIÓN</td>
                    <td class="field-label" style="width:20%;">NACIONALIDAD</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accionistas as $acc): ?>
                <tr>
                    <td><?= htmlspecialchars($acc['nombre'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($acc['documento'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($acc['participacion'] ?? '—') ?>%</td>
                    <td><?= htmlspecialchars($acc['nacionalidad'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- DATOS TRIBUTARIOS -->
<div class="form-section">
    <div class="section-title">DATOS TRIBUTARIOS</div>
    <div class="section-content">
        <div class="fr c1">
            <div class="fl">TIPO DE CONTRIBUYENTE:</div>
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
