<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAGRILAFT - Registro</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/font-scale-enhanced.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
            min-height: 100vh;
        }
        
        .container {
            background: var(--bg-card);
            border: 1px solid var(--border-accent);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            max-width: 900px;
            width: 100%;
            box-shadow: var(--shadow-lg);
        }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-secondary);
        }
        
        .header-logo {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            display: block;
        }
        
        .header h1 {
            color: var(--text-primary);
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .header p {
            color: var(--text-muted);
            font-size: var(--text-base);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        label {
            display: block;
            color: var(--text-secondary);
            font-size: var(--text-sm);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        label .required {
            color: var(--error);
            margin-left: 2px;
        }
        
        input,
        select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--bg-input);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: var(--text-base);
            transition: all var(--transition-fast);
        }
        
        input[type="file"] {
            padding: 0.5rem;
            cursor: pointer;
        }
        
        input::placeholder {
            color: var(--text-placeholder);
        }
        
        input:focus,
        select:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
            background: var(--bg-input-focus);
        }
        
        select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23e5e7eb' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
            appearance: none;
        }
        
        select option {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }
        
        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary));
            color: #ffffff;
            border: none;
            border-radius: var(--radius-full);
            font-size: var(--text-base);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
            margin-top: 0.5rem;
        }
        
        .btn-submit:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(56, 189, 248, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Estilo para botón de adjuntar archivo */
        button[onclick*="cedulaPdf"]:hover {
            background: var(--bg-secondary) !important;
            border-color: var(--accent-primary) !important;
        }
        
        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=4" alt="Logo" class="header-logo">
            <h1>Registro SAGRILAFT</h1>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                ✓ <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #fee2e2; border: 1px solid #dc2626; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                ✗ <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form id="registroForm" method="POST" action="index.php?route=/home/register" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de Usuario <span class="required">*</span></label>
                    <select name="user_type" id="userType" required>
                        <option value="">Seleccione...</option>
                        <option value="cliente" <?= (isset($_SESSION['last_user_type']) && $_SESSION['last_user_type'] === 'cliente') ? 'selected' : '' ?>>Cliente</option>
                        <option value="proveedor" <?= (isset($_SESSION['last_user_type']) && $_SESSION['last_user_type'] === 'proveedor') ? 'selected' : '' ?>>Proveedor</option>
                        <option value="transportista" <?= (isset($_SESSION['last_user_type']) && $_SESSION['last_user_type'] === 'transportista') ? 'selected' : '' ?>>Transportista</option>
                        <option value="empleado" <?= (isset($_SESSION['last_user_type']) && $_SESSION['last_user_type'] === 'empleado') ? 'selected' : '' ?>>Empleado</option>
                        <option value="otros" <?= (isset($_SESSION['last_user_type']) && $_SESSION['last_user_type'] === 'otros') ? 'selected' : '' ?>>Otros</option>
                    </select>
                </div>

                <div class="form-group" id="personTypeGroup">
                    <label>Tipo de Persona <span class="required">*</span></label>
                    <select name="person_type" id="personType" required>
                        <option value="">Seleccione...</option>
                        <option value="natural">Natural</option>
                        <option value="juridica">Jurídica</option>
                    </select>
                </div>
            </div>
            
            <!-- Campo adicional para "Otros" -->
            <div class="form-row" id="otrosTypeRow" style="display: none;">
                <div class="form-group">
                    <label>Categoría <span class="required">*</span></label>
                    <select name="otros_category" id="otrosCategory">
                        <option value="">Seleccione...</option>
                        <option value="cliente">Tipo Cliente</option>
                        <option value="proveedor">Tipo Proveedor</option>
                    </select>
                </div>
                <div class="form-group"></div>
            </div>

            <div class="form-row" id="ubicacionRow" style="display: none;">
                <div class="form-group">
                    <label>Ubicación <span class="required">*</span></label>
                    <select name="ubicacion" id="ubicacion">
                        <option value="nacional">Nacional</option>
                        <option value="internacional">Internacional</option>
                    </select>
                </div>
                <div class="form-group"></div>
            </div>

            <!-- CAMPOS NORMALES (Cliente, Proveedor, etc.) -->
            <div id="camposNormales">
                <div class="form-group full-width">
                    <label id="nameLabel">Nombre Completo / Razón Social <span class="required">*</span></label>
                    <input type="text" name="company_name" id="companyName" placeholder="Ingrese el nombre" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo de Documento <span class="required">*</span></label>
                        <select name="document_type" id="documentType" required>
                            <option value="">Seleccione...</option>
                            <option value="cedula">Cédula de Ciudadanía</option>
                            <option value="nit">NIT</option>
                            <option value="cedula_extranjeria">Cédula de Extranjería</option>
                            <option value="pasaporte">Pasaporte</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de Documento <span class="required">*</span></label>
                        <input type="text" name="document_number" placeholder="Ingrese su número de documento" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>

                    <div class="form-group">
                        <label>Teléfono <span class="required">*</span></label>
                        <input type="tel" name="phone" placeholder="Ingrese su número de teléfono" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Asesor Comercial que lo atendió <span class="required">*</span></label>
                    <select name="asesor_comercial_id" id="asesorComercial" required>
                        <option value="">Seleccione un asesor comercial...</option>
                        <?php 
                        if (isset($asesores) && !empty($asesores)) {
                            // Aplanar array para mostrar todos juntos sin agrupar
                            $todosAsesores = [];
                            foreach ($asesores as $sede => $asesoresSede) {
                                if (is_array($asesoresSede)) {
                                    foreach ($asesoresSede as $asesor) {
                                        if (isset($asesor['id']) && isset($asesor['nombre_completo'])) {
                                            $todosAsesores[] = $asesor;
                                        }
                                    }
                                }
                            }
                            
                            // Ordenar alfabéticamente
                            if (!empty($todosAsesores)) {
                                usort($todosAsesores, function($a, $b) {
                                    return strcmp($a['nombre_completo'], $b['nombre_completo']);
                                });
                                
                                // Mostrar opciones
                                foreach ($todosAsesores as $asesor) {
                                    $id = htmlspecialchars($asesor['id'], ENT_QUOTES, 'UTF-8');
                                    $nombre = htmlspecialchars($asesor['nombre_completo'], ENT_QUOTES, 'UTF-8');
                                    echo "<option value=\"{$id}\">{$nombre}</option>\n";
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- CAMPOS EMPLEADO (se muestran solo cuando se selecciona Empleado) -->
            <div id="camposEmpleado" style="display: none;">
                <div class="form-group full-width">
                    <label>Nombre Completo del Empleado <span class="required">*</span></label>
                    <input type="text" name="empleado_nombre" id="empleadoNombre" placeholder="Ingrese el nombre completo del empleado">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Número de Cédula <span class="required">*</span></label>
                        <input type="text" name="empleado_cedula" id="empleadoCedula" placeholder="Número de cédula" pattern="[0-9]{6,10}" title="La cédula debe tener entre 6 y 10 dígitos">
                    </div>

                    <div class="form-group">
                        <label>Celular <span class="required">*</span></label>
                        <input type="text" name="empleado_celular" id="empleadoCelular" placeholder="Número de celular" pattern="[0-9]{10}" title="El celular debe tener 10 dígitos">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Cargo <span class="required">*</span></label>
                        <input type="text" name="empleado_cargo" id="empleadoCargo" placeholder="Ingrese el cargo del empleado">
                    </div>

                    <div class="form-group">
                        <label>Ciudad Vacante <span class="required">*</span></label>
                        <input type="text" name="empleado_ciudad_vacante" id="empleadoCiudadVacante" placeholder="Ciudad">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ciudad de Nacimiento <span class="required">*</span></label>
                        <input type="text" name="empleado_ciudad_nacimiento" id="empleadoCiudadNacimiento" placeholder="Ciudad">
                    </div>

                    <div class="form-group">
                        <label>Fecha de Nacimiento <span class="required">*</span></label>
                        <input type="date" name="empleado_fecha_nacimiento" id="empleadoFechaNacimiento">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Adjuntar PDF de Cédula (Opcional)</label>
                    <input type="file" name="cedula_pdf" id="cedulaPdf" accept=".pdf" style="display:none;">
                    <button type="button" onclick="document.getElementById('cedulaPdf').click()" style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-primary); padding: 10px 18px; border-radius: var(--radius-md); cursor: pointer; font-size: 14px; font-family: var(--font-primary); margin-bottom: 8px; width: 100%; transition: all 0.2s;">
                        Adjuntar PDF de Cédula
                    </button>
                    <p style="font-size: 12px; color: #64748b; margin: 4px 0 8px 0;">Tamaño máximo: 10MB</p>
                    <div id="cedulaFileInfo" style="display: none; margin-top: 12px; background: rgba(241, 245, 249, 0.8); border: 1px solid var(--border-primary); border-radius: var(--radius-md); padding: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="cedulaFileName" style="color: var(--text-primary); font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;"></span>
                            <button type="button" onclick="clearCedulaPdf()" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; margin-left: 10px;">
                                Quitar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">Continuar al Formulario</button>
        </form>
    </div>

    <script>
        const userType = document.getElementById('userType');
        const personType = document.getElementById('personType');
        const personTypeGroup = document.getElementById('personTypeGroup');
        const documentType = document.getElementById('documentType');
        const ubicacionRow = document.getElementById('ubicacionRow');
        const otrosTypeRow = document.getElementById('otrosTypeRow');
        const otrosCategory = document.getElementById('otrosCategory');
        const nameLabel = document.getElementById('nameLabel');
        const companyName = document.getElementById('companyName');
        const camposNormales = document.getElementById('camposNormales');
        const camposEmpleado = document.getElementById('camposEmpleado');
        const btnSubmit = document.getElementById('btnSubmit');
        
        // Campos de empleado
        const empleadoNombre = document.getElementById('empleadoNombre');
        const empleadoCedula = document.getElementById('empleadoCedula');
        const empleadoCargo = document.getElementById('empleadoCargo');
        const empleadoFechaNacimiento = document.getElementById('empleadoFechaNacimiento');
        
        // Campos normales
        const companyNameInput = document.getElementById('companyName');
        const documentNumber = document.querySelector('input[name="document_number"]');
        const email = document.querySelector('input[name="email"]');
        const phone = document.querySelector('input[name="phone"]');
        const asesorComercial = document.getElementById('asesorComercial');
        
        // Manejo de archivo PDF de cédula
        const cedulaPdf = document.getElementById('cedulaPdf');
        const cedulaFileInfo = document.getElementById('cedulaFileInfo');
        const cedulaFileName = document.getElementById('cedulaFileName');
        
        if (cedulaPdf) {
            cedulaPdf.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const size = (file.size / 1024 / 1024).toFixed(2);
                    
                    // Validar tipo
                    if (file.type !== 'application/pdf') {
                        alert('Solo se permiten archivos PDF.');
                        this.value = '';
                        return;
                    }
                    
                    // Validar tamaño (máximo 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('El archivo es demasiado grande. El tamaño máximo es 10MB.');
                        this.value = '';
                        return;
                    }
                    
                    cedulaFileName.textContent = `${file.name} (${size} MB)`;
                    cedulaFileInfo.style.display = 'block';
                } else {
                    cedulaFileInfo.style.display = 'none';
                }
            });
        }
        
        function clearCedulaPdf() {
            if (cedulaPdf) {
                cedulaPdf.value = '';
                cedulaFileInfo.style.display = 'none';
            }
        }
        
        // Función para actualizar la vista según el tipo de usuario
        function updateUserTypeView() {
            const selectedType = userType.value;
            
            // Mostrar/ocultar ubicación para proveedores
            if (selectedType === 'proveedor') {
                ubicacionRow.style.display = 'grid';
            } else {
                ubicacionRow.style.display = 'none';
            }
            
            // Cambiar entre campos normales y campos de empleado
            if (selectedType === 'empleado') {
                // Ocultar campos normales
                personTypeGroup.style.display = 'none';
                camposNormales.style.display = 'none';
                otrosTypeRow.style.display = 'none';
                
                // Mostrar campos de empleado
                camposEmpleado.style.display = 'block';
                
                // Cambiar texto del botón
                btnSubmit.textContent = 'Enviar Registro de Empleado';
                
                // Quitar required de campos normales
                personType.removeAttribute('required');
                companyNameInput.removeAttribute('required');
                documentType.removeAttribute('required');
                documentNumber.removeAttribute('required');
                email.removeAttribute('required');
                phone.removeAttribute('required');
                asesorComercial.removeAttribute('required');
                otrosCategory.removeAttribute('required');
                
                // Agregar required a campos de empleado
                empleadoNombre.setAttribute('required', 'required');
                empleadoCedula.setAttribute('required', 'required');
                empleadoCargo.setAttribute('required', 'required');
                empleadoFechaNacimiento.setAttribute('required', 'required');
                
                // Valor por defecto para person_type
                personType.value = 'natural';
            } else {
                // Mostrar campos normales
                personTypeGroup.style.display = 'block';
                camposNormales.style.display = 'block';
                
                // Ocultar campos de empleado
                camposEmpleado.style.display = 'none';
                
                // Cambiar texto del botón
                btnSubmit.textContent = 'Continuar al Formulario';
                
                // Agregar required a campos normales
                personType.setAttribute('required', 'required');
                companyNameInput.setAttribute('required', 'required');
                documentType.setAttribute('required', 'required');
                documentNumber.setAttribute('required', 'required');
                email.setAttribute('required', 'required');
                phone.setAttribute('required', 'required');
                
                // Mostrar/ocultar campo de categoría para "Otros"
                if (selectedType === 'otros') {
                    otrosTypeRow.style.display = 'grid';
                    otrosCategory.setAttribute('required', 'required');
                } else {
                    otrosTypeRow.style.display = 'none';
                    otrosCategory.removeAttribute('required');
                }
                
                // Asesor comercial solo es requerido para clientes, NO para transportistas ni proveedores
                if (selectedType === 'cliente') {
                    asesorComercial.setAttribute('required', 'required');
                    asesorComercial.parentElement.style.display = 'block';
                } else {
                    asesorComercial.removeAttribute('required');
                    asesorComercial.parentElement.style.display = 'none';
                }
                
                // Quitar required de campos de empleado
                empleadoNombre.removeAttribute('required');
                empleadoCedula.removeAttribute('required');
                empleadoCargo.removeAttribute('required');
                empleadoFechaNacimiento.removeAttribute('required');
            }
        }
        
        // Ejecutar al cargar la página si hay un tipo preseleccionado
        if (userType.value) {
            updateUserTypeView();
        }

        userType.addEventListener('change', updateUserTypeView);

        personType.addEventListener('change', function() {
            if (this.value === 'natural') {
                nameLabel.innerHTML = 'Nombre Completo <span class="required" style="color: var(--error);">*</span>';
                companyName.placeholder = 'Ingrese su nombre completo';
                documentType.value = documentType.value === 'nit' ? 'cedula' : documentType.value;
            } else if (this.value === 'juridica') {
                nameLabel.innerHTML = 'Razón Social <span class="required" style="color: var(--error);">*</span>';
                companyName.placeholder = 'Ingrese la razón social de la empresa';
                documentType.value = documentType.value === 'cedula' ? 'nit' : documentType.value;
            }
        });

        documentType.addEventListener('change', function() {
            if (this.value === 'nit' && personType.value === '') {
                personType.value = 'juridica';
                nameLabel.innerHTML = 'Razón Social <span class="required" style="color: var(--error);">*</span>';
                companyName.placeholder = 'Ingrese la razón social de la empresa';
            } else if (this.value === 'cedula' && personType.value === '') {
                personType.value = 'natural';
                nameLabel.innerHTML = 'Nombre Completo <span class="required" style="color: var(--error);">*</span>';
                companyName.placeholder = 'Ingrese su nombre completo';
            }
        });
    </script>
    
    <style>
        /* Responsive para home */
        @media (max-width: 768px) {
            body {
                padding: 1rem !important;
            }
            
            .container {
                padding: 1.5rem !important;
                max-width: 100% !important;
            }
            
            .header-logo {
                width: 48px !important;
                height: 48px !important;
            }
            
            .header h1 {
                font-size: 1.25rem !important;
            }
            
            .header p {
                font-size: 0.85rem !important;
            }
            
            .form-row {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            
            .form-group {
                margin-bottom: 1rem !important;
            }
            
            label {
                font-size: 0.8rem !important;
            }
            
            input, select {
                font-size: 0.85rem !important;
                padding: 0.65rem 0.85rem !important;
            }
            
            .btn-submit {
                padding: 0.75rem !important;
                font-size: 0.9rem !important;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 0.75rem !important;
            }
            
            .container {
                padding: 1rem !important;
            }
            
            .header {
                margin-bottom: 1.5rem !important;
                padding-bottom: 1rem !important;
            }
            
            .header-logo {
                width: 40px !important;
                height: 40px !important;
            }
            
            .header h1 {
                font-size: 1.1rem !important;
            }
            
            .header p {
                font-size: 0.8rem !important;
            }
            
            .form-row {
                gap: 0.75rem !important;
            }
            
            .form-group {
                margin-bottom: 0.75rem !important;
            }
            
            input, select {
                font-size: 0.8rem !important;
                padding: 0.6rem 0.75rem !important;
            }
            
            .btn-submit {
                padding: 0.7rem !important;
                font-size: 0.85rem !important;
            }
        }
        
        /* Asegurar que no haya overflow horizontal */
        * {
            box-sizing: border-box;
        }
        
        body, html {
            overflow-x: hidden;
            max-width: 100%;
        }
    </style>
</body>
</html>
