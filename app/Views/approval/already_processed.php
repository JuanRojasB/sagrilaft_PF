<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Procesado - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/font-scale-enhanced.css">
    <style>
        body { background: #f8fafc; color: #0f172a; }
        .app-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 1.25rem; display: flex; justify-content: space-between; align-items: center; }
        .brand { display: flex; align-items: center; gap: 0.75rem; }
        .header-actions { display: flex; gap: 0.5rem; }
        .btn-header { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-weight: 600; font-size: 0.8rem; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap; }
        .btn-header:hover { background: #e2e8f0; border-color: #94a3b8; }
        .app-main { padding: 0; }
        .page-wrap { max-width: 700px; margin: 0 auto; padding: 1rem; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .card-center { text-align: center; padding: 2rem; margin: 2rem 0; }
        .status-icon { font-size: 3rem; font-weight: 700; margin-bottom: 1rem; }
        .card h2 { margin: 0 0 0.5rem; font-size: 1.5rem; color: #0f172a; font-weight: 600; }
        .card p { margin: 0 0 1.5rem; font-size: 0.9rem; color: #475569; }
        .badge-status { padding: 0.75rem 2rem; border-radius: 0.25rem; font-size: 1rem; font-weight: 600; display: inline-block; }
        .badge-approved { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-pending { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .badge-corrected { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-rejected { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .btn-link { padding: 0.75rem 1.5rem; border-radius: 0.25rem; font-size: 0.9rem; font-weight: 600; display: inline-block; text-decoration: none; margin-top: 1rem; transition: all 0.15s; }
        .btn-blue { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
        .btn-blue:hover { background: #bfdbfe; }
        .pdf-card { background: #eff6ff; border: 2px solid #93c5fd; border-radius: 0.5rem; padding: 1.25rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .pdf-card h3 { margin: 0 0 0.4rem; font-size: 0.95rem; color: #1d4ed8; font-weight: 700; }
        .pdf-card p { margin: 0; font-size: 0.85rem; color: #3b82f6; line-height: 1.5; }
        .btn-pdf { background: #1d4ed8; color: #ffffff; border: none; padding: 0.75rem 1.5rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.9rem; font-weight: 700; white-space: nowrap; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-pdf:hover { background: #1e40af; transform: translateY(-1px); }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .info-label { font-size: 0.7rem; color: #64748b; margin-bottom: 0.3rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .info-value { font-size: 0.85rem; color: #0f172a; font-weight: 500; }
        .section-title { font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 1rem; }
        .obs-card-rejected { background: #fee2e2; border: 1px solid #fca5a5; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1rem; }
        .obs-card-pending  { background: #fef9c3; border: 1px solid #fde047; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1rem; }
        .obs-card-info     { background: #dbeafe; border: 1px solid #93c5fd; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1rem; }
        .obs-title-rejected { color: #dc2626; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin: 0 0 0.75rem; }
        .obs-title-pending  { color: #a16207; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin: 0 0 0.75rem; }
        .obs-title-info     { color: #1d4ed8; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin: 0 0 0.75rem; }
        .obs-text { background: #ffffff; border-radius: 0.25rem; padding: 1rem; font-size: 0.85rem; line-height: 1.6; color: #0f172a; }
    </style>
</head>
<body>
    <div class="app">
        <header class="app-header">
            <div class="brand">
                <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=4" alt="Logo" style="width: 32px; height: 32px;">
                <h1 style="font-size: 1.1rem; margin: 0; color: #0f172a;">SAGRILAFT</h1>
            </div>
            <?php if (isset($_SESSION['reviewer_id'])): ?>
            <div class="header-actions">
                <a href="index.php?route=/reviewer/dashboard" class="btn-header">← Volver</a>
            </div>
            <?php endif; ?>
        </header>

        <main class="app-main">
            <div class="page-wrap">
                <?php
                $isApproved       = ($form['approval_status'] === 'approved');
                $isApprovedPending= ($form['approval_status'] === 'approved_pending');
                $isRejected       = ($form['approval_status'] === 'rejected');
                $isCorrected      = ($form['approval_status'] === 'corrected');
                $isEmpleado       = isset($form['form_type']) && $form['form_type'] === 'empleado';
                
                // Para empleados NO existe "approved_pending", solo "approved" o "rejected"
                // Si por algún error quedó como "approved_pending", tratarlo como "approved"
                if ($isEmpleado && $isApprovedPending) {
                    $isApproved = true;
                    $isApprovedPending = false;
                }
                ?>

                <!-- Estado principal -->
                <div class="card card-center">
                    <?php if ($isApproved): ?>
                        <div class="status-icon" style="color:#15803d;">✓</div>
                    <?php elseif ($isRejected): ?>
                        <div class="status-icon" style="color:#dc2626;">✗</div>
                    <?php elseif ($isApprovedPending): ?>
                        <div class="status-icon" style="color:#a16207;">!</div>
                    <?php else: ?>
                        <div class="status-icon" style="color:#475569;">↺</div>
                    <?php endif; ?>

                    <h2>Formulario Ya Procesado</h2>
                    <p>Este formulario ya fue revisado anteriormente</p>

                    <?php if ($isApproved): ?>
                        <span class="badge-status badge-approved">APROBADO</span>

                    <?php elseif ($isApprovedPending): ?>
                        <span class="badge-status badge-pending">APROBADO CON OBSERVACIONES</span>
                        <p style="margin-top:1rem; color:#a16207; font-size:0.85rem;">El usuario debe corregir los puntos señalados y volver a enviar el formulario</p>

                    <?php elseif ($isCorrected): ?>
                        <span class="badge-status badge-corrected">CORREGIDO</span>
                        <p style="margin-top:1rem; color:#475569; font-size:0.85rem;">Este formulario fue corregido por uno posterior</p>
                        <?php if (!empty($form['corrected_by_form_id'])): ?>
                        <?php
                        $db = \App\Core\Database::getConnection();
                        $stmt = $db->prepare("SELECT approval_token FROM forms WHERE id = ?");
                        $stmt->execute([$form['corrected_by_form_id']]);
                        $correctedByToken = $stmt->fetchColumn();
                        ?>
                        <br><a href="index.php?route=/approval/<?= $correctedByToken ?>" class="btn-link btn-blue">Ver formulario actualizado #<?= $form['corrected_by_form_id'] ?> →</a>
                        <?php endif; ?>

                    <?php else: ?>
                        <span class="badge-status badge-rejected">RECHAZADO</span>
                    <?php endif; ?>
                </div>

                <!-- Ver / Descargar PDF completo o adjuntos -->
                <?php if (!$isEmpleado): ?>
                    <?php
                    // Para formularios SAGRILAFT: mostrar PDF consolidado
                    $db2   = \App\Core\Database::getConnection();
                    $stmtC = $db2->prepare("SELECT id, signed FROM form_consolidated_pdfs WHERE form_id = ? ORDER BY signed DESC, id DESC LIMIT 1");
                    $stmtC->execute([$form['id']]);
                    $consolidatedPdf = $stmtC->fetch();
                    ?>
                    <div class="pdf-card">
                        <div style="flex:1;">
                            <h3>Formulario Completo</h3>
                            <p>
                                <?php if ($isApproved && $consolidatedPdf): ?>
                                    PDF consolidado con firma del oficial de cumplimiento.
                                <?php else: ?>
                                    Formulario completo con todos los datos y documentos adjuntos.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap; justify-content:flex-end;">
                            <a href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/view-pdf.php?id=<?= $form['id'] ?>" target="_blank" class="btn-pdf">👁 Ver PDF</a>
                            <?php if ($isApproved && $consolidatedPdf): ?>
                                <a href="/gestion-sagrilaft/public/index.php?route=/forms/consolidated/<?= $consolidatedPdf['id'] ?>/download" class="btn-pdf" style="background:#15803d;">⬇ Descargar</a>
                            <?php elseif ($isApproved): ?>
                                <a href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/view-pdf.php?id=<?= $form['id'] ?>&download=1" target="_blank" class="btn-pdf" style="background:#15803d;">⬇ Descargar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <?php
                    // Para empleados: mostrar solo los adjuntos (PDF de cédula)
                    $db2 = \App\Core\Database::getConnection();
                    $stmtAttach = $db2->prepare("SELECT id, filename FROM form_attachments WHERE form_id = ? ORDER BY id");
                    $stmtAttach->execute([$form['id']]);
                    $attachments = $stmtAttach->fetchAll();
                    ?>
                    <?php if (!empty($attachments)): ?>
                        <div class="pdf-card">
                            <div style="flex:1;">
                                <h3>Documentos Adjuntos</h3>
                                <p>Cédula y documentos del empleado</p>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:0.5rem;">
                                <?php foreach ($attachments as $attachment): ?>
                                    <a href="/gestion-sagrilaft/public/index.php?route=/reviewer/attachment/<?= $attachment['id'] ?>" target="_blank" class="btn-pdf">
                                        <?= htmlspecialchars($attachment['filename']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card" style="text-align:center; padding:2rem; color:#64748b;">
                            <p>Este registro de empleado no tiene documentos adjuntos.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Info del formulario -->
                <div class="card">
                    <p class="section-title">Información del Formulario</p>
                    <?php
                    $formTypeCodeMap = [
                        'cliente_natural'              => 'FD-08',
                        'cliente_juridica'             => 'FD-16',
                        'declaracion_fondos_clientes'  => 'FD-17',
                        'declaracion_cliente'          => 'FD-17',
                        'proveedor_natural'            => 'FD-05',
                        'proveedor_juridica'           => 'FD-02',
                        'proveedor_internacional'      => 'FD-04',
                        'declaracion_fondos_proveedores'=> 'FD-03',
                        'declaracion_proveedor'        => 'FD-03',
                        'empleado'                     => 'FD-09',
                    ];
                    $formCode = $formTypeCodeMap[$form['form_type'] ?? ''] ?? 'N/A';
                    ?>
                    <div class="info-grid">
                        <div><div class="info-label">ID</div><div class="info-value">#<?= $form['id'] ?></div></div>
                        <div><div class="info-label">Fecha de Revisión</div><div class="info-value"><?= date('d/m/Y H:i', strtotime($form['approval_date'])) ?></div></div>
                        <div><div class="info-label">Nomenclatura</div><div class="info-value"><?= htmlspecialchars($formCode) ?></div></div>
                        <div style="grid-column:1/-1;"><div class="info-label">Título</div><div class="info-value"><?= htmlspecialchars($form['title']) ?></div></div>
                        <div style="grid-column:1/-1;"><div class="info-label">Revisado por</div><div class="info-value"><?= htmlspecialchars($form['approved_by']) ?></div></div>
                    </div>
                </div>

                <!-- Observaciones -->
                <?php if (!empty($form['reviewer_comments']) || !empty($form['approval_observations'])): ?>
                <div class="<?= $isRejected ? 'obs-card-rejected' : ($isApprovedPending ? 'obs-card-pending' : 'obs-card-info') ?>">
                    <p class="<?= $isRejected ? 'obs-title-rejected' : ($isApprovedPending ? 'obs-title-pending' : 'obs-title-info') ?>">
                        <?= $isRejected ? 'Motivo del Rechazo' : ($isApprovedPending ? 'Observaciones — Requiere Correcciones' : 'Observaciones del Revisor') ?>
                    </p>
                    <div class="obs-text">
                        <?= nl2br(htmlspecialchars($form['reviewer_comments'] ?? $form['approval_observations'] ?? '')) ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
