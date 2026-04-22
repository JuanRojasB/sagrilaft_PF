<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Firma Digital - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <style>
        body { background: #f8fafc; color: #0f172a; }
        .container { max-width: 600px; margin: 2rem auto; padding: 1rem; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .btn { padding: 0.75rem 1.5rem; border-radius: 0.25rem; font-weight: 600; text-decoration: none; display: inline-block; cursor: pointer; border: none; transition: all 0.15s; }
        .btn-primary { background: #1d4ed8; color: #ffffff; }
        .btn-primary:hover { background: #1e40af; }
        .btn-danger { background: #dc2626; color: #ffffff; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: #6b7280; color: #ffffff; }
        .btn-secondary:hover { background: #4b5563; }
        .upload-area { border: 2px dashed #cbd5e1; border-radius: 0.5rem; padding: 2rem; text-align: center; margin: 1rem 0; transition: all 0.15s; }
        .upload-area:hover { border-color: #3b82f6; background: #f8fafc; }
        .upload-area.dragover { border-color: #1d4ed8; background: #eff6ff; }
        .signature-preview { max-width: 300px; max-height: 150px; border: 1px solid #e2e8f0; border-radius: 0.25rem; margin: 1rem auto; display: block; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; }
        .form-input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.25rem; }
        .alert { padding: 1rem; border-radius: 0.25rem; margin-bottom: 1rem; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .text-center { text-align: center; }
        .text-muted { color: #6b7280; font-size: 0.875rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mt-2 { margin-top: 0.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 style="margin: 0 0 1rem; font-size: 1.5rem; color: #0f172a;">Gestión de Firma Digital</h1>
            
            <?php if ($user_type === 'revisor'): ?>
                <p class="text-muted mb-2">Como revisor, tu firma aparecerá en los formularios aprobados en la sección "Firma del Oficial de Cumplimiento".</p>
            <?php else: ?>
                <p class="text-muted mb-2">Tu firma aparecerá en los formularios que envíes en la sección "Firma del Representante Legal".</p>
            <?php endif; ?>

            <div id="alertContainer"></div>

            <?php if ($firma_actual): ?>
                <!-- Firma actual -->
                <div class="text-center" style="margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem; color: #374151;">Firma Actual</h3>
                    <img src="index.php?route=/signature/view" alt="Firma actual" class="signature-preview">
                    <div class="mt-2">
                        <button onclick="deleteFirma()" class="btn btn-danger">Eliminar Firma</button>
                    </div>
                </div>
                
                <hr style="margin: 2rem 0; border: none; border-top: 1px solid #e5e7eb;">
                
                <h3 style="margin-bottom: 1rem; color: #374151;">Actualizar Firma</h3>
            <?php else: ?>
                <h3 style="margin-bottom: 1rem; color: #374151;">Subir Nueva Firma</h3>
            <?php endif; ?>

            <!-- Formulario de subida -->
            <form id="uploadForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="upload-area" id="uploadArea">
                    <div>
                        <svg style="width: 48px; height: 48px; margin: 0 auto 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p style="margin: 0 0 0.5rem; font-weight: 600;">Arrastra tu firma aquí o haz clic para seleccionar</p>
                        <p class="text-muted">PNG o JPG, máximo 2MB</p>
                        <input type="file" id="firmaFile" name="firma" accept="image/png,image/jpeg,image/jpg" style="display: none;">
                    </div>
                </div>

                <!-- Preview de la nueva firma -->
                <div id="previewContainer" style="display: none; text-align: center; margin: 1rem 0;">
                    <h4 style="margin-bottom: 0.5rem; color: #374151;">Vista Previa</h4>
                    <img id="previewImage" class="signature-preview" alt="Vista previa">
                </div>

                <div class="text-center mt-2">
                    <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                        <?= $firma_actual ? 'Actualizar Firma' : 'Guardar Firma' ?>
                    </button>
                    <a href="<?= $user_type === 'revisor' ? 'index.php?route=/reviewer/dashboard' : 'index.php?route=/forms' ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>

            <div class="mt-2">
                <h4 style="color: #374151; margin-bottom: 0.5rem;">Recomendaciones:</h4>
                <ul class="text-muted" style="font-size: 0.875rem; line-height: 1.5;">
                    <li>Usa una imagen con fondo transparente (PNG) para mejores resultados</li>
                    <li>La firma debe ser clara y legible</li>
                    <li>Tamaño recomendado: 300x150 píxeles aproximadamente</li>
                    <li>Evita fondos de colores o texturas</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('firmaFile');
        const uploadForm = document.getElementById('uploadForm');
        const uploadBtn = document.getElementById('uploadBtn');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const alertContainer = document.getElementById('alertContainer');

        // Drag and drop
        uploadArea.addEventListener('click', () => fileInput.click());
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        // File selection
        fileInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            const file = fileInput.files[0];
            if (!file) return;

            // Validar tipo
            if (!['image/png', 'image/jpeg', 'image/jpg'].includes(file.type)) {
                showAlert('Solo se permiten archivos PNG o JPG', 'error');
                return;
            }

            // Validar tamaño
            if (file.size > 2 * 1024 * 1024) {
                showAlert('El archivo no debe superar 2MB', 'error');
                return;
            }

            // Mostrar preview
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
                uploadBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }

        // Upload form
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!fileInput.files[0]) {
                showAlert('Por favor selecciona un archivo', 'error');
                return;
            }

            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Subiendo...';

            try {
                const formData = new FormData(uploadForm);
                const response = await fetch('index.php?route=/signature/upload', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error al subir la firma', 'error');
            } finally {
                uploadBtn.disabled = false;
                uploadBtn.textContent = '<?= $firma_actual ? 'Actualizar Firma' : 'Guardar Firma' ?>';
            }
        });

        // Delete firma
        async function deleteFirma() {
            if (!confirm('¿Estás seguro de que quieres eliminar tu firma?')) return;

            try {
                const formData = new FormData();
                formData.append('csrf_token', '<?= htmlspecialchars($csrf_token) ?>');
                
                const response = await fetch('index.php?route=/signature/delete', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error al eliminar la firma', 'error');
            }
        }

        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }
    </script>
</body>
</html>