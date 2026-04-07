<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <style>
        body {
            padding: 20px;
        }
        .container {
            background: var(--bg-card);
            border: 1px solid var(--border-accent);
            border-radius: var(--radius-lg);
            padding: 32px;
            max-width: 1000px;
            margin: 0 auto;
            box-shadow: var(--shadow-lg);
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-secondary);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .header-logo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: contain;
        }
        .header h1 {
            color: var(--text-primary);
            font-size: 24px;
            margin-bottom: 6px;
            font-weight: 700;
        }
        .header p {
            color: var(--text-muted);
            font-size: 13px;
        }
        
        /* Secciones */
        .section {
            background: var(--bg-secondary);
            border: 1px solid var(--border-secondary);
            border-radius: var(--radius-sm);
            padding: 20px;
            margin-bottom: 20px;
        }
        .section-title {
            color: var(--text-primary);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-secondary);
        }
        
        /* Formulario */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .form-row.three-cols {
            grid-template-columns: 1fr 1fr 1fr;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        label {
            display: block;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        label .required {
            color: var(--error);
            margin-left: 2px;
        }
        label .hint {
            color: var(--text-placeholder);
            font-weight: 400;
            font-size: 11px;
            margin-left: 4px;
        }
        
        /* Ocultar spinners de inputs numéricos */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
        
        /* File upload */
        .file-upload-wrapper {
            position: relative;
        }
        input[type="file"]::file-selector-button {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            border: 1px solid var(--border-primary);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            margin-right: 10px;
        }
        .file-hint {
            color: var(--text-placeholder);
            font-size: 11px;
            margin-top: 6px;
        }
        .file-list {
            background: var(--bg-secondary);
            border: 1px solid var(--border-secondary);
            border-radius: var(--radius-sm);
            padding: 12px;
            margin-top: 10px;
        }
        .file-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .file-list-title {
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 600;
        }
        .file-list-items {
            list-style: none;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            background: var(--bg-tertiary);
            border-radius: var(--radius-sm);
            margin-bottom: 6px;
            gap: 10px;
        }
        .file-item:last-child {
            margin-bottom: 0;
        }
        .file-name {
            color: var(--text-secondary);
            font-size: 12px;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Botones */
        .btn-small {
            padding: 6px 10px;
            font-size: 11px;
        }
        
        /* Campos condicionales */
        .conditional {
            display: none;
        }
        
        @media (max-width: 768px) {
            .form-row, .form-row.three-cols {
                grid-template-columns: 1fr;
            }
            .container {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=4" alt="Logo" class="header-logo">
            <h1>Formulario SAGRILAFT</h1>
            <p><?= $temp_data['person_type'] === 'natural' ? 'Persona Natural' : 'Persona Jurídica' ?> - <?= ucfirst($temp_data['role'] ?? 'Usuario') ?></p>
        </div>

        <form id="formCreate" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="user_type" value="<?= $temp_data['role'] ?>">
            <input type="hidden" name="person_type" value="<?= $temp_data['person_type'] ?>">
            
            <!-- SECCIÓN 1: DATOS GENERALES -->
            <div class="section">
                <div class="section-title">1. Datos Generales</div>
                
                <div class="form-group full-width">
                    <label><?= $temp_data['person_type'] === 'natural' ? 'Nombre Completo' : 'Razón Social' ?> <span class="required">*</span></label>
                    <input type="text" name="razon_social" required value="<?= htmlspecialchars($temp_data['company_name']) ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>NIT / Documento <span class="required">*</span></label>
                        <input type="text" name="nit" required value="<?= htmlspecialchars($temp_data['document_number']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Teléfono <span class="required">*</span></label>
                        <input type="tel" name="telefono" required value="<?= htmlspecialchars($temp_data['phone']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($temp_data['email']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Celular</label>
                        <input type="tel" name="celular" placeholder="Ingrese número de celular">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Dirección <span class="required">*</span></label>
                    <input type="text" 
                           id="direccion-input" 
                           name="direccion" 
                           required 
                           placeholder="Ej: Calle 123 #45-67"
                           autocomplete="off">
                </div>

                <div class="form-group full-width">
                    <label>Ciudad <span class="required">*</span></label>
                    <input type="text" id="ciudad-input" name="ciudad" required placeholder="Ciudad">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Barrio</label>
                        <input type="text" name="barrio" placeholder="Barrio o localidad">
                    </div>

                    <div class="form-group">
                        <label>País</label>
                        <input type="text" name="pais" value="Colombia" placeholder="País">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: ACTIVIDAD ECONÓMICA -->
            <div class="section">
                <div class="section-title">2. Actividad Económica</div>
                
                <div class="form-group full-width">
                    <label>Código CIIU <span class="required">*</span></label>
                    <select name="codigo_ciiu" id="codigoCiiu" required>
                        <option value="">Seleccione una actividad económica...</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Descripción de la Actividad <span class="required">*</span></label>
                    <textarea name="actividad_economica" required placeholder="Describa brevemente la actividad económica principal"></textarea>
                </div>
            </div>

            <!-- SECCIÓN 3: INFORMACIÓN FINANCIERA -->
            <div class="section">
                <div class="section-title">3. Información Financiera</div>
                
                <div class="form-row three-cols">
                    <div class="form-group">
                        <label>Activos <span class="hint">(COP)</span></label>
                        <input type="number" name="activos" step="0.01" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>Pasivos <span class="hint">(COP)</span></label>
                        <input type="number" name="pasivos" step="0.01" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>Patrimonio <span class="hint">(COP)</span></label>
                        <input type="number" name="patrimonio" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ingresos Mensuales <span class="hint">(COP)</span></label>
                        <input type="number" name="ingresos" step="0.01" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>Gastos Mensuales <span class="hint">(COP)</span></label>
                        <input type="number" name="gastos" step="0.01" placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 4: REPRESENTANTE LEGAL (Solo Jurídica) -->
            <div class="section conditional" id="seccionRepresentante">
                <div class="section-title">4. Representante Legal</div>
                
                <div class="form-group full-width">
                    <label>Nombre Completo <span class="required">*</span></label>
                    <input type="text" name="representante_nombre" placeholder="Nombre del representante legal">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo de Documento</label>
                        <select name="representante_tipo_doc">
                            <option value="">Seleccione...</option>
                            <option value="cc">Cédula de Ciudadanía</option>
                            <option value="ce">Cédula de Extranjería</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de Documento</label>
                        <input type="text" name="representante_documento" placeholder="Número de documento">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Profesión</label>
                        <input type="text" name="representante_profesion" placeholder="Profesión u oficio">
                    </div>

                    <div class="form-group">
                        <label>Fecha de Nacimiento</label>
                        <input type="date" name="representante_nacimiento">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 5: DECLARACIÓN ORIGEN DE FONDOS -->
            <div class="section">
                <div class="section-title">5. Declaración de Origen de Fondos</div>
                
                <div class="form-group full-width">
                    <label>Origen de los Fondos <span class="required">*</span></label>
                    <textarea name="origen_fondos" required placeholder="Describa el origen de los recursos que generan el vínculo comercial"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>¿Es Persona Expuesta Políticamente (PEP)? <span class="required">*</span></label>
                        <select name="es_pep" id="esPep" required>
                            <option value="">Seleccione...</option>
                            <option value="no">No</option>
                            <option value="si">Sí</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>¿Tiene cuentas en el exterior?</label>
                        <select name="tiene_cuentas_exterior" id="tieneCuentasExterior">
                            <option value="">Seleccione...</option>
                            <option value="no">No</option>
                            <option value="si">Sí</option>
                        </select>
                    </div>
                </div>

                <!-- Campos condicionales PEP -->
                <div class="conditional" id="camposPep">
                    <div class="form-group full-width">
                        <label>Cargo o Relación con PEP</label>
                        <input type="text" name="cargo_pep" placeholder="Especifique el cargo o relación">
                    </div>
                </div>

                <!-- Campos condicionales Cuentas Exterior -->
                <div class="conditional" id="camposCuentasExterior">
                    <div class="form-group full-width">
                        <label>País de las Cuentas</label>
                        <input type="text" name="pais_cuentas_exterior" placeholder="País donde tiene las cuentas">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 6: DOCUMENTOS ADJUNTOS -->
            <div class="section">
                <div class="section-title">6. Documentos Adjuntos <span class="required">*</span></div>
                
                <div style="background: #eff6ff; border-left: 3px solid #3b82f6; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
                    <p style="color: #1d4ed8; font-size: 12px; margin: 0; line-height: 1.5;">
                        <strong>Documentos requeridos:</strong>
                    </p>
                    <ul style="color: #bfdbfe; font-size: 11px; margin: 8px 0 0 20px; line-height: 1.6;">
                        <li><strong>RUT</strong></li>
                        <li id="doc-juridica" style="display: none;"><strong>Cámara de Comercio</strong> (Persona Jurídica)</li>
                        <li id="doc-juridica-2" style="display: none;"><strong>Composición Accionaria</strong> (Persona Jurídica)</li>
                        <li id="doc-natural" style="display: none;"><strong>Cédula</strong> (Persona Natural)</li>
                        <li id="doc-natural-2" style="display: none;"><strong>Certificación Bancaria</strong> no superior a 3 meses (Persona Natural)</li>
                    </ul>
                </div>
                
                <div class="form-group full-width">
                    <label>Adjuntar Documentos <span class="required">*</span></label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="documents" name="documents[]" multiple accept=".pdf" required>
                        <p class="file-hint">Solo archivos PDF. Máximo 10MB por archivo.</p>
                    </div>
                    <div id="fileList" class="file-list" style="display: none;">
                        <div class="file-list-header">
                            <span class="file-list-title">Archivos seleccionados (<span id="fileCount">0</span>)</span>
                            <button type="button" onclick="clearAllFiles()" class="btn btn-small btn-danger">
                                Limpiar todos
                            </button>
                        </div>
                        <ul id="fileListItems" class="file-list-items"></ul>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Enviar Formulario para Revisión →
            </button>
        </form>

        <div id="message" class="message"></div>
    </div>


    <script>
        // Datos temporales del usuario
        const userType = '<?= $temp_data['role'] ?? 'cliente' ?>';
        const personType = '<?= $temp_data['person_type'] ?? 'natural' ?>';

        // Mostrar/ocultar secciones según tipo de persona
        if (personType === 'juridica') {
            document.getElementById('seccionRepresentante').style.display = 'block';
            document.getElementById('seccionRepresentante').querySelectorAll('input[name="representante_nombre"]').forEach(input => {
                input.required = true;
            });
            // Mostrar documentos para jurídica
            document.getElementById('doc-juridica').style.display = 'list-item';
            document.getElementById('doc-juridica-2').style.display = 'list-item';
        } else {
            // Mostrar documentos para natural
            document.getElementById('doc-natural').style.display = 'list-item';
            document.getElementById('doc-natural-2').style.display = 'list-item';
        }

        // Cargar actividades económicas
        fetch('/api/actividades-economicas')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('codigoCiiu');
                data.forEach(act => {
                    const option = document.createElement('option');
                    option.value = act.codigo;
                    option.textContent = `${act.codigo} - ${act.descripcion}`;
                    select.appendChild(option);
                });
            })
            .catch(err => console.error('Error cargando actividades:', err));

        // Mostrar campos condicionales PEP
        document.getElementById('esPep').addEventListener('change', function() {
            const camposPep = document.getElementById('camposPep');
            if (this.value === 'si') {
                camposPep.style.display = 'block';
            } else {
                camposPep.style.display = 'none';
            }
        });

        // Mostrar campos condicionales Cuentas Exterior
        document.getElementById('tieneCuentasExterior').addEventListener('change', function() {
            const camposCuentas = document.getElementById('camposCuentasExterior');
            if (this.value === 'si') {
                camposCuentas.style.display = 'block';
            } else {
                camposCuentas.style.display = 'none';
            }
        });

        // Gestión de archivos
        let selectedFiles = [];

        document.getElementById('documents').addEventListener('change', function(e) {
            const newFiles = Array.from(this.files);
            newFiles.forEach(file => {
                const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    selectedFiles.push(file);
                }
            });
            updateFileList();
            this.value = '';
        });

        function updateFileList() {
            const fileList = document.getElementById('fileList');
            const fileListItems = document.getElementById('fileListItems');
            const fileCount = document.getElementById('fileCount');
            
            if (selectedFiles.length > 0) {
                fileList.style.display = 'block';
                fileCount.textContent = selectedFiles.length;
                fileListItems.innerHTML = '';
                
                selectedFiles.forEach((file, index) => {
                    const li = document.createElement('li');
                    li.className = 'file-item';
                    const size = (file.size / 1024 / 1024).toFixed(2);
                    li.innerHTML = `
                        <span class="file-name" title="${file.name}">${file.name} (${size} MB)</span>
                        <button type="button" onclick="removeFile(${index})" class="btn btn-small btn-danger">
                            Quitar
                        </button>
                    `;
                    fileListItems.appendChild(li);
                });
            } else {
                fileList.style.display = 'none';
            }
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileList();
        }

        function clearAllFiles() {
            if (confirm('¿Estás seguro de que quieres quitar todos los archivos?')) {
                selectedFiles = [];
                updateFileList();
            }
        }

        // Envío del formulario
        document.getElementById('formCreate').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Agregar archivos
            selectedFiles.forEach((file, index) => {
                formData.append(`document_${index}`, file);
            });
            formData.append('file_count', selectedFiles.length);
            
            const messageDiv = document.getElementById('message');
            const submitButton = this.querySelector('button[type="submit"]');
            messageDiv.style.display = 'none';
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';
            
            // Crear barra de progreso
            const progressContainer = document.createElement('div');
            progressContainer.style.cssText = 'margin-top: 16px; background: rgba(241, 245, 249, 0.8); border-radius: 8px; padding: 16px; border: 1px solid rgba(71, 85, 105, 0.3);';
            progressContainer.innerHTML = `
                <div style="color: #1e293b; font-size: 13px; margin-bottom: 8px; text-align: center;">
                    <span id="progressText">Subiendo archivos...</span>
                </div>
                <div style="background: rgba(226, 232, 240, 0.8); border-radius: 4px; height: 8px; overflow: hidden;">
                    <div id="progressBar" style="background: linear-gradient(90deg, #3b82f6, #60a5fa); height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <div style="color: #94a3b8; font-size: 11px; margin-top: 6px; text-align: center;">
                    <span id="progressPercent">0%</span>
                </div>
            `;
            submitButton.parentNode.insertBefore(progressContainer, submitButton.nextSibling);
            
            try {
                const xhr = new XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        document.getElementById('progressBar').style.width = percent + '%';
                        document.getElementById('progressPercent').textContent = percent + '%';
                        
                        if (percent < 100) {
                            document.getElementById('progressText').textContent = 'Subiendo archivos...';
                        } else {
                            document.getElementById('progressText').textContent = 'Procesando formulario...';
                        }
                    }
                });
                
                const uploadPromise = new Promise((resolve, reject) => {
                    xhr.onload = () => {
                        if (xhr.status === 403) {
                            reject(new Error('Error de permisos. Por favor recarga la página e intenta de nuevo.'));
                        } else if (xhr.status >= 400) {
                            reject(new Error(`Error del servidor (${xhr.status})`));
                        } else {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                resolve(data);
                            } catch (e) {
                                console.error('Respuesta del servidor:', xhr.responseText.substring(0, 500));
                                reject(new Error('Error al procesar la respuesta del servidor. Revisa la consola para más detalles.'));
                            }
                        }
                    };
                    
                    xhr.onerror = () => reject(new Error('Error de conexión'));
                });
                
                xhr.open('POST', '/form/store');
                xhr.send(formData);
                
                const data = await uploadPromise;
                
                if (data.success) {
                    document.getElementById('progressText').textContent = 'Formulario enviado correctamente';
                    document.getElementById('progressBar').style.background = 'linear-gradient(90deg, #10b981, #34d399)';
                    
                    messageDiv.className = 'message success';
                    messageDiv.style.display = 'block';
                    messageDiv.textContent = data.message;
                    
                    setTimeout(() => {
                        window.location.href = '/form/success';
                    }, 2000);
                } else {
                    throw new Error(data.error || 'Error al enviar el formulario');
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
                messageDiv.textContent = error.message;
                submitButton.disabled = false;
                submitButton.textContent = 'Enviar Formulario para Revisión →';
                
                const progressContainer = document.querySelector('[id="progressText"]')?.closest('div').parentElement;
                if (progressContainer) progressContainer.remove();
            }
        });
    </script>
    
    <!-- Sistema de búsqueda de direcciones -->
    <script src="/gestion-sagrilaft/public/assets/js/map-location-picker.js"></script>
    <script>
        initMapLocationPicker('direccion-input', 'ciudad-input');
    </script>
</body>
</html>
