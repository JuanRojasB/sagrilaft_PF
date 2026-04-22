<?php
/**
 * Componente: Pad de Firma Digital
 * 
 * Permite a los usuarios dibujar su firma directamente en el navegador
 * o subir una imagen de su firma.
 * 
 * Parámetros:
 * - $field_name: Nombre del campo (default: 'signature_data')
 * - $required: Si es obligatorio (default: false)
 * - $label: Etiqueta del campo (default: 'Firma Digital')
 */

$field_name = $field_name ?? 'signature_data';
$required = $required ?? false;
$label = $label ?? 'Firma Digital';
$existing_signature = $existing_signature ?? '';
?>

<div class="signature-section" style="margin-bottom: 1.5rem;">
    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
        <?= htmlspecialchars($label) ?>
        <?php if ($required): ?>
            <span style="color: #dc2626;">*</span>
        <?php endif; ?>
    </label>
    
    <div class="signature-container" style="border: 1px solid #d1d5db; border-radius: 0.5rem; background: #ffffff;">
        <!-- Tabs -->
        <div class="signature-tabs" style="display: flex; border-bottom: 1px solid #e5e7eb;">
            <button type="button" class="tab-btn active" data-tab="draw" style="flex: 1; padding: 0.75rem; border: none; background: #f9fafb; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                Dibujar Firma
            </button>
            <button type="button" class="tab-btn" data-tab="upload" style="flex: 1; padding: 0.75rem; border: none; background: #ffffff; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                Subir Imagen
            </button>
        </div>

        <!-- Tab: Dibujar -->
        <div class="tab-content" id="tab-draw" style="padding: 1rem;">
            <canvas id="signature-canvas-<?= $field_name ?>" width="400" height="150" style="border: 1px dashed #d1d5db; border-radius: 0.25rem; cursor: crosshair; display: block; margin: 0 auto; max-width: 100%;"></canvas>
            <div style="text-align: center; margin-top: 0.75rem; display: flex; gap: 0.5rem; justify-content: center;">
                <button type="button" onclick="clearSignature('<?= $field_name ?>')" style="padding: 0.5rem 1rem; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 0.25rem; font-size: 0.875rem; cursor: pointer;">
                    Limpiar
                </button>
                <button type="button" onclick="saveSignature('<?= $field_name ?>')" style="padding: 0.5rem 1rem; background: #1d4ed8; color: #ffffff; border: none; border-radius: 0.25rem; font-size: 0.875rem; cursor: pointer;">
                    Guardar Firma
                </button>
            </div>
        </div>

        <!-- Tab: Subir -->
        <div class="tab-content" id="tab-upload" style="display: none; padding: 1rem;">
            <div class="upload-area" style="border: 2px dashed #d1d5db; border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer;" onclick="document.getElementById('signature-file-<?= $field_name ?>').click()">
                <svg style="width: 48px; height: 48px; margin: 0 auto 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <p style="margin: 0 0 0.5rem; font-weight: 600; color: #374151;">Haz clic para seleccionar una imagen</p>
                <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">PNG o JPG, máximo 2MB</p>
            </div>
            <input type="file" id="signature-file-<?= $field_name ?>" accept="image/png,image/jpeg,image/jpg" style="display: none;" onchange="handleFileUpload('<?= $field_name ?>', this)">
        </div>

        <!-- Preview -->
        <div class="signature-preview" id="signature-preview-<?= $field_name ?>" style="display: none; padding: 1rem; border-top: 1px solid #e5e7eb; text-align: center;">
            <p style="margin: 0 0 0.5rem; font-size: 0.875rem; color: #374151; font-weight: 500;">Vista Previa:</p>
            <img id="signature-image-<?= $field_name ?>" style="max-width: 300px; max-height: 100px; border: 1px solid #e5e7eb; border-radius: 0.25rem;">
            <div style="margin-top: 0.5rem;">
                <button type="button" onclick="removeSignature('<?= $field_name ?>')" style="padding: 0.25rem 0.75rem; background: #dc2626; color: #ffffff; border: none; border-radius: 0.25rem; font-size: 0.75rem; cursor: pointer;">
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Campo oculto para almacenar la firma -->
    <input type="hidden" id="<?= $field_name ?>" name="<?= $field_name ?>" value="<?= htmlspecialchars($existing_signature) ?>">
</div>

<script>
(function() {
    const fieldName = '<?= $field_name ?>';
    const canvas = document.getElementById('signature-canvas-' + fieldName);
    const ctx = canvas.getContext('2d');
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

    // Configurar canvas
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    // Eventos de dibujo
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Eventos táctiles
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);

    function startDrawing(e) {
        isDrawing = true;
        const rect = canvas.getBoundingClientRect();
        lastX = e.clientX - rect.left;
        lastY = e.clientY - rect.top;
    }

    function draw(e) {
        if (!isDrawing) return;
        
        const rect = canvas.getBoundingClientRect();
        const currentX = e.clientX - rect.left;
        const currentY = e.clientY - rect.top;

        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(currentX, currentY);
        ctx.stroke();

        lastX = currentX;
        lastY = currentY;
    }

    function stopDrawing() {
        isDrawing = false;
    }

    function handleTouch(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                        e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }

    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            
            // Actualizar botones
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active');
                b.style.background = '#ffffff';
            });
            this.classList.add('active');
            this.style.background = '#f9fafb';

            // Mostrar contenido
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            document.getElementById('tab-' + tab).style.display = 'block';
        });
    });

    // Cargar firma existente si existe
    const existingSignature = '<?= $existing_signature ?>';
    if (existingSignature) {
        showSignaturePreview(fieldName, existingSignature);
    }
})();

// Funciones globales
function clearSignature(fieldName) {
    const canvas = document.getElementById('signature-canvas-' + fieldName);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    removeSignature(fieldName);
}

function saveSignature(fieldName) {
    const canvas = document.getElementById('signature-canvas-' + fieldName);
    const dataURL = canvas.toDataURL('image/png');
    
    // Verificar que hay contenido
    const ctx = canvas.getContext('2d');
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const hasContent = imageData.data.some(channel => channel !== 0);
    
    if (!hasContent) {
        alert('Por favor dibuja tu firma antes de guardar');
        return;
    }

    document.getElementById(fieldName).value = dataURL;
    showSignaturePreview(fieldName, dataURL);
}

function handleFileUpload(fieldName, input) {
    const file = input.files[0];
    if (!file) return;

    // Validar tipo
    if (!['image/png', 'image/jpeg', 'image/jpg'].includes(file.type)) {
        alert('Solo se permiten archivos PNG o JPG');
        return;
    }

    // Validar tamaño
    if (file.size > 2 * 1024 * 1024) {
        alert('El archivo no debe superar 2MB');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const dataURL = e.target.result;
        document.getElementById(fieldName).value = dataURL;
        showSignaturePreview(fieldName, dataURL);
    };
    reader.readAsDataURL(file);
}

function showSignaturePreview(fieldName, dataURL) {
    const preview = document.getElementById('signature-preview-' + fieldName);
    const image = document.getElementById('signature-image-' + fieldName);
    
    image.src = dataURL;
    preview.style.display = 'block';
}

function removeSignature(fieldName) {
    document.getElementById(fieldName).value = '';
    document.getElementById('signature-preview-' + fieldName).style.display = 'none';
    
    // Limpiar canvas
    const canvas = document.getElementById('signature-canvas-' + fieldName);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Limpiar input de archivo
    const fileInput = document.getElementById('signature-file-' + fieldName);
    if (fileInput) fileInput.value = '';
}
</script>

<style>
.signature-section .tab-btn.active {
    background: #f9fafb !important;
    border-bottom: 2px solid #1d4ed8;
}

.signature-section .upload-area:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}

.signature-section canvas {
    touch-action: none;
}

@media (max-width: 640px) {
    .signature-section canvas {
        width: 100%;
        height: auto;
    }
}
</style>