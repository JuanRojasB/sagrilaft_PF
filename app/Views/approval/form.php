<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Formulario - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/font-scale-enhanced.css">
    <style>
        body { background: #f8fafc; color: #0f172a; }
        /* Header */
        .app-header { background: #ffffff !important; border-bottom: 1px solid #e2e8f0 !important; }
        /* Form data tables */
        .form-section { border:1px solid #e2e8f0; margin-bottom:16px; border-radius:6px; overflow:hidden; background:#ffffff; }
        .section-title { background:#f1f5f9; padding:10px 14px; font-weight:700; font-size:11px; text-align:center; border-bottom:1px solid #e2e8f0; text-transform:uppercase; letter-spacing:1px; color:#475569; }
        .section-content { padding:0; }
        .fr { display:grid; border-bottom:1px solid #f1f5f9; min-height:38px; }
        .fr:last-child { border-bottom:none; }
        .fl { font-weight:600; background:#f8fafc; color:#475569; font-size:10px; text-transform:uppercase; letter-spacing:0.4px; padding:8px 10px; display:flex; align-items:center; border-right:1px solid #f1f5f9; word-wrap:break-word; overflow-wrap:break-word; hyphens:auto; }
        .fv { padding:8px 12px; display:flex; align-items:center; flex-wrap:wrap; gap:8px; font-size:13px; color:#0f172a; border-right:1px solid #f1f5f9; min-width:0; word-wrap:break-word; overflow-wrap:break-word; }
        .fv:last-child, .fl:last-child { border-right:none; }
        .c1   { grid-template-columns:220px 1fr; }
        .c22  { grid-template-columns:220px 1fr 220px 1fr; }
        .c33  { grid-template-columns:180px 1fr 180px 1fr 180px 1fr; }
        .c222 { grid-template-columns:180px 1fr 180px 1fr 180px 1fr; }
        .c322 { grid-template-columns:160px 1fr 140px 1fr 120px 1fr; }
        .cfull { grid-template-columns:1fr; }
        .fr.dir-row { grid-template-columns:160px 1fr; }
        .field-table { width:100%; border-collapse:collapse; }
        .field-table td { border:1px solid #e2e8f0; padding:8px 12px; font-size:12px; color:#0f172a; }
        .field-label { font-weight:600; background:#f8fafc; color:#475569; text-transform:uppercase; font-size:10px; }
        @media (max-width:900px) { .c22 { grid-template-columns:200px 1fr !important; } .c33 { grid-template-columns:160px 1fr !important; } .c322 { grid-template-columns:140px 1fr !important; } }
        @media (max-width:600px) { .c22,.c33,.c222,.c322,.c1,.fr.dir-row { grid-template-columns:1fr !important; } .fl { border-right:none !important; border-bottom:1px solid #f1f5f9; } }
        /* Inputs en panel de aprobación */
        input, textarea { width:100%; padding:0.75rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.85rem; }
        input:focus, textarea:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 2px rgba(59,130,246,0.15); }
        button { cursor:pointer; transition:all 0.15s; }
        button:disabled { opacity:0.5; cursor:not-allowed; }
        /* Cards genéricas */
        .light-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:0.5rem; padding:1.25rem; margin-bottom:1rem; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
        .btn-header { background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; padding:0.4rem 0.75rem; border-radius:0.25rem; text-decoration:none; font-weight:600; font-size:0.8rem; transition:all 0.15s; display:inline-flex; align-items:center; gap:0.3rem; white-space:nowrap; }
        .btn-header:hover { background:#e2e8f0; border-color:#94a3b8; }
        .btn-pdf { background:#1d4ed8; color:#ffffff; border:none; padding:0.75rem 1.5rem; border-radius:0.25rem; text-decoration:none; font-size:0.9rem; font-weight:700; white-space:nowrap; display:inline-flex; align-items:center; gap:0.5rem; flex-shrink:0; transition:all 0.15s; }
        .btn-pdf:hover { background:#1e40af; transform:translateY(-1px); }
    </style>
</head>
<body>
<div class="app">
    <header class="app-header" style="padding:0.75rem 1.25rem; background:#ffffff; border-bottom:1px solid #e2e8f0;">
        <div class="brand" style="display:flex; align-items:center; gap:0.75rem;">
            <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=4" alt="Logo" style="width:32px; height:32px;">
            <h1 style="font-size:1.1rem; margin:0; color:#0f172a;">SAGRILAFT - Revisión</h1>
        </div>
        <?php if ($is_logged_in): ?>
        <div class="header-actions" style="display:flex; gap:0.5rem;">
            <a href="index.php?route=/reviewer/dashboard" class="btn-header">
                <span>← Volver</span>
            </a>
        </div>
        <?php endif; ?>
    </header>

    <main class="app-main" style="padding:0;">
        <div style="max-width:900px; margin:0 auto; padding:1rem;">

            <!-- Step 1: Login -->
            <div id="step1" style="<?= $is_logged_in ? 'display:none;' : '' ?> background:#ffffff; border:1px solid #e2e8f0; border-radius:0.5rem; padding:2rem; max-width:450px; margin:2rem auto; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                <h2 style="margin:0 0 0.5rem; font-size:1.2rem; color:#0f172a; font-weight:600;">Revisión de Formulario</h2>
                <p style="margin:0 0 1.5rem; font-size:0.85rem; color:#475569;">Por favor identifícate para continuar</p>
                <form id="reviewerLoginForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-size:0.85rem; color:#334155; font-weight:600;">Usuario</label>
                        <input type="text" id="reviewer_email" name="reviewer_email" required placeholder="" autocomplete="username">
                    </div>
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-size:0.85rem; color:#334155; font-weight:600;">Contraseña</label>
                        <input type="password" id="reviewer_password" name="reviewer_password" required placeholder="••••••••" autocomplete="current-password">
                    </div>
                    <button type="submit" style="width:100%; padding:0.75rem; background:#1d4ed8; color:#ffffff; border:none; border-radius:0.25rem; font-weight:600; font-size:0.85rem; cursor:pointer;">
                        Iniciar Sesión
                    </button>
                </form>
                <div id="loginMessage" style="margin-top:1rem; text-align:center; font-size:0.85rem;"></div>
            </div>

            <!-- Step 2: Revisión -->
            <div id="step2" style="<?= $is_logged_in ? '' : 'display:none;' ?>">

                <?php
                $formCode = $formTypeCodeMap[$form['form_type'] ?? ''] ?? 'N/A';
                $isEmpleado = ($form['form_type'] ?? '') === 'empleado';
                ?>

                <!-- Header info -->
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:0.5rem; padding:1.5rem; margin-bottom:1rem; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <div style="display:flex; justify-content:space-between; align-items:start; gap:1rem; margin-bottom:0.5rem;">
                        <h2 style="margin:0; font-size:1.2rem; color:#0f172a; font-weight:600;">
                            <?php if ($isEmpleado): ?>
                                Registro de Empleado - <?= htmlspecialchars($form['empleado_nombre'] ?? 'Sin nombre') ?>
                            <?php else: ?>
                                Formulario SAGRILAFT - <?= htmlspecialchars($form['company_name'] ?? 'Sin nombre') ?>
                            <?php endif; ?>
                        </h2>
                        <span style="background:#fef9c3; color:#a16207; border:1px solid #fde047; padding:0.3rem 0.75rem; border-radius:0.25rem; font-size:0.8rem; font-weight:600; white-space:nowrap;">
                            Pendiente
                        </span>
                    </div>
                    <div style="display:flex; gap:1.5rem; flex-wrap:wrap; font-size:0.75rem; color:#64748b;">
                        <span>ID: #<?= $form['id'] ?></span>
                        <span>Creado: <?= (!empty($form['created_at']) && strtotime($form['created_at'])) ? date('d/m/Y H:i', strtotime($form['created_at'])) : '—' ?></span>
                        <span>Revisor: <strong><?= htmlspecialchars($reviewer_name) ?></strong></span>
                        <?php if ($formCode !== 'N/A'): ?>
                        <span>Código: <strong><?= htmlspecialchars($formCode) ?></strong></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Alerta formularios relacionados con observaciones -->
                <?php if (!empty($related_forms)): ?>
                <div style="background:#fef9c3; border:2px solid #fde047; border-radius:0.5rem; padding:1.25rem; margin-bottom:1rem;">
                    <div style="display:flex; align-items:start; gap:0.75rem;">
                        <span style="font-size:1.5rem;">⚠️</span>
                        <div style="flex:1;">
                            <h3 style="margin:0 0 0.5rem; font-size:0.9rem; color:#a16207; font-weight:700;">Formulario Anterior con Observaciones</h3>
                            <p style="margin:0 0 0.75rem; font-size:0.85rem; color:#a16207; line-height:1.5;">Este usuario tiene otro formulario con el mismo NIT:</p>
                            <?php foreach ($related_forms as $relatedForm): ?>
                            <div style="background:#ffffff; border:1px solid #fde047; border-radius:0.25rem; padding:0.75rem; margin-bottom:0.5rem;">
                                <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                                    <div>
                                        <div style="font-size:0.85rem; color:#0f172a; font-weight:600; margin-bottom:0.25rem;">
                                            #<?= $relatedForm['id'] ?> - <?= (!empty($relatedForm['approval_date']) && strtotime($relatedForm['approval_date'])) ? date('d/m/Y', strtotime($relatedForm['approval_date'])) : '—' ?>
                                        </div>
                                        <?php if (!empty($relatedForm['approval_observations'])): ?>
                                        <div style="font-size:0.75rem; color:#a16207; margin-top:0.5rem; padding:0.5rem; background:#fef9c3; border-radius:0.25rem;">
                                            <strong>Observaciones:</strong> <?= htmlspecialchars(substr($relatedForm['approval_observations'], 0, 100)) ?><?= strlen($relatedForm['approval_observations']) > 100 ? '...' : '' ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <a href="/gestion-sagrilaft/public/index.php?route=/approval/<?= $relatedForm['approval_token'] ?>" target="_blank"
                                       style="background:#dbeafe; color:#1d4ed8; border:1px solid #93c5fd; padding:0.4rem 0.75rem; border-radius:0.25rem; text-decoration:none; font-size:0.75rem; font-weight:600; white-space:nowrap;">
                                        Ver
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- PDF (solo para no-empleados) -->
                <?php if (!$isEmpleado): ?>
                <div style="background:#eff6ff; border:2px solid #93c5fd; border-radius:0.5rem; padding:1.25rem; margin-bottom:1rem;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                        <div style="flex:1; min-width:0;">
                            <h3 style="margin:0 0 0.5rem; font-size:0.95rem; color:#1d4ed8; font-weight:700;">Formulario Completo</h3>
                            <p style="margin:0; font-size:0.85rem; color:#3b82f6; line-height:1.5;">Revisa el formulario completo en formato PDF con todos los datos y documentos adjuntos.</p>
                        </div>
                        <a href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/view-pdf.php?id=<?= $form['id'] ?>" target="_blank" class="btn-pdf" rel="noopener noreferrer">
                            Ver PDF
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Adjuntos (solo mostrar si no es empleado O si es empleado con adjuntos) -->
                <?php if (!$isEmpleado || !empty($attachments)): ?>
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:0.5rem; padding:1.25rem; margin-bottom:1rem; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <h3 style="margin:0 0 0.75rem; font-size:0.9rem; color:#0f172a; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;">
                        <?= $isEmpleado ? 'Cédula Adjunta' : 'Documentos Adjuntos del Usuario' ?>
                    </h3>
                    <?php if (!empty($attachments)): ?>
                        <div style="display:grid; gap:0.5rem;">
                            <?php foreach ($attachments as $attachment): ?>
                            <a href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/view-attachment.php?id=<?= (int)$attachment['id'] ?>" target="_blank"
                               style="display:flex; justify-content:space-between; align-items:center; gap:1rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:0.25rem; padding:0.6rem 0.75rem; color:#0f172a; text-decoration:none; transition:background 0.15s;">
                                <span style="font-size:0.82rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($attachment['filename'] ?? $attachment['original_filename'] ?? ('Adjunto #' . $attachment['id'])) ?></span>
                                <span style="font-size:0.75rem; color:#64748b; white-space:nowrap;">
                                    <?= (isset($attachment['filesize']) || isset($attachment['file_size'])) ? number_format(((float)($attachment['filesize'] ?? $attachment['file_size'] ?? 0)) / 1024, 2) . ' KB' : 'Descargar' ?>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="font-size:0.84rem; color:#64748b; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:0.25rem; padding:0.75rem;">
                            Este formulario no tiene documentos adjuntos.
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Datos del formulario — vista tipo formulario -->
                <?php
                $reviewFormMap = [
                    'cliente_natural'                => 'cliente_natural',
                    'cliente_juridica'               => 'cliente_juridica',
                    'proveedor_natural'              => 'proveedor_natural',
                    'proveedor_juridica'             => 'proveedor_juridica',
                    'proveedor_internacional'        => 'proveedor_internacional',
                    'declaracion_fondos_clientes'    => 'declaracion_fondos',
                    'declaracion_cliente'            => 'declaracion_fondos',
                    'declaracion_fondos_proveedores' => 'declaracion_fondos',
                    'declaracion_proveedor'          => 'declaracion_fondos',
                    'empleado'                       => 'empleado',
                ];
                $reviewFile = $reviewFormMap[$form['form_type'] ?? ''] ?? null;
                $reviewPath = $reviewFile ? __DIR__ . '/../forms/review_forms/' . $reviewFile . '.php' : null;
                if ($reviewPath && file_exists($reviewPath)) {
                    include $reviewPath;
                }
                ?>

                <!-- Formularios relacionados (Declaración adjunta) -->
                <?php
                // Solo mostrar formularios relacionados si el formulario principal NO es una declaración
                $isDeclaracion = str_starts_with((string)($form['form_type'] ?? ''), 'declaracion');
                if (!$isDeclaracion):
                $dbRel    = \App\Core\Database::getConnection();
                $stmtRel  = $dbRel->prepare("SELECT * FROM forms WHERE related_form_id = ? ORDER BY id ASC");
                $stmtRel->execute([$form['id']]);
                $relForms = $stmtRel->fetchAll(\PDO::FETCH_ASSOC);
                $decReviewPath = __DIR__ . '/../forms/review_forms/declaracion_fondos.php';
                foreach ($relForms as $rf):
                    $rfFormType = (string)($rf['form_type'] ?? '');
                    $rfReviewFile = $reviewFormMap[$rfFormType] ?? (str_starts_with($rfFormType, 'declaracion') ? 'declaracion_fondos' : null);
                    $rfReviewPath = $rfReviewFile ? __DIR__ . '/../forms/review_forms/' . $rfReviewFile . '.php' : $decReviewPath;
                    $rfTitle = str_starts_with($rfFormType, 'declaracion') ? 'Declaración de Origen de Fondos' : 'Formulario Relacionado #' . $rf['id'];
                ?>
                <div style="border:2px solid #93c5fd; border-radius:6px; margin-bottom:1rem; overflow:hidden;">
                    <div style="background:#eff6ff; padding:10px 14px; font-size:11px; font-weight:700; color:#1d4ed8; text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid #93c5fd;">
                        <?= htmlspecialchars($rfTitle) ?>
                    </div>
                    <?php
                    $parentForm = $form;
                    $form = $rf;
                    if (file_exists($rfReviewPath)) { include $rfReviewPath; }
                    $form = $parentForm;
                    ?>
                </div>
                <?php endforeach;
                endif; ?>


                <!-- Decisión del Revisor -->
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:0.5rem; padding:1.5rem; margin-bottom:1rem; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <h3 style="margin:0 0 1rem; font-size:0.9rem; color:#0f172a; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;">Decisión del Revisor</h3>

                    <form id="approvalForm" method="POST" action="index.php?route=/approval/<?= htmlspecialchars($token) ?>" data-return-url="index.php?route=/approval/<?= htmlspecialchars($token) ?>">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="approved_by" value="<?= htmlspecialchars($reviewer_name) ?>">

                        <?php 
                        $isEmpleado = ($form['form_type'] ?? '') === 'empleado';
                        if (!$isEmpleado): 
                        ?>
                        
                        <!-- Espacio para ser tramitado por el departamento de Cartera/Compras -->
                        <div style="margin-bottom:1rem; padding:0.9rem; background:#fef3c7; border:1px solid #fcd34d; border-radius:0.25rem;">
                            <div style="font-size:0.78rem; color:#92400e; margin-bottom:0.6rem; text-transform:uppercase; letter-spacing:0.05em; font-weight:700;">
                                <?php
                                // Determinar si es Cliente o Proveedor para el título
                                $formType = $form['form_type'] ?? 'cliente';
                                $departamento = (strpos($formType, 'proveedor') !== false || strpos($formType, 'transportista') !== false) 
                                    ? 'DEPARTAMENTO DE COMPRAS' 
                                    : 'DEPARTAMENTO DE CARTERA';
                                echo "Espacio para ser tramitado por el {$departamento}";
                                ?>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:0.75rem;">
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Vinculación</label>
                                    <select name="vinculacion" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                        <option value="">Seleccione</option>
                                        <option value="nueva" <?= (($form['vinculacion'] ?? '') === 'nueva') ? 'selected' : '' ?>>Nueva</option>
                                        <option value="actualizacion" <?= (($form['vinculacion'] ?? '') === 'actualizacion') ? 'selected' : '' ?>>Actualización</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Fecha de Vinculación</label>
                                    <input type="date" name="fecha_vinculacion" value="<?= htmlspecialchars((string)($form['fecha_vinculacion'] ?? date('Y-m-d'))) ?>" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                </div>
                                <div style="grid-column:1/-1;">
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Actualización</label>
                                    <input type="text" name="actualizacion" value="<?= htmlspecialchars((string)($form['actualizacion'] ?? '')) ?>" placeholder="Ej: Primera actualización, Segunda actualización..." style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                </div>
                            </div>
                        </div>

                        <!-- Espacio exclusivo Pollo Fiesta -->
                        <div style="margin-bottom:1rem; padding:0.9rem; background:#eff6ff; border:1px solid #bfdbfe; border-radius:0.25rem;">
                            <div style="font-size:0.78rem; color:#1d4ed8; margin-bottom:0.6rem; text-transform:uppercase; letter-spacing:0.05em; font-weight:700;">
                                Espacio Exclusivo Para Pollo Fiesta
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:0.75rem; margin-bottom:0.75rem;">
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Consulta OFAC</label>
                                    <select name="consulta_ofac" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                        <option value="">Seleccione</option>
                                        <option value="negativa" <?= (($form['consulta_ofac'] ?? '') === 'negativa') ? 'selected' : '' ?>>Negativa</option>
                                        <option value="positiva" <?= (($form['consulta_ofac'] ?? '') === 'positiva') ? 'selected' : '' ?>>Positiva</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Listas Nacionales</label>
                                    <select name="consulta_listas_nacionales" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                        <option value="">Seleccione</option>
                                        <option value="negativa" <?= (($form['consulta_listas_nacionales'] ?? '') === 'negativa') ? 'selected' : '' ?>>Negativa</option>
                                        <option value="positiva" <?= (($form['consulta_listas_nacionales'] ?? '') === 'positiva') ? 'selected' : '' ?>>Positiva</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:0.75rem; margin-bottom:0.75rem;">
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Consulta ONU</label>
                                    <select name="consulta_onu" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                        <option value="">Seleccione</option>
                                        <option value="negativa" <?= (($form['consulta_onu'] ?? '') === 'negativa') ? 'selected' : '' ?>>Negativa</option>
                                        <option value="positiva" <?= (($form['consulta_onu'] ?? '') === 'positiva') ? 'selected' : '' ?>>Positiva</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Consulta Interpol</label>
                                    <select name="consulta_interpol" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                        <option value="">Seleccione</option>
                                        <option value="negativa" <?= (($form['consulta_interpol'] ?? '') === 'negativa') ? 'selected' : '' ?>>Negativa</option>
                                        <option value="positiva" <?= (($form['consulta_interpol'] ?? '') === 'positiva') ? 'selected' : '' ?>>Positiva</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:0.75rem;">
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Recibe</label>
                                    <input type="text" name="recibe" value="<?= htmlspecialchars((string)($form['recibe'] ?? 'Angie Paola Martínez Paredes')) ?>" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                </div>
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Verificado por</label>
                                    <input type="text" name="verificado_por" value="<?= htmlspecialchars((string)($form['verificado_por'] ?? 'Angie Paola Martínez Paredes')) ?>" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                </div>
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Director de cartera</label>
                                    <input type="text" name="director_cartera" value="<?= htmlspecialchars((string)($form['director_cartera'] ?? 'Luz Mery Murillo')) ?>" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                </div>
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Gerencia comercial</label>
                                    <select name="gerencia_comercial" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                        <?php 
                                        $gerenciaValue = (string)($form['gerencia_comercial'] ?? '');
                                        $gerenciaOptions = ['Hernan Mateo Benito', 'German Rodriguez'];
                                        ?>
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($gerenciaOptions as $option): ?>
                                            <option value="<?= htmlspecialchars($option) ?>" <?= $gerenciaValue === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Preparó</label>
                                    <?php
                                    // Obtener lista de asesores comerciales para el campo Preparó (misma lista que "Asesor Comercial que lo atendió")
                                    $db = \App\Core\Database::getConnection();
                                    $stmt = $db->query("SELECT id, nombre_completo FROM asesores_comerciales WHERE activo = 1 ORDER BY nombre_completo");
                                    $asesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    $preparoValue = (string)($form['preparo'] ?? '');
                                    ?>
                                    <select name="preparo" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                        <option value="">Seleccionar...</option>
                                        <?php foreach ($asesores as $asesor): 
                                            // Convertir nombre a formato título (primera letra de cada palabra en mayúscula)
                                            $nombreFormateado = mb_convert_case(mb_strtolower($asesor['nombre_completo'], 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
                                        ?>
                                            <option value="<?= htmlspecialchars($nombreFormateado) ?>" <?= $preparoValue === $nombreFormateado || $preparoValue === $asesor['nombre_completo'] ? 'selected' : '' ?>><?= htmlspecialchars($nombreFormateado) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Revisó</label>
                                    <input type="text" name="reviso" value="<?= htmlspecialchars((string)($form['reviso'] ?? 'Angie Paola Martínez Paredes')) ?>" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                </div>
                                <div style="grid-column:1/-1;">
                                    <?php
                                    $nombreOficialValue = trim((string)($form['nombre_oficial'] ?? ''));
                                    if ($nombreOficialValue === '') {
                                        $nombreOficialValue = 'Angie Paola Martínez Paredes';
                                    }
                                    ?>
                                    <label style="display:block; font-size:0.72rem; color:#475569; margin-bottom:0.3rem; text-transform:uppercase;">Nombre Oficial de Cumplimiento</label>
                                    <input type="text" name="nombre_oficial" value="<?= htmlspecialchars($nombreOficialValue) ?>" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.82rem;">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($related_forms)): ?>
                        <div style="margin-bottom:1rem; padding:0.8rem; background:#eff6ff; border:1px solid #93c5fd; border-radius:0.25rem;">
                            <label style="display:block; font-size:0.75rem; color:#1d4ed8; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.05em;">Marcar formulario anterior como corregido (opcional)</label>
                            <select name="mark_as_corrected_id" style="width:100%; padding:0.6rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.85rem;">
                                <option value="">No marcar ninguno</option>
                                <?php foreach ($related_forms as $relatedForm): ?>
                                <option value="<?= (int)$relatedForm['id'] ?>">
                                    #<?= (int)$relatedForm['id'] ?> - <?= htmlspecialchars(date('d/m/Y', strtotime($relatedForm['created_at'] ?? $relatedForm['approval_date'] ?? 'now'))) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; font-size:0.75rem; color:#475569; margin-bottom:0.35rem; text-transform:uppercase; letter-spacing:0.05em;">Observaciones</label>
                            <textarea name="observations" rows="4" placeholder="" style="width:100%; padding:0.75rem; background:#ffffff; border:1px solid #cbd5e1; border-radius:0.25rem; color:#0f172a; font-size:0.85rem; resize:vertical;"></textarea>
                        </div>

                        <div style="display:flex; gap:0.75rem; justify-content:flex-end; flex-wrap:wrap;">
                            <button type="submit" name="decision" value="rejected" style="background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; padding:0.6rem 1rem; border-radius:0.25rem; font-size:0.85rem; font-weight:700;">
                                Rechazar
                            </button>
                            <button type="submit" name="decision" value="approved" style="background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:0.6rem 1rem; border-radius:0.25rem; font-size:0.85rem; font-weight:700;">
                                Aprobar
                            </button>
                        </div>
                    </form>
                    <div id="approvalMessage" style="margin-top:0.8rem; display:none; font-size:0.85rem;"></div>
                </div>

            </div><!-- /step2 -->
        </div><!-- /container -->
    </main>
</div><!-- /app -->

<script>
(function () {
    const loginForm = document.getElementById('reviewerLoginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const message = document.getElementById('loginMessage');
            const formData = new FormData(loginForm);
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Validando...';
            try {
                const resp = await fetch('index.php?route=/reviewer/login', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await resp.json();
                if (!data.success) throw new Error(data.error || 'No fue posible iniciar sesión');
                location.reload();
            } catch (err) {
                message.style.color = '#fca5a5';
                message.textContent = err.message || 'Error de autenticación';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Iniciar Sesión';
            }
        });
    }

    const approvalForm = document.getElementById('approvalForm');
    if (!approvalForm) return;

    approvalForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const message = document.getElementById('approvalMessage');
        const submitter = e.submitter;
        const decision = submitter ? submitter.value : '';
        const fd = new FormData(approvalForm);
        if (decision) fd.set('decision', decision);

        const buttons = approvalForm.querySelectorAll('button[type="submit"]');
        buttons.forEach(b => b.disabled = true);
        message.style.display = 'none';

        try {
            const resp = await fetch(approvalForm.action, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();
            if (!data.success) throw new Error(data.error || 'No fue posible procesar la decisión');

            message.style.display = 'block';
            message.style.color = '#86efac';
            message.textContent = data.message || 'Formulario procesado correctamente';

            setTimeout(() => {
                const returnUrl = approvalForm.getAttribute('data-return-url') || 'index.php?route=/reviewer/dashboard';
                window.location.href = returnUrl;
            }, 900);
        } catch (err) {
            message.style.display = 'block';
            message.style.color = '#fca5a5';
            message.textContent = err.message || 'Error al procesar la decisión';
            buttons.forEach(b => b.disabled = false);
        }
    });
})();
</script>
</body>
</html>
