<?php
/**
 * Test de Sistema de Firma Electrónica
 * Verifica que el modal de firma funcione correctamente
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Sistema de Firma</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/signature-modal.css">
    
    <style>
        body {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            background: var(--bg-primary);
        }
        
        .test-container {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        h1 {
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        h2 {
            color: var(--text-primary);
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .description {
            color: var(--text-secondary);
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .signature-section {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .signature-label {
            display: block;
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .signature-display {
            background: white;
            border: 2px dashed var(--border-color);
            border-radius: 4px;
            padding: 15px;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .signature-display img {
            max-width: 100%;
            max-height: 100px;
            border: 1px solid var(--border-color);
        }
        
        .signature-display.empty {
            color: var(--text-tertiary);
            font-style: italic;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .test-result {
            background: var(--bg-tertiary);
            border-left: 4px solid var(--success-color);
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        
        .test-result.error {
            border-left-color: var(--error-color);
        }
        
        .test-result h3 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }
        
        .test-result pre {
            background: var(--bg-primary);
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .status-badge.success {
            background: rgba(34, 197, 94, 0.15);
            color: var(--success-color);
        }
        
        .status-badge.pending {
            background: rgba(251, 191, 36, 0.15);
            color: var(--warning-color);
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🖊️ Test del Sistema de Firma Electrónica</h1>
        <p class="description">
            Esta página permite probar TODAS las firmas utilizadas en el proyecto SAGRILAFT.
            Total: <strong>11 tipos de firmas diferentes</strong>
        </p>
        
        <h2>📋 Firmas de Clientes</h2>
        
        <div class="signature-section">
            <label class="signature-label">
                1. Firma Oficial - Cliente Jurídica (CJ)
                <span class="status-badge pending" id="status-cj">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-cj">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-cj" name="firma_cj">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('cj')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('cj')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('cj')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                2. Firma Oficial - Cliente Natural (CN)
                <span class="status-badge pending" id="status-cn">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-cn">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-cn" name="firma_cn">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('cn')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('cn')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('cn')">📋 Ver Datos</button>
            </div>
        </div>
        
        <h2>🏢 Firmas de Proveedores</h2>
        
        <div class="signature-section">
            <label class="signature-label">
                3. Firma Proveedor - Persona Natural (PN)
                <span class="status-badge pending" id="status-prov-pn">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-prov-pn">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-prov-pn" name="firma_prov_pn">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('prov-pn')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('prov-pn')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('prov-pn')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                4. Firma Oficial - Proveedor Natural (PN)
                <span class="status-badge pending" id="status-ofic-pn">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-ofic-pn">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-ofic-pn" name="firma_ofic_pn">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('ofic-pn')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('ofic-pn')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('ofic-pn')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                5. Firma Preparador - Proveedor Jurídica (PJ)
                <span class="status-badge pending" id="status-prep-pj">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-prep-pj">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-prep-pj" name="firma_prep_pj">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('prep-pj')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('prep-pj')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('prep-pj')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                6. Firma Oficial - Proveedor Jurídica (PJ)
                <span class="status-badge pending" id="status-ofic-pj">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-ofic-pj">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-ofic-pj" name="firma_ofic_pj">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('ofic-pj')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('ofic-pj')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('ofic-pj')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                7. Firma Proveedor - Internacional (PI)
                <span class="status-badge pending" id="status-prov-pi">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-prov-pi">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-prov-pi" name="firma_prov_pi">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('prov-pi')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('prov-pi')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('prov-pi')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                8. Firma Oficial - Proveedor Internacional (PI)
                <span class="status-badge pending" id="status-ofic-pi">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-ofic-pi">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-ofic-pi" name="firma_ofic_pi">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('ofic-pi')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('ofic-pi')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('ofic-pi')">📋 Ver Datos</button>
            </div>
        </div>
        
        <h2>📝 Firmas de Declaraciones</h2>
        
        <div class="signature-section">
            <label class="signature-label">
                9. Firma Declarante - Fondos Clientes (FC)
                <span class="status-badge pending" id="status-dec-fc">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-dec-fc">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-dec-fc" name="firma_dec_fc">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('dec-fc')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('dec-fc')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('dec-fc')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                10. Firma Oficial - Fondos Clientes (FC)
                <span class="status-badge pending" id="status-ofic-fc">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-ofic-fc">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-ofic-fc" name="firma_ofic_fc">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('ofic-fc')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('ofic-fc')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('ofic-fc')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                11. Firma Declarante - Fondos Proveedores (FP)
                <span class="status-badge pending" id="status-dec-fp">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-dec-fp">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-dec-fp" name="firma_dec_fp">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('dec-fp')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('dec-fp')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('dec-fp')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div class="signature-section">
            <label class="signature-label">
                12. Firma Oficial - Fondos Proveedores (FP)
                <span class="status-badge pending" id="status-ofic-fp">Sin firmar</span>
            </label>
            <div class="signature-display empty" id="display-ofic-fp">
                <span>No hay firma</span>
            </div>
            <input type="hidden" id="firma-ofic-fp" name="firma_ofic_fp">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="openSignature('ofic-fp')">✍️ Firmar</button>
                <button type="button" class="btn btn-secondary" onclick="clearSignature('ofic-fp')">🗑️ Limpiar</button>
                <button type="button" class="btn btn-info" onclick="showSignatureData('ofic-fp')">📋 Ver Datos</button>
            </div>
        </div>
        
        <div id="test-results"></div>
    </div>
    
    <!-- Modales de firma - TODAS LAS FIRMAS DEL PROYECTO -->
    
    <!-- Clientes -->
    <div id="sigModal-cj" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Oficial - Cliente Jurídica</h3>
                <button class="signature-modal-close" onclick="closeSignature('cj')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-cj"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('cj')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('cj')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-cn" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Oficial - Cliente Natural</h3>
                <button class="signature-modal-close" onclick="closeSignature('cn')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-cn"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('cn')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('cn')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <!-- Proveedores -->
    <div id="sigModal-prov-pn" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Proveedor - Persona Natural</h3>
                <button class="signature-modal-close" onclick="closeSignature('prov-pn')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-prov-pn"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('prov-pn')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('prov-pn')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-ofic-pn" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Oficial - Proveedor Natural</h3>
                <button class="signature-modal-close" onclick="closeSignature('ofic-pn')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-ofic-pn"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('ofic-pn')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('ofic-pn')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-prep-pj" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Preparador - Proveedor Jurídica</h3>
                <button class="signature-modal-close" onclick="closeSignature('prep-pj')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-prep-pj"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('prep-pj')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('prep-pj')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-ofic-pj" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Oficial - Proveedor Jurídica</h3>
                <button class="signature-modal-close" onclick="closeSignature('ofic-pj')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-ofic-pj"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('ofic-pj')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('ofic-pj')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-prov-pi" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Proveedor - Internacional</h3>
                <button class="signature-modal-close" onclick="closeSignature('prov-pi')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-prov-pi"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('prov-pi')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('prov-pi')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-ofic-pi" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Oficial - Proveedor Internacional</h3>
                <button class="signature-modal-close" onclick="closeSignature('ofic-pi')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-ofic-pi"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('ofic-pi')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('ofic-pi')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <!-- Declaraciones -->
    <div id="sigModal-dec-fc" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Declarante - Fondos Clientes</h3>
                <button class="signature-modal-close" onclick="closeSignature('dec-fc')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-dec-fc"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('dec-fc')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('dec-fc')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-ofic-fc" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Oficial - Fondos Clientes</h3>
                <button class="signature-modal-close" onclick="closeSignature('ofic-fc')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-ofic-fc"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('ofic-fc')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('ofic-fc')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-dec-fp" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Declarante - Fondos Proveedores</h3>
                <button class="signature-modal-close" onclick="closeSignature('dec-fp')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-dec-fp"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('dec-fp')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('dec-fp')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <div id="sigModal-ofic-fp" class="signature-modal">
        <div class="signature-modal-content">
            <div class="signature-modal-header">
                <h3>Firma Oficial - Fondos Proveedores</h3>
                <button class="signature-modal-close" onclick="closeSignature('ofic-fp')">&times;</button>
            </div>
            <div class="signature-modal-body">
                <canvas id="sigCanvas-ofic-fp"></canvas>
            </div>
            <div class="signature-modal-footer">
                <button class="btn btn-secondary" onclick="clearCanvas('ofic-fp')">Limpiar</button>
                <button class="btn btn-primary" onclick="saveSignature('ofic-fp')">Guardar Firma</button>
            </div>
        </div>
    </div>
    
    <script src="/gestion-sagrilaft/public/assets/js/signature-pad.js"></script>
    <script>
        // Instancias de SignaturePad para cada firma
        const signaturePads = {};
        
        function openSignature(type) {
            const modal = document.getElementById('sigModal-' + type);
            const canvas = document.getElementById('sigCanvas-' + type);
            
            modal.style.display = 'flex';
            
            // Inicializar SignaturePad si no existe
            if (!signaturePads[type]) {
                signaturePads[type] = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });
                
                // Ajustar tamaño del canvas
                resizeCanvas(canvas);
            }
        }
        
        function closeSignature(type) {
            const modal = document.getElementById('sigModal-' + type);
            modal.style.display = 'none';
        }
        
        function clearCanvas(type) {
            if (signaturePads[type]) {
                signaturePads[type].clear();
            }
        }
        
        function saveSignature(type) {
            const pad = signaturePads[type];
            
            if (!pad || pad.isEmpty()) {
                alert('Por favor, firme antes de guardar');
                return;
            }
            
            // Obtener imagen en base64
            const dataURL = pad.toDataURL('image/png');
            
            // Guardar en input hidden
            document.getElementById('firma-' + type).value = dataURL;
            
            // Mostrar en display
            const display = document.getElementById('display-' + type);
            display.innerHTML = '<img src="' + dataURL + '" alt="Firma">';
            display.classList.remove('empty');
            
            // Actualizar estado
            const status = document.getElementById('status-' + type);
            status.textContent = 'Firmado ✓';
            status.classList.remove('pending');
            status.classList.add('success');
            
            // Cerrar modal
            closeSignature(type);
            
            console.log('Firma guardada para:', type);
        }
        
        function clearSignature(type) {
            // Limpiar input
            document.getElementById('firma-' + type).value = '';
            
            // Limpiar display
            const display = document.getElementById('display-' + type);
            display.innerHTML = '<span>No hay firma</span>';
            display.classList.add('empty');
            
            // Actualizar estado
            const status = document.getElementById('status-' + type);
            status.textContent = 'Sin firmar';
            status.classList.remove('success');
            status.classList.add('pending');
            
            // Limpiar canvas si existe
            if (signaturePads[type]) {
                signaturePads[type].clear();
            }
            
            console.log('Firma limpiada para:', type);
        }
        
        function showSignatureData(type) {
            const input = document.getElementById('firma-' + type);
            const data = input.value;
            
            const resultsDiv = document.getElementById('test-results');
            
            if (!data) {
                resultsDiv.innerHTML = `
                    <div class="test-result error">
                        <h3>❌ No hay firma para "${type}"</h3>
                        <p>Por favor, firme primero antes de ver los datos.</p>
                    </div>
                `;
                return;
            }
            
            const size = new Blob([data]).size;
            const sizeKB = (size / 1024).toFixed(2);
            
            resultsDiv.innerHTML = `
                <div class="test-result">
                    <h3>✅ Datos de la firma "${type}"</h3>
                    <p><strong>Formato:</strong> PNG (Base64)</p>
                    <p><strong>Tamaño:</strong> ${sizeKB} KB</p>
                    <p><strong>Longitud:</strong> ${data.length} caracteres</p>
                    <p><strong>Preview:</strong></p>
                    <pre>${data.substring(0, 100)}...</pre>
                </div>
            `;
        }
        
        function resizeCanvas(canvas) {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.classList.contains('signature-modal')) {
                event.target.style.display = 'none';
            }
        }
        
        // Ajustar canvas al redimensionar ventana
        window.addEventListener('resize', function() {
            Object.keys(signaturePads).forEach(type => {
                const canvas = document.getElementById('sigCanvas-' + type);
                if (canvas) {
                    resizeCanvas(canvas);
                }
            });
        });
        
        console.log('✅ Sistema de firma cargado correctamente');
        console.log('📝 Total de firmas disponibles: 12');
        console.log('📋 Clientes: 2 firmas (CJ, CN)');
        console.log('🏢 Proveedores: 6 firmas (PN x2, PJ x2, PI x2)');
        console.log('📝 Declaraciones: 4 firmas (FC x2, FP x2)');
    </script>
</body>
</html>
