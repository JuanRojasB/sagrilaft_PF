<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(($codigo ?? 'SAGRILAFT') . ' - ' . ($headerSubtitle ?? 'Formulario SAGRILAFT')) ?></title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/font-scale-enhanced.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/signature-modal.css">
    <!-- Sistema de Firma Electrónica - cargado en <head> para que esté disponible antes del DOMContentLoaded -->
    <script src="/gestion-sagrilaft/public/assets/js/signature-pad.js"></script>
    <!-- PDF.js para previsualización de PDFs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* ============================================
           ESTILOS PARA PANTALLA (WEB) - TEMA GLOBAL
           ============================================ */
        @media screen {
            body {
                font-family: var(--font-primary);
                background: var(--bg-primary);
                padding: 1rem 1.5rem;
                font-size: 14px;
                min-height: 100vh;
            }
            
            .pdf-container {
                max-width: 1400px;
                width: 100%;
                margin: 0 auto;
                background: var(--bg-card);
                box-shadow: var(--shadow-lg);
                padding: 1.5rem 2rem;
                border-radius: var(--radius-lg);
                border: 1px solid var(--border-accent);
            }            
            /* Encabezado moderno */
            .form-header {
                border: 2px solid var(--border-primary);
                margin-bottom: 1.25rem;
                border-radius: var(--radius-md);
                overflow: hidden;
                background: var(--bg-secondary);
            }
            
            .header-row {
                display: grid;
                grid-template-columns: 100px 1fr;
                border-bottom: 2px solid var(--border-primary);
            }
            
            .logo-cell {
                border-right: 2px solid var(--border-primary);
                padding: 0.9375rem;
                display: flex;
                align-items: center;
                justify-content: center;
                background: transparent;
            }
            
            .logo-cell img {
                max-width: 70px;
                max-height: 70px;
                width: auto;
                height: auto;
                object-fit: contain;
            }
            
            .title-cell {
                padding: 0.9375rem 1.25rem;
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: var(--bg-secondary);
            }
            
            .title-cell h1, .title-cell h2 {
                color: var(--text-primary);
                font-size: 16px;
                font-weight: bold;
                margin: 3px 0;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .metadata-row {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                font-size: 14px;
                background: var(--bg-tertiary);
            }
            
            .metadata-cell {
                border-right: 2px solid var(--border-primary);
                padding: 0.6rem;
                text-align: center;
                color: var(--text-primary);
            }
            
            .metadata-cell:last-child {
                border-right: none;
            }
            
            .metadata-cell strong {
                display: block;
                font-weight: bold;
                margin-bottom: 4px;
                color: var(--text-primary);
                font-size: 13px;
            }
            
            /* Secciones modernas */
            .form-section {
                border: 2px solid var(--border-secondary);
                margin-bottom: 20px;
                border-radius: var(--radius-md);
                overflow: hidden;
                background: var(--bg-secondary);
            }

            /* Sección exclusiva para uso interno */
            .internal-section .section-title {
                background: rgba(234, 179, 8, 0.12);
                border-bottom-color: rgba(234, 179, 8, 0.4);
                color: #fbbf24;
            }
            .internal-section.locked {
                position: relative;
                opacity: 0.6;
                pointer-events: none;
                user-select: none;
            }
            .internal-section.locked::after {
                content: 'Solo uso interno — Oficial de Cumplimiento';
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(241, 245, 249, 0.75);
                color: #92400e;
                font-size: 14px;
                font-weight: 600;
                letter-spacing: 0.5px;
                pointer-events: none;
                border-radius: var(--radius-md);
            }
            
            .section-title {
                background: var(--bg-tertiary);
                padding: 12px 15px;
                font-weight: bold;
                font-size: 15px;
                text-align: center;
                border-bottom: 2px solid var(--border-secondary);
                text-transform: uppercase;
                letter-spacing: 1px;
                color: var(--text-primary);
            }
            
            .section-content {
                padding: 0;
            }
            
            /* Tabla moderna */
            .field-table {
                width: 100%;
                border-collapse: collapse;
            }
            
            .field-table td {
                border: 1px solid var(--border-secondary);
                padding: 10px 14px;
                font-size: 14px;
                vertical-align: middle;
                color: var(--text-primary);
            }
            
            .field-label {
                font-weight: 600;
                background: var(--bg-tertiary);
                color: var(--text-secondary);
                text-transform: uppercase;
                font-size: 13px;
                letter-spacing: 0.3px;
            }
            
            .field-input {
                background: var(--bg-secondary);
                color: var(--text-primary);
            }
            
            /* Asegurar que todos los textos en tablas sean claros */
            .field-table thead td {
                color: var(--text-secondary);
            }
            
            .field-table tbody td {
                color: var(--text-primary);
            }
            
            /* En pantallas anchas, los labels no necesitan ser tan angostos */
                @media (min-width: 1200px) {
                .field-table td {
                    padding: 11px 16px;
                }
                .field-label {
                    font-size: 14px;
                    white-space: nowrap;
                }
            }
            
            /* Inputs modernos */
            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="date"],
            input[type="time"],
            input[type="datetime-local"],
            input[type="number"],
            select,
            textarea {
                width: 100%;
                border: 1px solid var(--border-primary);
                padding: 9px 13px;
                font-size: 14px;
                font-family: var(--font-primary);
                background: var(--bg-input);
                color: var(--text-primary);
                border-radius: var(--radius-sm);
                transition: all var(--transition-base);
                color-scheme: dark;
                -webkit-color-scheme: dark;
            }
            
            input::placeholder,
            textarea::placeholder {
                color: var(--text-placeholder);
                opacity: 1;
            }
            
            input:focus, select:focus, textarea:focus {
                outline: none;
                border-color: var(--border-focus);
                background: var(--bg-input-focus);
                box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
            }
            
            textarea {
                min-height: 60px;
                resize: vertical;
            }
            
            select {
                cursor: pointer;
            }
            
            /* Opciones de select */
            select option {
                background: var(--bg-tertiary);
                color: var(--text-primary);
            }
            
            /* ICONOS NATIVOS - Webkit (Chrome, Safari, Edge) */
            input[type="date"]::-webkit-calendar-picker-indicator,
            input[type="time"]::-webkit-calendar-picker-indicator,
            input[type="datetime-local"]::-webkit-calendar-picker-indicator {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
                background-size: 16px 16px;
                background-repeat: no-repeat;
                background-position: center;
                width: 20px;
                height: 20px;
                cursor: pointer;
                opacity: 1 !important;
            }
            
            /* Ocultar spinners de inputs numéricos */
            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            
            /* Forzar iconos blancos en todos los estados */
            input[type="date"]:hover::-webkit-calendar-picker-indicator,
            input[type="time"]:hover::-webkit-calendar-picker-indicator,
            input[type="datetime-local"]:hover::-webkit-calendar-picker-indicator {
                opacity: 0.8;
            }
            
            /* Firefox */
            input[type="date"],
            input[type="time"],
            input[type="datetime-local"],
            input[type="number"] {
                -moz-appearance: textfield;
            }
            
            /* Scrollbar personalizado */
            select::-webkit-scrollbar {
                width: 8px;
            }
            
            select::-webkit-scrollbar-track {
                background: var(--bg-tertiary);
            }
            
            select::-webkit-scrollbar-thumb {
                background: var(--text-disabled);
                border-radius: var(--radius-sm);
            }
            
            select::-webkit-scrollbar-thumb:hover {
                background: var(--text-muted);
            }
            
            /* Flecha del select siempre visible */
            select {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23e5e7eb' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 8px center;
                padding-right: 30px;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }
            
            /* Checkbox y radio modernos */
            input[type="radio"],
            input[type="checkbox"] {
                width: 16px;
                height: 16px;
                margin: 0 6px 0 0;
                cursor: pointer;
                accent-color: var(--accent-primary);
                vertical-align: middle;
            }
            
            .checkbox-group {
                display: inline-flex;
                gap: 15px;
                align-items: center;
                flex-wrap: wrap;
                padding: 4px 0;
            }
            
            .checkbox-group label {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                color: var(--text-secondary);
                cursor: pointer;
                transition: color var(--transition-fast);
                white-space: nowrap;
            }
            
            .checkbox-group label:hover {
                color: var(--accent-light);
            }
            
            /* Labels dentro de celdas de tabla */
            .field-input label {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                margin-right: 12px;
                font-size: 13px;
                color: var(--text-primary);
                white-space: nowrap;
            }
            
            .field-input label input[type="radio"],
            .field-input label input[type="checkbox"] {
                margin: 0 4px 0 0;
            }
            
            /* Info box moderno */
            .info-box {
                margin: 15px;
                padding: 15px;
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                border-left: 4px solid #3b82f6;
                font-size: 13px;
                line-height: 1.6;
                color: #1e3a5f;
                border-radius: var(--radius-sm);
            }
            
            .info-box strong {
                color: #1d4ed8;
            }

            .info-box p, .info-box ul, .info-box li {
                color: #1e3a5f !important;
            }
            
            .info-box a {
                color: #2563eb;
                text-decoration: underline;
            }
            
            .info-box a:hover {
                color: #1d4ed8;
            }
            
            /* Botones modernos */
            .form-actions {
                margin-top: 30px;
                display: flex;
                gap: 15px;
                justify-content: center;
                padding: 20px;
                background: var(--bg-tertiary);
                border-radius: var(--radius-md);
            }
            
            .btn {
                padding: 12px 30px;
                border: none;
                border-radius: var(--radius-sm);
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all var(--transition-base);
                box-shadow: var(--shadow-md);
            }
            
            .btn-primary {
                background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary));
                color: #ffffff;
            }
            
            .btn-primary:hover {
                background: linear-gradient(135deg, #1e40af, var(--accent-secondary));
                transform: translateY(-2px);
                box-shadow: 0 6px 12px rgba(56, 189, 248, 0.3);
            }
            
            .btn-primary:disabled {
                background: linear-gradient(135deg, var(--success), #34d399);
                color: #ffffff !important;
                cursor: not-allowed;
                opacity: 0.9;
            }
            
            .btn-secondary {
                background: linear-gradient(135deg, #94a3b8, #64748b);
                color: #ffffff;
            }
            
            .btn-secondary:hover {
                background: linear-gradient(135deg, var(--text-muted), var(--text-disabled));
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
            }
            
            /* Mensajes */
            .message {
                padding: 15px;
                border-radius: var(--radius-md);
                margin-bottom: 20px;
                font-size: 13px;
                display: none;
                text-align: center;
                font-weight: 500;
            }
            
            .message.success {
                background: var(--success-bg);
                border: 2px solid var(--success-border);
                color: #15803d;
            }
            
            .message.error {
                background: var(--error-bg);
                border: 2px solid var(--error-border);
                color: #dc2626;
            }
        }
        
        /* ============================================
           ESTILOS PARA IMPRESIÓN (PDF) - BLANCO Y NEGRO
           ============================================ */
        @media print {
            @page {
                size: letter;
                margin: 2mm;
            }
            
            html, body {
                font-family: Arial, Helvetica, sans-serif;
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 4.5px;
                line-height: 1.0;
                width: 100%;
                height: 100%;
            }
            
            .pdf-container {
                max-width: 100%;
                width: 100%;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
                border-radius: 0 !important;
                /* Escalar todo para que quepa en una página */
                transform-origin: top left;
                transform: scale(0.72);
                width: 139%; /* compensar el scale */
            }
            
            /* Encabezado PDF */
            .form-header {
                border: 1px solid #000 !important;
                margin-bottom: 0.5mm !important;
                border-radius: 0 !important;
                background: white !important;
            }
            
            .header-row {
                border-bottom: 1px solid #000 !important;
                display: grid !important;
                grid-template-columns: 40px 1fr !important;
            }
            
            .logo-cell {
                border-right: 1px solid #000 !important;
                background: white !important;
                padding: 1px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            
            .logo-cell img {
                max-width: 24px !important;
                max-height: 24px !important;
                width: auto !important;
                height: auto !important;
                object-fit: contain !important;
            }
            
            .title-cell {
                background: white !important;
                color: #000 !important;
                padding: 0.5px 1px !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
            }
            
            .title-cell h1, .title-cell h2 {
                color: #000 !important;
                font-size: 7px !important;
                margin: 0.5px 0 !important;
                line-height: 1.1 !important;
                padding: 0 !important;
                text-align: center !important;
            }
            
            .metadata-row {
                background: white !important;
                font-size: 5px !important;
                display: grid !important;
                grid-template-columns: repeat(6, 1fr) !important;
            }
            
            .metadata-cell {
                border-right: 1px solid #000 !important;
                color: #000 !important;
                padding: 0.8px 1px !important;
                line-height: 1.1 !important;
                text-align: center !important;
            }
            
            .metadata-cell:last-child {
                border-right: none !important;
            }
            
            .metadata-cell strong {
                color: #000 !important;
                font-size: 5px !important;
                display: block !important;
                margin-bottom: 0.3px !important;
            }
            
            /* Secciones PDF */
            .form-section {
                border: 1px solid #000 !important;
                margin-bottom: 0.4mm !important;
                border-radius: 0 !important;
                background: white !important;
                page-break-inside: avoid !important;
            }
            
            .section-title {
                background: #e8e8e8 !important;
                color: #000 !important;
                padding: 1px 2px !important;
                font-size: 6px !important;
                border-bottom: 1px solid #000 !important;
                font-weight: bold !important;
                line-height: 1.1 !important;
                text-align: center !important;
                text-transform: uppercase !important;
            }
            
            .section-content {
                padding: 0 !important;
            }
            
            /* Tabla PDF */
            .field-table {
                border-collapse: collapse !important;
                width: 100% !important;
            }
            
            .field-table td {
                border: 0.5px solid #666 !important;
                padding: 1px 1.5px !important;
                font-size: 6px !important;
                background: white !important;
                line-height: 1.1 !important;
                vertical-align: middle !important;
            }
            
            .field-label {
                background: #f0f0f0 !important;
                color: #000 !important;
                font-size: 5.5px !important;
                font-weight: bold !important;
                padding: 1px 1.5px !important;
                text-transform: uppercase !important;
                line-height: 1.1 !important;
            }
            
            .field-input {
                background: white !important;
                padding: 1px 1.5px !important;
            }
            
            /* Inputs PDF */
            input, select, textarea {
                border: none !important;
                border-bottom: 0.5px solid #999 !important;
                background: transparent !important;
                color: #000 !important;
                font-size: 5.5px !important;
                padding: 0.5px 1px !important;
                margin: 0 !important;
                box-shadow: none !important;
                line-height: 1.1 !important;
                height: auto !important;
                min-height: 0 !important;
            }
            
            /* Placeholders uniformes */
            input::placeholder,
            textarea::placeholder,
            select::placeholder {
                font-size: 5px !important;
                color: #999 !important;
                opacity: 0.7 !important;
            }
            
            textarea {
                border: 0.5px solid #999 !important;
                min-height: 12px !important;
                max-height: 18px !important;
                padding: 0.5px !important;
                line-height: 1.1 !important;
                font-size: 5px !important;
                overflow: hidden !important;
            }
            
            select {
                border: 0.5px solid #999 !important;
                padding: 0.5px 1px !important;
                font-size: 5.5px !important;
                height: auto !important;
            }
            
            select option {
                font-size: 5.5px !important;
            }
            
            /* Info box PDF */
            .info-box {
                background: #fafafa !important;
                border: 0.5px solid #ccc !important;
                color: #000 !important;
                font-size: 4.5px !important;
                margin: 0.5mm !important;
                padding: 0.5mm !important;
                line-height: 1.1 !important;
            }
            
            .info-box strong {
                color: #000 !important;
                font-size: 4.5px !important;
            }
            
            .info-box a {
                color: #2563eb !important;
                font-size: 4.5px !important;
            }
            
            .info-box ul {
                margin: 0.5mm 0 0 3mm !important;
                padding: 0 !important;
            }
            
            .info-box li {
                margin-bottom: 0 !important;
                line-height: 1.2 !important;
            }
            
            .info-box p {
                margin: 0 !important;
            }
            
            /* Ocultar en PDF */
            .form-actions {
                display: none !important;
            }
            
            .message {
                display: none !important;
            }
            
            /* Ocultar sección de documentos adjuntos en PDF */
            .no-print {
                display: none !important;
            }
            
            /* Checkbox y radio PDF */
            input[type="radio"],
            input[type="checkbox"] {
                width: 6px !important;
                height: 6px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 1px 0 0 !important;
                padding: 0 !important;
                vertical-align: middle !important;
            }
            
            .checkbox-group {
                padding: 0 !important;
                gap: 1mm !important;
                display: inline-flex !important;
                flex-wrap: nowrap !important;
            }
            
            .checkbox-group label {
                color: #000 !important;
                font-size: 5.5px !important;
                margin-right: 1mm !important;
                display: inline-flex !important;
                align-items: center !important;
                line-height: 1.05 !important;
                white-space: nowrap !important;
            }
            
            /* Labels dentro de celdas en PDF */
            .field-input label,
            td label {
                color: #000 !important;
                font-size: 5.5px !important;
                margin-right: 1mm !important;
                display: inline-flex !important;
                align-items: center !important;
                white-space: nowrap !important;
            }
            
            .field-input label input[type="radio"],
            .field-input label input[type="checkbox"],
            td label input[type="radio"],
            td label input[type="checkbox"] {
                margin: 0 0.5px 0 0 !important;
            }
            
            /* Reducir espaciado en tablas de accionistas */
            #accionistasTable thead td {
                padding: 0.8px 1px !important;
                font-size: 4.5px !important;
                background: #f0f0f0 !important;
            }
            
            #accionistasTable tbody td {
                padding: 0.5px 0.8px !important;
            }
            
            #accionistasTable input {
                font-size: 4.2px !important;
            }
            
            /* Ajustar botones pequeños en tablas */
            .btn-remove-row,
            button[onclick*="remove"],
            button[onclick*="add"] {
                display: none !important;
            }
            
            /* Reducir altura de filas */
            tr {
                height: auto !important;
            }
            
            /* Ajustar white-space para labels */
            .field-label {
                white-space: normal !important;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .pdf-container {
                padding: 15px;
            }
            
            .metadata-row {
                grid-template-columns: 1fr 1fr;
            }
            
            .metadata-cell {
                border-bottom: 1px solid var(--border-secondary);
            }
        }
        
        @media (min-width: 1600px) {
            .pdf-container {
                max-width: 1600px;
            }
        }

        /* ============================================
           SISTEMA DE GRID PARA FORMULARIOS (.fr/.fl/.fv)
           ============================================ */
        @media screen {
            /* Variables de ancho de label — consistentes en todo el formulario */
            :root {
                --lw: 280px;   /* label width base (aumentado de 250px a 280px) */
                --lws: 210px;  /* label width small (aumentado de 190px a 210px) */
            }

            .fr {
                display: grid;
                border-bottom: 1px solid var(--border-secondary);
                align-items: stretch;
                min-height: 44px;
            }
            .fr:last-child { border-bottom: none; }

            /* Label cell */
            .fl {
                font-weight: 600;
                background: var(--bg-tertiary);
                color: var(--text-secondary);
                text-transform: uppercase;
                font-size: 14px;
                letter-spacing: 0.3px;
                padding: 10px 14px;
                display: flex;
                align-items: center;
                border-right: 1px solid var(--border-secondary);
                white-space: nowrap;
                word-break: keep-all;
                line-height: 1.3;
                min-width: 0;
            }

            /* Value cell */
            .fv {
                padding: 8px 12px;
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                background: var(--bg-secondary);
                border-right: 1px solid var(--border-secondary);
                min-width: 0;
            }
            .fv:last-child, .fl:last-child { border-right: none; }

            /* Borde verde en campos válidos */
            .fr input[required]:valid,
            .fr select[required]:valid,
            .fr textarea[required]:valid {
                border-color: rgba(52, 211, 153, 0.4);
            }

            /* Inputs */
            .fr input, .fr select, .fr textarea {
                width: 100%;
                min-width: 0;
                border: 1px solid var(--border-primary);
                padding: 8px 12px;
                font-size: 14px;
                font-family: var(--font-primary);
                background: var(--bg-input);
                color: var(--text-primary);
                border-radius: var(--radius-sm);
                transition: border-color var(--transition-base);
                color-scheme: dark;
                box-sizing: border-box;
            }
            .fr input:focus, .fr select:focus, .fr textarea:focus {
                outline: none;
                border-color: var(--border-focus);
                background: var(--bg-input-focus);
            }
            .fr textarea { min-height: 60px; resize: vertical; }
            .fr select {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23475569' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 8px center;
                padding-right: 30px;
                -webkit-appearance: none;
                appearance: none;
            }
            .fr label {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 14px;
                color: var(--text-secondary);
                cursor: pointer;
                white-space: nowrap;
            }
            .fr label input[type="radio"],
            .fr label input[type="checkbox"] {
                width: 16px; height: 16px; margin: 0;
                accent-color: var(--accent-primary);
            }

            /* ── Layouts de columnas ──
               Patrón: label=280px fijo, valor=1fr, repetido
               Los labels siempre tienen ancho fijo para alinearse entre filas */
            .c1   { grid-template-columns: 280px 1fr; }
            .c22  { grid-template-columns: 280px 1fr 280px 1fr; }
            .c33  { grid-template-columns: 210px 1fr 210px 1fr 210px 1fr; }
            .c222 { grid-template-columns: 210px 1fr 210px 1fr 210px 1fr; }
            /* 3 pares label+valor con anchos distintos: nombre(ancho), doc(medio), campo3(corto) */
            .c322 { grid-template-columns: 210px 1fr 190px 1fr 160px 1fr; }
            /* Fila mixta: label+valor-fijo + label+valor + label+valor */
            .c3fx { grid-template-columns: 210px 240px 210px 1fr 210px 1fr; }
            .cfull { grid-template-columns: 1fr; }

            /* Fila de dirección: ocupa todo el ancho, el widget se expande verticalmente */
            .fr.dir-row { grid-template-columns: 280px 1fr; }
            .fr.dir-row .fv {
                flex-direction: column;
                align-items: stretch;
                padding: 10px 12px;
            }
        }

        /* ── Responsive grid (fuera de @media screen para que funcionen) ── */
        @media screen and (max-width: 900px) {
            .c22, .c3fx  { grid-template-columns: 280px 1fr !important; }
            .c33, .c222  { grid-template-columns: 210px 1fr !important; }
            .c322        { grid-template-columns: 210px 1fr !important; }
        }
        @media screen and (max-width: 600px) {
            .c22, .c33, .c222, .c322, .c3fx, .c1, .fr.dir-row {
                grid-template-columns: 1fr !important;
            }
            .fl {
                border-right: none !important;
                border-bottom: 1px solid var(--border-secondary);
                padding: 6px 12px !important;
                min-height: unset !important;
            }
            .fr { min-height: unset; }
        }

        @media print {
            .fr { display: grid !important; border-bottom: 0.5px solid #999 !important; }
            .fr:last-child { border-bottom: none !important; }
            .fl {
                font-weight: bold !important; background: #f0f0f0 !important; color: #000 !important;
                text-transform: uppercase !important; font-size: 5.5px !important;
                padding: 1px 1.5px !important; display: flex !important; align-items: center !important;
                border-right: 0.5px solid #999 !important; white-space: normal !important;
                line-height: 1.1 !important;
            }
            .fv {
                padding: 1px 1.5px !important; display: flex !important; align-items: center !important;
                flex-wrap: wrap !important; gap: 2px !important;
                background: white !important; border-right: 0.5px solid #999 !important;
            }
            .fv:last-child, .fl:last-child { border-right: none !important; }
            /* Fixed-width labels matching screen layout */
            .c1   { grid-template-columns: 45px 1fr !important; }
            .c22  { grid-template-columns: 45px 1fr 45px 1fr !important; }
            .c33  { grid-template-columns: 38px 1fr 38px 1fr 38px 1fr !important; }
            .c222 { grid-template-columns: 38px 1fr 38px 1fr 38px 1fr !important; }
            .c322 { grid-template-columns: 35px 1fr 30px 1fr 25px 1fr !important; }
            .c3fx { grid-template-columns: 38px 70px 38px 1fr 38px 1fr !important; }
            .cfull { grid-template-columns: 1fr !important; }
            .fr.dir-row { grid-template-columns: 45px 1fr !important; }
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <form id="pdfForm" method="POST" onsubmit="return false;">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="form_type" value="<?= $formType ?>">
            
            <!-- ENCABEZADO -->
            <div class="form-header">
                <div class="header-row">
                    <div class="logo-cell">
                        <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=4" alt="Logo">
                    </div>
                    <div class="title-cell">
                        <h1><?= $headerTitle ?></h1>
                        <h2><?= $headerSubtitle ?></h2>
                    </div>
                </div>
                <div class="metadata-row">
                    <div class="metadata-cell">
                        <strong>Fecha emisión:</strong><?= $fechaEmision ?>
                    </div>
                    <div class="metadata-cell">
                        <strong>Fecha actualización:</strong><?= $fechaActualizacion ?>
                    </div>
                    <div class="metadata-cell">
                        <strong>Fecha revisión:</strong><?= $fechaRevision ?>
                    </div>
                    <div class="metadata-cell">
                        <strong>Versión:</strong><?= $version ?>
                    </div>
                    <div class="metadata-cell">
                        <strong>Código:</strong><?= $codigo ?>
                    </div>
                    <div class="metadata-cell">
                        <strong>Página:</strong>1 de 1
                    </div>
                </div>
            </div>
            
            <!-- CONTENIDO DINÁMICO SEGÚN TIPO DE FORMULARIO -->
            <?php include "pdf_forms/{$formTemplate}.php"; ?>
            
            <!-- SECCIÓN DE DOCUMENTOS ADJUNTOS -->
            <?php if (!isset($is_step_2) || !$is_step_2): ?>
            <div class="form-section no-print" style="margin-top: 20px;">
                <div class="section-title">DOCUMENTOS ADJUNTOS REQUERIDOS</div>
                <div class="section-content">
                    <div style="background: #eff6ff; border-left: 3px solid #3b82f6; padding: 12px 16px; margin: 15px; border-radius: 4px;">
                        <p style="color: #1d4ed8; font-size: 14px; margin: 0 0 8px; font-weight: 600;">
                            <strong>Documentos requeridos:</strong>
                        </p>
                        <ul style="color: #1e3a5f; font-size: 13px; margin: 0 0 0 20px; line-height: 1.8;">
                            <li><strong>RUT</strong></li>
                            <?php if (isset($temp_data['person_type']) && $temp_data['person_type'] === 'juridica'): ?>
                            <li><strong>Cámara de Comercio</strong></li>
                            <li><strong>Composición Accionaria</strong></li>
                            <li><strong>Certificación Bancaria</strong> no mayor a 3 meses</li>
                            <li><strong>Cédula del Representante Legal</strong></li>
                            <?php else: ?>
                            <li><strong>Cédula</strong></li>
                            <li><strong>Certificación Bancaria</strong> no superior a 3 meses</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <table class="field-table">
                        <tr>
                            <td class="field-label" style="width: 30%; vertical-align: top; padding-top: 15px;">ADJUNTAR DOCUMENTOS:</td>
                            <td class="field-input" colspan="3" id="fileInputCell">
                                <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.heic,.heif,image/*" capture="environment" style="display:none;">
                                <button type="button" onclick="document.getElementById('documents').click()" style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-primary); padding: 8px 16px; border-radius: var(--radius-sm); cursor: pointer; font-size: 14px; font-family: var(--font-primary); margin-bottom: 8px;">
                                    + Adjuntar Archivos (PDF o Fotos)
                                </button>
                                <p style="color: #94a3b8; font-size: 12px; margin: 0;">PDF o imágenes (JPG, PNG). Máximo 10MB por archivo.</p>
                                <div id="fileList" style="display: none; margin-top: 12px; background: rgba(241, 245, 249, 0.8); border: 1px solid rgba(71, 85, 105, 0.3); border-radius: 4px; padding: 10px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                        <span style="color: #1e293b; font-size: 13px; font-weight: 600;">Archivos seleccionados (<span id="fileCount">0</span>)</span>
                                        <button type="button" onclick="clearAllFiles()" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                            Limpiar todos
                                        </button>
                                    </div>
                                    <ul id="fileListItems" style="list-style: none; margin: 0; padding: 0;"></ul>
                                </div>
                                <p id="fileError" style="color: #dc2626; font-size: 13px; margin: 8px 0 0; display: none;">Debe adjuntar al menos un documento</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- BOTONES DE ACCIÓN -->
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                    ← Volver
                </button>
                <!-- <button type="button" class="btn btn-secondary" onclick="descargarPDF()">
                    Descargar PDF
                </button> -->
                <button type="submit" class="btn btn-primary">
                    Enviar Formulario
                </button>
            </div>
        </form>
        
        <!-- MENSAJE DE ERROR/ÉXITO - AL FINAL -->
        <div id="message" class="message"></div>
    </div>
    
    <script>
        // Gestión de archivos adjuntos
        let selectedFiles = [];

        const documentsInput = document.getElementById('documents');
        if (documentsInput) {
            documentsInput.addEventListener('change', function(e) {
                const newFiles = Array.from(this.files);
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/heic', 'image/heif'];
                const allowedExtensions = ['.pdf', '.jpg', '.jpeg', '.png', '.heic', '.heif'];
                
                newFiles.forEach(file => {
                    // Validar tipo de archivo
                    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    const isValidType = allowedTypes.includes(file.type) || allowedExtensions.includes(fileExtension);
                    
                    if (!isValidType) {
                        alert(`El archivo "${file.name}" no es válido. Solo se permiten archivos PDF o imágenes (JPG, PNG).`);
                        return;
                    }
                    
                    // Validar tamaño (máximo 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert(`El archivo "${file.name}" es demasiado grande. El tamaño máximo es 10MB.`);
                        return;
                    }
                    
                    const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                    if (!exists) {
                        selectedFiles.push(file);
                    }
                });
                updateFileList();
                this.value = '';
            });
        }

        function updateFileList() {
            const fileList = document.getElementById('fileList');
            const fileListItems = document.getElementById('fileListItems');
            const fileCount = document.getElementById('fileCount');
            const fileInputCell = document.getElementById('fileInputCell');
            
            if (selectedFiles.length > 0) {
                if (fileInputCell) fileInputCell.style.border = '';
                fileList.style.display = 'block';
                fileCount.textContent = selectedFiles.length;
                fileListItems.innerHTML = '';
                
                selectedFiles.forEach((file, index) => {
                    const li = document.createElement('li');
                    li.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 6px 8px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 4px; color: #0f172a;';
                    const size = (file.size / 1024 / 1024).toFixed(2);
                    
                    // Determinar si es imagen o PDF para mostrar preview
                    const isImage = file.type.startsWith('image/');
                    const isPDF = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                    
                    // Crear contenedor de preview
                    const previewContainer = document.createElement('div');
                    previewContainer.style.cssText = 'width: 40px; height: 40px; margin-right: 8px; flex-shrink: 0;';
                    previewContainer.id = `preview-${index}`;
                    
                    if (isImage) {
                        const objectURL = URL.createObjectURL(file);
                        previewContainer.innerHTML = `<img src="${objectURL}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;" alt="Preview">`;
                    } else if (isPDF) {
                        previewContainer.innerHTML = `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #dbeafe; border-radius: 4px; font-size: 20px; border: 1px solid #93c5fd;">📄</div>`;
                        // Intentar generar preview del PDF
                        generatePDFPreview(file, index);
                    } else {
                        previewContainer.innerHTML = `<span style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #e5e7eb; border-radius: 4px; font-size: 20px;">📎</span>`;
                    }
                    
                    const contentDiv = document.createElement('div');
                    contentDiv.style.cssText = 'display: flex; align-items: center; flex: 1; min-width: 0;';
                    contentDiv.appendChild(previewContainer);
                    
                    const nameSpan = document.createElement('span');
                    nameSpan.style.cssText = 'color: #334155; font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;';
                    nameSpan.title = file.name;
                    nameSpan.textContent = `${file.name} (${size} MB)`;
                    contentDiv.appendChild(nameSpan);
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.onclick = () => removeFile(index);
                    removeBtn.style.cssText = 'background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; margin-left: 8px; flex-shrink: 0;';
                    removeBtn.textContent = 'Quitar';
                    
                    li.appendChild(contentDiv);
                    li.appendChild(removeBtn);
                    fileListItems.appendChild(li);
                });
            } else {
                if (fileInputCell) fileInputCell.style.border = '';
                fileList.style.display = 'none';
            }
        }
        
        async function generatePDFPreview(file, index) {
            try {
                // Usar PDF.js si está disponible
                if (typeof pdfjsLib === 'undefined') {
                    console.log('PDF.js no disponible, usando icono por defecto');
                    return;
                }
                
                const arrayBuffer = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument({data: arrayBuffer}).promise;
                const page = await pdf.getPage(1);
                
                const scale = 0.3;
                const viewport = page.getViewport({scale: scale});
                
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
                
                // Reemplazar el icono con el canvas
                const previewContainer = document.getElementById(`preview-${index}`);
                if (previewContainer) {
                    canvas.style.cssText = 'width: 100%; height: 100%; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;';
                    previewContainer.innerHTML = '';
                    previewContainer.appendChild(canvas);
                }
            } catch (error) {
                console.log('Error generando preview de PDF:', error);
                // Mantener el icono por defecto
            }
        }

        function removeFile(index) {
            // Liberar URL del objeto si es una imagen
            const file = selectedFiles[index];
            if (file && file.type.startsWith('image/')) {
                // Las URLs se limpiarán automáticamente al actualizar la lista
            }
            selectedFiles.splice(index, 1);
            updateFileList();
        }

        function clearAllFiles() {
            if (confirm('¿Estás seguro de que quieres quitar todos los archivos?')) {
                selectedFiles = [];
                updateFileList();
            }
        }

        // Descargar PDF: solo genera y descarga el PDF sin guardar ni redirigir
        async function descargarPDF() {
            // Validar campos requeridos primero
            const form = document.getElementById('pdfForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const btn = document.querySelector('.btn.btn-secondary');
            const orig = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Generando...';

            try {
                const formData = new FormData(document.getElementById('pdfForm'));
                formData.append('pdf_preview_only', '1'); // Indicar que es solo preview

                const response = await fetch('<?= $_ENV['APP_URL'] ?>/form/pdf-preview', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const txt = await response.text();
                    throw new Error('Error del servidor: ' + txt.substring(0, 200));
                }

                const ct = response.headers.get('content-type') || '';
                if (ct.includes('application/pdf')) {
                    // Descargar el PDF directamente
                    const blob = await response.blob();
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.target = '_blank';
                    a.click();
                    setTimeout(() => URL.revokeObjectURL(url), 5000);
                } else {
                    const txt = await response.text();
                    throw new Error('Respuesta inesperada: ' + txt.substring(0, 200));
                }
            } catch (err) {
                alert('No se pudo generar el PDF: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.textContent = orig;
            }
        }
        
        // Función para scroll al mensaje
        function scrollToMessage() {
            const messageDiv = document.getElementById('message');
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Registrar event listener cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => { initFormSubmit(); });
        } else {
            initFormSubmit();
        }
        
        function initFormSubmit() {
            const form = document.getElementById('pdfForm');
            if (!form) {
                console.error('ERROR: No se encontró el formulario #pdfForm');
                return;
            }
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Detectar si es el paso 2 (declaración)
                const isStep2 = <?= isset($is_step_2) && $is_step_2 ? 'true' : 'false' ?>;
                
                console.log('Form submit - isStep2:', isStep2);
                console.log('Form submit - selectedFiles.length:', selectedFiles.length);
                
                // Validar que haya archivos seleccionados (solo en paso 1)
                const fileError = document.getElementById('fileError');
                if (!isStep2 && selectedFiles.length === 0) {
                    console.log('BLOQUEANDO ENVÍO - No hay archivos adjuntos');
                    if (fileError) {
                        fileError.style.display = 'block';
                        fileError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    alert('Debe adjuntar al menos un documento antes de enviar el formulario');
                    return;
                }
                if (fileError) fileError.style.display = 'none';
                
                console.log('Validación pasada, enviando formulario...');
            
            // Validar dirección primero (widget oculta el input original y pone sub-campos con required)
            const hiddenDirInputs = this.querySelectorAll('input[id^="direccion-"]');
            for (const dirInput of hiddenDirInputs) {
                if (!dirInput.value || dirInput.value.trim() === '') {
                    const errDiv = document.getElementById('err-' + dirInput.id);
                    if (errDiv) { errDiv.style.display = 'block'; errDiv.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                    else { dirInput.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                    alert('El campo DIRECCIÓN es obligatorio. Complete Tipo, Nº, # y -');
                    return;
                }
            }

            // Validar campos requeridos manualmente (excluir sub-campos del widget de dirección)
            const dirWidgetPrefixes = ['v-', 'n-', 'o-', 'n1-', 'n2-', 'c-'];
            const isDirWidgetField = (f) => dirWidgetPrefixes.some(p => (f.id || '').startsWith(p));

            const requiredFields = this.querySelectorAll('[required]');
            let firstInvalidField = null;
            
            for (const field of requiredFields) {
                // Saltar sub-campos del widget de dirección (ya validados arriba)
                if (isDirWidgetField(field)) continue;

                // Para radio buttons, verificar si al menos uno del grupo está seleccionado
                if (field.type === 'radio') {
                    const radioGroup = this.querySelectorAll(`input[name="${field.name}"]`);
                    const isChecked = Array.from(radioGroup).some(radio => radio.checked);
                    
                    if (!isChecked && !firstInvalidField) {
                        firstInvalidField = field;
                        alert(`Debe seleccionar una opción en el campo: ${field.name.replace(/_/g, ' ').toUpperCase()}`);
                        field.focus();
                        return;
                    }
                }
                // Para otros campos, verificar que tengan valor
                else if (!field.value || field.value.trim() === '') {
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                        
                        // Obtener el label asociado
                        let fieldLabel = field.name.replace(/_/g, ' ').toUpperCase();
                        const label = field.closest('tr')?.querySelector('.field-label')
                                   || field.closest('.fr')?.querySelector('.fl');
                        if (label) {
                            fieldLabel = label.textContent.replace(':', '').trim();
                        }
                        
                        alert(`El campo "${fieldLabel}" es obligatorio`);
                        field.focus();
                        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                }
            }

            // Validar archivos adjuntos (solo paso 1)
            if (!isStep2 && selectedFiles.length === 0) {
                const fileSection = document.getElementById('fileError');
                if (fileSection) { fileSection.style.display = 'block'; fileSection.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                alert('Debe adjuntar al menos un documento antes de enviar el formulario');
                return;
            }

            // Validar firma (solo paso 2 — declaración)
            if (isStep2) {
                const sigInput = this.querySelector('input[name="firma_declarante"]');
                if (sigInput && !sigInput.value) {
                    const sigErr = document.getElementById('sig-error');
                    if (sigErr) { sigErr.style.display = 'block'; sigErr.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                    alert('La firma es obligatoria. Por favor firme el documento antes de enviar.');
                    return;
                }
            }
            
            const formData = new FormData(this);
            
            // Agregar archivos seleccionados
            selectedFiles.forEach((file, index) => {
                formData.append(`document_${index}`, file);
            });
            formData.append('file_count', selectedFiles.length);
            
            const messageDiv = document.getElementById('message');
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Resetear estado
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
            messageDiv.style.display = 'none';
            messageDiv.className = 'message';
            
            // Determinar URL de envío según el paso
            const submitUrl = isStep2 
                ? '<?= $_ENV['APP_URL'] ?>/form/declaracion/store'
                : '<?= $_ENV['APP_URL'] ?>/form/store-pdf';
            
            try {
                const response = await fetch(submitUrl, {
                    method: 'POST',
                    body: formData
                });
                
                // Verificar que la respuesta sea JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Respuesta no JSON recibida:', text.substring(0, 500));
                    
                    // Intentar extraer mensaje de error del HTML
                    let errorMessage = 'Error del servidor. Por favor contacta al administrador.';
                    
                    // Buscar errores comunes en el HTML
                    if (text.includes('Parse error') || text.includes('syntax error')) {
                        errorMessage = 'Error de sintaxis en el servidor. Contacta al administrador.';
                    } else if (text.includes('Fatal error')) {
                        errorMessage = 'Error fatal en el servidor. Contacta al administrador.';
                    } else if (text.includes('Warning')) {
                        errorMessage = 'Advertencia del servidor. El formulario puede no haberse guardado correctamente.';
                    } else if (text.includes('Unexpected token')) {
                        errorMessage = 'Error de formato en la respuesta del servidor.';
                    }
                    
                    throw new Error(errorMessage);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message;
                    messageDiv.style.display = 'block';
                    scrollToMessage();
                    
                    // Si necesita llenar declaración (paso 1), redirigir
                    if (data.needs_declaracion && data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1500);
                    } else {
                        // Si es paso 2 o no necesita declaración, ir a página de éxito
                        setTimeout(() => {
                            window.location.href = '<?= $_ENV['APP_URL'] ?>/form/success';
                        }, 2000);
                    }
                } else {
                    throw new Error(data.error || 'Error al enviar el formulario');
                }
            } catch (error) {
                console.error('Error completo:', error);
                messageDiv.className = 'message error';
                messageDiv.textContent = error.message;
                messageDiv.style.display = 'block';
                scrollToMessage();
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Formulario';
            }
        });
        } // Fin initFormSubmit
    </script>
    
    <!-- Sistema de búsqueda de direcciones -->
    <script src="/gestion-sagrilaft/public/assets/js/map-location-picker.js?v=2"></script>
    <script>
        // Inicializar búsqueda para todos los formularios PDF
        document.addEventListener('DOMContentLoaded', function() {
            // Cliente Natural
            if (document.getElementById('direccion-cliente-natural')) {
                initMapLocationPicker('direccion-cliente-natural', 'ciudad-cliente-natural');
            }
            // Proveedor Natural
            if (document.getElementById('direccion-proveedor-natural')) {
                initMapLocationPicker('direccion-proveedor-natural', 'ciudad-proveedor-natural');
            }
            // Cliente Jurídica
            if (document.getElementById('direccion-cliente-juridica')) {
                initMapLocationPicker('direccion-cliente-juridica', 'ciudad-cliente-juridica');
            }
            // Proveedor Jurídica
            if (document.getElementById('direccion-proveedor-juridica')) {
                initMapLocationPicker('direccion-proveedor-juridica', 'ciudad-proveedor-juridica');
            }
            // Proveedor Internacional - NO usar selector ICFES (es otro país)
            // El campo queda como input de texto simple
        });
    </script>
    
    <style>
        /* Responsive para formulario PDF */
        @media (max-width: 1024px) {
            .form-container {
                max-width: 100% !important;
                padding: 1.5rem !important;
            }
            
            .form-grid {
                grid-template-columns: 1fr !important;
            }
            
            .form-section {
                padding: 1rem !important;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 0.75rem !important;
            }
            
            .form-container {
                padding: 1rem !important;
            }
            
            .form-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.75rem !important;
            }
            
            .form-header img {
                width: 60px !important;
                height: 60px !important;
            }
            
            .form-header h1 {
                font-size: 1.1rem !important;
            }
            
            .form-section h2 {
                font-size: 1rem !important;
            }
            
            .form-section h3 {
                font-size: 0.9rem !important;
            }
            
            .checkbox-group {
                flex-direction: column !important;
                gap: 0.5rem !important;
            }
            
            .button-group {
                flex-direction: column !important;
            }
            
            .button-group button {
                width: 100% !important;
            }
            
            input, select, textarea {
                font-size: 0.8rem !important;
                padding: 0.6rem !important;
            }
            
            label {
                font-size: 0.8rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                padding: 0.75rem !important;
            }
            
            .form-header h1 {
                font-size: 0.95rem !important;
            }
            
            .form-section {
                padding: 0.75rem !important;
            }
            
            .form-section h2 {
                font-size: 0.9rem !important;
            }
            
            button {
                font-size: 0.75rem !important;
                padding: 0.6rem !important;
            }
        }
    </style>
</body>
</html>
