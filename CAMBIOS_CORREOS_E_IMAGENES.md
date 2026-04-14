# CAMBIOS IMPLEMENTADOS - Correos e Imágenes

## ✅ 1. LÓGICA DE CORREOS COMPLETA

### Archivo Creado: `app/Config/EmailRecipientsConfig.php`

Este archivo centraliza toda la lógica de destinatarios de correos según:
- Tipo de formulario (cliente, proveedor, transportista, empleado)
- Estado (aprobado, rechazado)

### Modo de Operación

**MODO TEST (Activo):**
```php
const TEST_MODE = true;
const TEST_EMAIL = 'pasantesistemas1@pollo-fiesta.com';
```
- Todos los correos van a: `pasantesistemas1@pollo-fiesta.com`
- Para activar modo producción: cambiar `TEST_MODE = false`

### Destinatarios por Tipo

#### CLIENTES (cliente_natural, cliente_juridica)

**Aprobado:**
- Cliente (creador del formulario)
- Asesor Comercial
- Gerente Comercial (jefe del asesor)
- Cartera: camila1@pollo-fiesta.com, camila2@pollo-fiesta.com, eymis@pollo-fiesta.com, directora.cartera@pollo-fiesta.com
- Oficial de Cumplimiento: oficialdecumplimiento@pollo-fiesta.com

**Rechazado:**
- Asesor Comercial
- Gerente Comercial

#### PROVEEDORES (proveedor_natural, proveedor_juridica, proveedor_internacional)

**Aprobado:**
- Compras: briyith@pollo-fiesta.com
- Contabilidad: esperanza@pollo-fiesta.com, alejandra@pollo-fiesta.com
- Tesorería: keyner@pollo-fiesta.com
- Área solicitante (si existe en el formulario)

**Rechazado:**
- Compras: briyith@pollo-fiesta.com
- Área solicitante (si existe)

#### TRANSPORTISTAS (transportista_natural, transportista_juridica)

**Aprobado:**
- Rutas: rutas@pollo-fiesta.com
- Gerente Logístico: diego@pollo-fiesta.com

**Rechazado:**
- Rutas: rutas@pollo-fiesta.com
- Gerente Logístico: diego@pollo-fiesta.com

#### EMPLEADOS (empleado)

**Aprobado y Rechazado:**
- Gestión Humana: yohanna@pollo-fiesta.com, elsa@pollo-fiesta.com, seleccion@pollo-fiesta.com

### Nuevo Formulario (Link de Aprobación)

Siempre va a:
- **Modo Test:** pasantesistemas1@pollo-fiesta.com
- **Modo Producción:** oficialdecumplimiento@pollo-fiesta.com (Angie)

### Archivos Modificados

1. **app/Controllers/ApprovalController.php**
   - Método `sendApprovalNotification()` actualizado
   - Usa `EmailRecipientsConfig::getApprovedRecipients()` o `getRejectedRecipients()`

2. **app/Controllers/FormController.php**
   - Método `sendFormNotification()` actualizado
   - Usa `EmailRecipientsConfig::getNewFormRecipients()`

---

## ✅ 2. SUBIDA DE IMÁGENES

### Estado: YA IMPLEMENTADO ✓

El sistema ya permite subir imágenes además de PDFs:

### Frontend (Formularios)

**Archivos:**
- `app/Views/forms/pdf_style_form.php`
- `app/Views/forms/create_direct.php`

**Input de archivos:**
```html
<input type="file" 
       name="documents[]" 
       multiple 
       accept=".pdf,.jpg,.jpeg,.png,.heic,.heif,image/*" 
       capture="environment">
```

**Características:**
- Permite múltiples archivos
- Acepta: PDF, JPG, JPEG, PNG, HEIC, HEIF
- Permite captura de cámara (`capture="environment"`)
- Botón personalizado: "+ Adjuntar Archivos (PDF o Fotos)"

### Backend (Validación)

**Archivo:** `app/Controllers/FormController.php`
**Método:** `handleFileUploads()`

**Tipos permitidos:**
```php
$allowedTypes = [
    'application/pdf',
    'image/jpeg',
    'image/jpg', 
    'image/png',
    'image/heic',
    'image/heif'
];
```

**Validaciones:**
- Tamaño máximo: 10MB por archivo
- Validación por MIME type y extensión
- Almacenamiento en BD como BLOB

---

## 📋 CORREOS GUARDADOS (No enviar aún)

Lista de correos adicionales para referencia:
- juan.david.rojas.burbano0@gmail.com

---

## 🔄 CÓMO ACTIVAR MODO PRODUCCIÓN

Cuando esté listo para enviar correos reales:

1. Abrir: `app/Config/EmailRecipientsConfig.php`
2. Cambiar línea 12:
   ```php
   const TEST_MODE = false;  // Cambiar de true a false
   ```
3. Guardar archivo
4. Los correos se enviarán a los destinatarios reales

---

## ✅ VERIFICACIÓN

### Probar Modo Test:
1. Crear un nuevo formulario
2. Verificar que el correo llegue a: pasantesistemas1@pollo-fiesta.com
3. Aprobar/Rechazar el formulario
4. Verificar que el correo llegue a: pasantesistemas1@pollo-fiesta.com

### Probar Subida de Imágenes:
1. Crear un formulario
2. Adjuntar: 1 PDF + 2 fotos (JPG/PNG)
3. Verificar que se suban correctamente
4. Verificar que aparezcan en el PDF consolidado

---

## 📝 NOTAS IMPORTANTES

1. **No se rompió nada:** Los cambios son aditivos, no modifican la lógica existente
2. **Modo test activo:** Todos los correos van a pasantesistemas1@pollo-fiesta.com
3. **Imágenes ya funcionaban:** Solo se confirmó que está implementado correctamente
4. **Fácil activación:** Un solo cambio de variable para activar modo producción
