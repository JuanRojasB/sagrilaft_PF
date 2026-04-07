<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAGRILAFT - Registro</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
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

        <form id="registroForm" method="POST" action="<?= $_ENV['APP_URL'] ?>/home/register">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de Usuario <span class="required">*</span></label>
                    <select name="user_type" id="userType" required>
                        <option value="">Seleccione...</option>
                        <option value="cliente">Cliente</option>
                        <option value="proveedor">Proveedor</option>
                        <option value="transportista">Transportista</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tipo de Persona <span class="required">*</span></label>
                    <select name="person_type" id="personType" required>
                        <option value="">Seleccione...</option>
                        <option value="natural">Natural</option>
                        <option value="juridica">Jurídica</option>
                    </select>
                </div>
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

            <button type="submit" class="btn-submit">Continuar al Formulario</button>
        </form>
    </div>

    <script>
        const userType = document.getElementById('userType');
        const personType = document.getElementById('personType');
        const documentType = document.getElementById('documentType');
        const ubicacionRow = document.getElementById('ubicacionRow');
        const nameLabel = document.getElementById('nameLabel');
        const companyName = document.getElementById('companyName');

        userType.addEventListener('change', function() {
            if (this.value === 'proveedor') {
                ubicacionRow.style.display = 'grid';
            } else {
                ubicacionRow.style.display = 'none';
            }
        });

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
