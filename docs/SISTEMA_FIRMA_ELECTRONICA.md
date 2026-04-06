# Sistema de Firma Electrónica

Sistema de firma electrónica similar a Adobe Sign para los formularios SAGRILAFT.

## Características

✅ **3 Métodos de Firma:**
1. **Dibujar** - Dibuja tu firma con el mouse o dedo (táctil)
2. **Escribir** - Escribe tu nombre y elige una fuente cursiva elegante
3. **Subir** - Sube una imagen de tu firma escaneada

✅ **Características Adicionales:**
- Interfaz moderna y profesional
- Compatible con dispositivos móviles (touch)
- Preview en tiempo real
- Múltiples fuentes cursivas
- Drag & drop para subir imágenes
- Validación de firma vacía

## Cómo Integrar en un Formulario

### 1. Agregar Campo de Firma en el HTML

```php
<!-- FIRMA DEL PROVEEDOR/CLIENTE -->
<div class="form-section">
    <div class="section-title">FIRMA Y SELLO</div>
    <div class="section-content">
        <table class="field-table">
            <tr>
                <td class="field-label" style="width: 20%;">FIRMA:</td>
                <td class="field-input" style="width: 80%;">
                    <!-- Campo oculto para guardar la firma -->
                    <input type="hidden" id="signature_data" name="signature_data">
                    
                    <!-- Preview de la firma -->
                    <div class="signature-container">
                        <img id="signature_preview" src="" alt="Firma" style="display: none; max-width: 300px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                        
                        <!-- Botones -->
                        <div style="margin-top: 10px;">
                            <button type="button" id="add_signature_btn" class="btn btn-primary" onclick="openSignatureModal()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-right: 6px;">
                                    <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                                    <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                                </svg>
                                Agregar Firma
                            </button>
                            
                            <button type="button" id="change_signature_btn" class="btn btn-secondary" onclick="openSignatureModal()" style="display: none;">
                                Cambiar Firma
                            </button>
                            
                            <button type="button" class="btn btn-secondary" onclick="clearSignature()" style="display: none;" id="clear_signature_btn">
                                Limpiar
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="field-label">NOMBRE:</td>
                <td class="field-input">
                    <input type="text" name="nombre_firma" placeholder="Nombre completo" required>
                </td>
            </tr>
        </table>
    </div>
</div>
```

### 2. Los Scripts ya están Incluidos

Los archivos ya están incluidos en `pdf_style_form.php`:
- `/public/assets/css/signature-modal.css`
- `/public/assets/js/signature-pad.js`

### 3. Guardar la Firma en el Backend

En tu controlador PHP, la firma viene como base64 en el campo `signature_data`:

```php
// FormController.php
public function store() {
    $signatureData = $_POST['signature_data'] ?? null;
    
    if ($signatureData) {
        // Opción 1: Guardar directamente en la base de datos
        $stmt = $this->db->prepare("
            INSERT INTO formularios (signature_data, created_at) 
            VALUES (?, NOW())
        ");
        $stmt->execute([$signatureData]);
        
        // Opción 2: Guardar como archivo de imagen
        $signatureFile = $this->saveSignatureAsFile($signatureData);
        
        // Opción 3: Ambas (recomendado)
        $stmt = $this->db->prepare("
            INSERT INTO formularios (signature_data, signature_file, created_at) 
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$signatureData, $signatureFile]);
    }
}

private function saveSignatureAsFile($base64Data) {
    // Extraer el tipo de imagen y los datos
    list($type, $data) = explode(';', $base64Data);
    list(, $data) = explode(',', $data);
    
    // Decodificar
    $data = base64_decode($data);
    
    // Generar nombre único
    $filename = 'signature_' . uniqid() . '.png';
    $filepath = 'uploads/signatures/' . $filename;
    
    // Crear directorio si no existe
    if (!is_dir('uploads/signatures')) {
        mkdir('uploads/signatures', 0755, true);
    }
    
    // Guardar archivo
    file_put_contents($filepath, $data);
    
    return $filepath;
}
```

### 4. Mostrar la Firma en el PDF

Para mostrar la firma en el PDF generado:

```php
// En tu servicio de generación de PDF
if (!empty($formData['signature_data'])) {
    // Convertir base64 a imagen temporal
    $signatureData = $formData['signature_data'];
    list($type, $data) = explode(';', $signatureData);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);
    
    $tempFile = tempnam(sys_get_temp_dir(), 'sig') . '.png';
    file_put_contents($tempFile, $data);
    
    // Agregar al PDF (ejemplo con FPDF)
    $pdf->Image($tempFile, $x, $y, $width, $height);
    
    // Limpiar archivo temporal
    unlink($tempFile);
}
```

## Estructura de la Base de Datos

Agrega estos campos a tu tabla de formularios:

```sql
ALTER TABLE formularios 
ADD COLUMN signature_data LONGTEXT NULL COMMENT 'Firma en formato base64',
ADD COLUMN signature_file VARCHAR(255) NULL COMMENT 'Ruta del archivo de firma',
ADD COLUMN signature_date DATETIME NULL COMMENT 'Fecha y hora de la firma';
```

## Estilos Personalizados

Puedes personalizar los colores y estilos editando `/public/assets/css/signature-modal.css`:

```css
/* Cambiar color primario */
.signature-btn-primary {
    background: #tu-color;
}

.signature-tab.active {
    color: #tu-color;
    border-bottom-color: #tu-color;
}
```

## Validación de Firma

Para hacer la firma obligatoria:

```javascript
// En el evento submit del formulario
document.getElementById('pdfForm').addEventListener('submit', function(e) {
    const signatureData = document.getElementById('signature_data').value;
    
    if (!signatureData) {
        e.preventDefault();
        alert('Por favor agrega tu firma antes de enviar el formulario');
        return false;
    }
});
```

## Funciones Disponibles

### JavaScript

```javascript
// Abrir modal de firma
openSignatureModal()

// Limpiar firma
clearSignature()

// Acceder al modal
signatureModal.open()
signatureModal.close()
signatureModal.save()
```

## Ejemplo Completo

Ver los archivos:
- `app/Views/forms/pdf_forms/proveedor_natural.php` (ejemplo de integración)
- `public/assets/js/signature-pad.js` (código JavaScript)
- `public/assets/css/signature-modal.css` (estilos)

## Soporte Móvil

El sistema es completamente compatible con dispositivos móviles:
- Touch events para dibujar con el dedo
- Interfaz responsive
- Teclado virtual para escribir firma

## Fuentes Disponibles

1. **Dancing Script** - Elegante y fluida
2. **Pacifico** - Moderna y amigable
3. **Great Vibes** - Clásica y formal
4. **Allura** - Sofisticada y estilizada

## Troubleshooting

### La firma no se guarda
- Verifica que el campo `signature_data` exista en el formulario
- Revisa la consola del navegador para errores JavaScript

### El modal no se abre
- Asegúrate de que los scripts estén cargados correctamente
- Verifica que `signatureModal` esté inicializado

### La firma se ve pixelada
- Aumenta el tamaño del canvas en `signature-pad.js`
- Usa formato PNG en lugar de JPEG

## Próximas Mejoras

- [ ] Soporte para múltiples firmas en un formulario
- [ ] Historial de firmas del usuario
- [ ] Verificación de firma con timestamp
- [ ] Exportar firma como SVG
- [ ] Integración con certificados digitales

## Licencia

Este sistema es parte del proyecto SAGRILAFT y está disponible para uso interno.
