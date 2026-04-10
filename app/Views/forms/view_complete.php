<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario SAGRILAFT - #<?= $form['id'] ?></title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/font-scale-enhanced.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #1e40af; font-size: 24px; margin-bottom: 10px; }
        .header .meta { color: #64748b; font-size: 14px; }
        .section { margin-bottom: 30px; page-break-inside: avoid; }
        .section-title { background: #3b82f6; color: white; padding: 10px 15px; font-weight: bold; font-size: 16px; margin-bottom: 15px; }
        .field-group { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 15px; }
        .field-group.full { grid-template-columns: 1fr; }
        .field-group.three { grid-template-columns: repeat(3, 1fr); }
        .field { border: 1px solid #e2e8f0; padding: 12px; background: #f8fafc; }
        .field-label { font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .field-value { font-size: 14px; color: #1e293b; word-wrap: break-word; }
        .attachments { margin-top: 20px; }
        .attachment-item { display: flex; align-items: center; gap: 10px; padding: 10px; background: #f1f5f9; border: 1px solid #cbd5e1; margin-bottom: 10px; }
        .attachment-item a { color: #3b82f6; text-decoration: none; word-break: break-all; flex: 1; }
        .attachment-item a:hover { text-decoration: underline; }
        .print-button { position: fixed; top: 20px; right: 20px; background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3); z-index: 1000; }
        .print-button:hover { background: #2563eb; }
        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; padding: 20px; }
            .print-button { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Imprimir</button>
    
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($form['title']) ?></h1>
            <div class="meta">
                ID: #<?= $form['id'] ?> | 
                Creado: <?= date('d/m/Y H:i', strtotime($form['created_at'])) ?> |
                Estado: <?= $form['approval_status'] === 'approved' ? 'APROBADO' : ($form['approval_status'] === 'rejected' ? 'RECHAZADO' : 'PENDIENTE') ?>
                <?php if (!empty($relatedForms)): ?>
                | <strong style="color: #f59e0b;">+ <?= count($relatedForms) ?> formulario(s) relacionado(s)</strong>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        // Función para mostrar todos los campos de un formulario
        function mostrarFormulario($formData, $sectionOffset = 0) {
        ?>
        
        <!-- MOSTRAR TODOS LOS CAMPOS DEL FORMULARIO -->
        <?php
        // Definir todos los campos posibles y sus etiquetas
        $allFields = [
            // Datos Generales
            'company_name' => 'Empresa/Persona',
            'sucursal' => 'Sucursal',
            'nit' => 'NIT/Cédula',
            'nombre_establecimiento' => 'Nombre Establecimiento',
            'ciudad' => 'Ciudad',
            'barrio' => 'Barrio',
            'localidad' => 'Localidad',
            'address' => 'Dirección',
            'phone' => 'Teléfono',
            'telefono_fijo' => 'Teléfono Fijo',
            'celular' => 'Celular',
            'email' => 'Email',
            'fax' => 'Fax',
            'pais' => 'País',
            // Actividad Económica
            'activity' => 'Actividad Económica',
            'codigo_ciiu' => 'Código CIIU',
            'objeto_social' => 'Objeto Social',
            // Información Financiera
            'activos' => 'Activos',
            'pasivos' => 'Pasivos',
            'patrimonio' => 'Patrimonio',
            'ingresos' => 'Ingresos',
            'gastos' => 'Gastos',
            'otros_ingresos' => 'Otros Ingresos',
            'detalle_otros_ingresos' => 'Detalle Otros Ingresos',
            // Datos Tributarios
            'tipo_contribuyente' => 'Tipo Contribuyente',
            'regimen_tributario' => 'Régimen Tributario',
            // Representante Legal
            'representante_nombre' => 'Representante Legal - Nombre',
            'representante_documento' => 'Representante Legal - Documento',
            'representante_tipo_doc' => 'Representante Legal - Tipo Doc',
            'representante_profesion' => 'Representante Legal - Profesión',
            'representante_nacimiento' => 'Representante Legal - Fecha Nacimiento',
            'representante_telefono' => 'Representante Legal - Teléfono',
            'representante_direccion' => 'Representante Legal - Dirección',
            // Accionistas
            'accionistas' => 'Accionistas/Socios',
            // Datos Clientes
            'lista_precios' => 'Lista de Precios',
            'codigo_vendedor' => 'Código Vendedor',
            'tipo_pago' => 'Tipo de Pago',
            'cupo_credito' => 'Cupo de Crédito',
            'fecha_nacimiento' => 'Fecha de Nacimiento',
            // Datos Proveedores
            'tipo_compania' => 'Tipo de Compañía',
            'persona_contacto' => 'Persona de Contacto',
            'tiene_certificacion' => 'Tiene Certificación',
            'cual_certificacion' => 'Cuál Certificación',
            // Importación
            'concepto_importacion' => 'Concepto Importación',
            'declaracion_importacion' => 'Declaración Importación',
            'certificado_origen' => 'Certificado de Origen',
            'certificado_transporte' => 'Certificado de Transporte',
            'certificado_fitosanitario' => 'Certificado Fitosanitario',
            'copia_swift' => 'Copia SWIFT',
            // Origen de Fondos y PEP
            'origen_fondos' => 'Origen de Fondos',
            'es_pep' => '¿Es PEP?',
            'cargo_pep' => 'Cargo PEP',
            'fecha_vinculacion_pep' => 'Fecha Vinculación PEP',
            'fecha_desvinculacion_pep' => 'Fecha Desvinculación PEP',
            'relacion_pep' => 'Relación con PEP',
            'identificacion_pep' => 'Identificación PEP',
            'familiares_pep' => 'Familiares PEP',
            'tiene_cuentas_exterior' => '¿Tiene Cuentas en el Exterior?',
            'pais_cuentas_exterior' => 'País de Cuentas Exterior',
            // Autorizaciones
            'autoriza_centrales_riesgo' => 'Autoriza Consulta Centrales de Riesgo',
            'consulta_ofac' => 'Consulta OFAC',
            'consulta_listas_nacionales' => 'Consulta Listas Nacionales',
            'consulta_onu' => 'Consulta ONU',
            // Firma
            'nombre_firmante' => 'Nombre Firmante',
            'clase_cliente' => 'Clase de Cliente',
            'descripcion_firma' => 'Descripción Firma',
            // Campos Internos
            'director_cartera' => 'Director de Cartera',
            'gerencia_comercial' => 'Gerencia Comercial',
            'oficial_cumplimiento' => 'Oficial de Cumplimiento',
            'fecha_oficial_cumplimiento' => 'Fecha Oficial Cumplimiento',
            // Información Adicional
            'content' => 'Información Adicional'
        ];
        
        // Agrupar campos por sección - MOSTRAR TODO
        $sections = [
            'Datos Generales' => ['company_name', 'sucursal', 'nit', 'nombre_establecimiento', 'ciudad', 'barrio', 'localidad', 'address', 'phone', 'telefono_fijo', 'celular', 'email', 'fax', 'pais'],
            'Actividad Económica' => ['activity', 'codigo_ciiu', 'objeto_social'],
            'Información Financiera' => ['activos', 'pasivos', 'patrimonio', 'ingresos', 'gastos', 'otros_ingresos', 'detalle_otros_ingresos'],
            'Datos Tributarios' => ['tipo_contribuyente', 'regimen_tributario'],
            'Representante Legal' => ['representante_nombre', 'representante_documento', 'representante_tipo_doc', 'representante_profesion', 'representante_nacimiento', 'representante_telefono', 'representante_direccion'],
            'Accionistas/Socios' => ['accionistas'],
            'Información Comercial' => ['lista_precios', 'codigo_vendedor', 'tipo_pago', 'cupo_credito', 'fecha_nacimiento', 'tipo_compania', 'persona_contacto', 'tiene_certificacion', 'cual_certificacion'],
            'Importación' => ['concepto_importacion', 'declaracion_importacion', 'certificado_origen', 'certificado_transporte', 'certificado_fitosanitario', 'copia_swift'],
            'Origen de Fondos y PEP' => ['origen_fondos', 'es_pep', 'cargo_pep', 'fecha_vinculacion_pep', 'fecha_desvinculacion_pep', 'relacion_pep', 'identificacion_pep', 'familiares_pep', 'tiene_cuentas_exterior', 'pais_cuentas_exterior'],
            'Autorizaciones y Consultas' => ['autoriza_centrales_riesgo', 'consulta_ofac', 'consulta_listas_nacionales', 'consulta_onu'],
            'Firma y Sello' => ['nombre_firmante', 'clase_cliente', 'descripcion_firma'],
            'Campos Internos' => ['director_cartera', 'gerencia_comercial', 'oficial_cumplimiento', 'fecha_oficial_cumplimiento'],
            'Información Adicional' => ['content']
        ];
        
        $sectionNumber = 1 + $sectionOffset;
        foreach ($sections as $sectionName => $fields):
            // Verificar si hay al menos un campo con valor en esta sección
            $hasData = false;
            foreach ($fields as $field) {
                if (!empty($formData[$field])) {
                    $hasData = true;
                    break;
                }
            }
            
            if ($hasData):
        ?>
        <div class="section">
            <div class="section-title"><?= $sectionNumber ?>. <?= strtoupper($sectionName) ?></div>
            <div class="field-group">
                <?php foreach ($fields as $field): ?>
                    <?php if (!empty($formData[$field])): ?>
                    <div class="field">
                        <div class="field-label"><?= $allFields[$field] ?></div>
                        <div class="field-value">
                            <?php
                            $value = $formData[$field];
                            // Formatear según el tipo de campo
                            if (in_array($field, ['activos', 'pasivos', 'patrimonio', 'ingresos', 'gastos', 'otros_ingresos', 'cupo_credito'])) {
                                echo '$' . number_format($value, 2);
                            } elseif (in_array($field, ['fecha_nacimiento', 'representante_nacimiento', 'fecha_vinculacion_pep', 'fecha_desvinculacion_pep', 'fecha_oficial_cumplimiento'])) {
                                echo date('d/m/Y', strtotime($value));
                            } elseif ($field === 'accionistas' && !empty($value)) {
                                // Decodificar JSON de accionistas
                                $accionistas = json_decode($value, true);
                                if (is_array($accionistas)) {
                                    echo '<ul style="margin: 0; padding-left: 20px;">';
                                    foreach ($accionistas as $accionista) {
                                        echo '<li>' . htmlspecialchars($accionista['nombre'] ?? '') . ' - ' . htmlspecialchars($accionista['participacion'] ?? '') . '%</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo htmlspecialchars($value);
                                }
                            } elseif (in_array($field, ['activity', 'objeto_social', 'detalle_otros_ingresos', 'origen_fondos', 'familiares_pep', 'content', 'concepto_importacion', 'descripcion_firma'])) {
                                echo nl2br(htmlspecialchars($value));
                            } else {
                                echo htmlspecialchars($value);
                            }
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
            $sectionNumber++;
            endif;
        endforeach;
        
        return $sectionNumber;
        } // Fin función mostrarFormulario
        
        // Mostrar el formulario principal
        $nextSection = mostrarFormulario($form, 0);
        ?>
        
        <!-- FORMULARIOS RELACIONADOS -->
        <?php if (!empty($relatedForms)): ?>
            <?php foreach ($relatedForms as $index => $relatedForm): ?>
                <div style="margin-top: 40px; padding-top: 40px; border-top: 3px solid #f59e0b;">
                    <div class="header" style="border-bottom: 3px solid #f59e0b;">
                        <h1 style="color: #f59e0b;">Formulario Relacionado #<?= $index + 2 ?></h1>
                        <div class="meta">
                            ID: #<?= $relatedForm['id'] ?> | 
                            <?= htmlspecialchars($relatedForm['title']) ?> |
                            Creado: <?= date('d/m/Y H:i', strtotime($relatedForm['created_at'])) ?>
                        </div>
                    </div>
                    <?php
                    // Mostrar el formulario relacionado
                    $nextSection = mostrarFormulario($relatedForm, $nextSection - 1);
                    
                    // Mostrar adjuntos del formulario relacionado
                    $attachmentModel = new \App\Models\Attachment();
                    $relatedAttachments = $attachmentModel->getByFormId((int)$relatedForm['id']);
                    if (!empty($relatedAttachments) && count($relatedAttachments) > 0):
                    ?>
                    <div class="section">
                        <div class="section-title"><?= $nextSection ?>. DOCUMENTOS ADJUNTOS (Formulario #<?= $relatedForm['id'] ?>)</div>
                        <div class="attachments">
                            <?php foreach ($relatedAttachments as $attachment): ?>
                            <div class="attachment-item">
                                <span>Adjunto:</span>
                                <a href="/gestion-sagrilaft/public/forms/attachment/<?= $attachment['id'] ?>" target="_blank">
                                    <?= htmlspecialchars($attachment['filename'] ?? $attachment['original_filename'] ?? 'Documento ' . $attachment['id']) ?>
                                </a>
                                <?php if (!empty($attachment['filesize']) || !empty($attachment['file_size'])): ?>
                                <span style="color: #64748b; font-size: 12px;">
                                    (<?= number_format(($attachment['filesize'] ?? $attachment['file_size'] ?? 0) / 1024, 2) ?> KB)
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php
                    $nextSection++;
                    endif;
                    ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        
        <!-- DOCUMENTOS ADJUNTOS DEL FORMULARIO PRINCIPAL -->
        <?php if (!empty($attachments) && count($attachments) > 0): ?>
        <div class="section">
            <div class="section-title"><?= $nextSection ?>. DOCUMENTOS ADJUNTOS</div>
            <div class="attachments">
                <?php foreach ($attachments as $attachment): ?>
                <div class="attachment-item">
                    <span>Adjunto:</span>
                    <a href="/gestion-sagrilaft/public/forms/attachment/<?= $attachment['id'] ?>" target="_blank">
                        <?= htmlspecialchars($attachment['filename'] ?? $attachment['original_filename'] ?? 'Documento ' . $attachment['id']) ?>
                    </a>
                    <?php if (!empty($attachment['filesize']) || !empty($attachment['file_size'])): ?>
                    <span style="color: #64748b; font-size: 12px;">
                        (<?= number_format(($attachment['filesize'] ?? $attachment['file_size'] ?? 0) / 1024, 2) ?> KB)
                    </span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php $nextSection++; endif; ?>
        
        <!-- INFORMACIÓN DE REVISIÓN -->
        <?php if ($form['approval_status'] !== 'pending'): ?>
        <div class="section">
            <div class="section-title"><?= $nextSection ?>. INFORMACIÓN DE REVISIÓN</div>
            
            <div class="field-group">
                <?php if (!empty($form['approval_date']) || !empty($form['reviewed_at'])): ?>
                <div class="field">
                    <div class="field-label">Fecha de Revisión</div>
                    <div class="field-value"><?= date('d/m/Y H:i', strtotime($form['approval_date'] ?? $form['reviewed_at'])) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($form['approved_by'])): ?>
                <div class="field">
                    <div class="field-label">Revisado por</div>
                    <div class="field-value"><?= htmlspecialchars($form['approved_by']) ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($form['reviewer_comments']) || !empty($form['approval_observations'])): ?>
            <div class="field-group full">
                <div class="field">
                    <div class="field-label">Observaciones</div>
                    <div class="field-value"><?= nl2br(htmlspecialchars($form['reviewer_comments'] ?? $form['approval_observations'] ?? '')) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
