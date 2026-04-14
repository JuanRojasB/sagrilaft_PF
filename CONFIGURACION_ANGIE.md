# 👤 CONFIGURACIÓN USUARIO ANGIE - OFICIAL DE CUMPLIMIENTO

## 📋 RESUMEN DE CAMBIOS

### ✅ 1. SECCIÓN "ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO"

**Ubicación:** `app/Views/approval/form.php` (líneas 254-290)

Nueva sección agregada que muestra:

- ✅ Vinculación (Nueva/Actualización)
- ✅ Fecha de Vinculación
- ✅ Actualización (texto descriptivo)

**Título dinámico:**
- Para Clientes: "ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE CARTERA"
- Para Proveedores/Transportistas: "ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE COMPRAS"

---

### ✅ 2. SECCIÓN "ESPACIO EXCLUSIVO PARA POLLO FIESTA"

**Ubicación:** `app/Views/approval/form.php` (líneas 292-380)

La sección ya está implementada y visible para todos los revisores. Incluye:

- ✅ Consulta OFAC (Negativa/Positiva)
- ✅ Consulta Listas Nacionales (Negativa/Positiva)
- ✅ Consulta ONU (Negativa/Positiva)
- ✅ Consulta INTERPOL (Negativa/Positiva)
- ✅ Recibe (default: "Angie Paola Martínez Paredes")
- ✅ Verificado por (default: "Angie Paola Martínez Paredes")
- ✅ Director de Cartera (default: "Luz Mery Murillo")
- ✅ Gerencia Comercial (opciones: Hernan Mateo Benito, German Rodriguez)
- ✅ Preparó (lista de asesores comerciales)
- ✅ Revisó (default: "Angie Paola Martínez Paredes")
- ✅ Nombre Oficial de Cumplimiento (default: "Angie Paola Martínez Paredes")

**Nota:** Esta sección solo aparece para formularios SAGRILAFT (NO para empleados)

---

### ✅ 2. USUARIO PARA ANGIE

**Archivos creados:**
- `database/migrations/create_angie_user.sql` - Script SQL
- `database/migrations/setup_angie_user.php` - Script PHP de migración

**Datos del usuario:**
```
Nombre: Angie Paola Martínez Paredes
Email: oficialdecumplimiento@pollo-fiesta.com
Usuario sugerido: a.martinez
Password: angie1404*
Role: revisor
```

**Para crear el usuario, ejecutar:**
```bash
# Opción 1: Ejecutar el script PHP
php database/migrations/setup_angie_user.php

# Opción 2: Ejecutar el SQL directamente
mysql -u [usuario] -p [base_de_datos] < database/migrations/create_angie_user.sql
```

**Para agregar los campos de vinculación (si no existen):**
```bash
mysql -u [usuario] -p [base_de_datos] < database/migrations/add_vinculacion_fields.sql
```

**Después de crear el usuario:**
1. ✅ Angie puede iniciar sesión en: `https://pollo-fiesta.com/gestion-sagrilaft/public/login`
2. ⚠️ CAMBIAR la contraseña temporal inmediatamente
3. ✅ Subir su firma digital desde el panel de administración

---

### ✅ 3. CONFIGURACIÓN DE CORREOS

**Estado actual:** ✅ TODOS los correos se envían a `pasantesistemas1@pollo-fiesta.com`

**Ubicación:** `app/Controllers/ApprovalController.php` (líneas 590-620)

```php
// MODO PRODUCCIÓN: Enviar a pasantesistemas1@pollo-fiesta.com
$recipients[] = [
    'email' => 'pasantesistemas1@pollo-fiesta.com',
    'name' => 'Sistemas Pollo Fiesta',
    'type' => 'admin'
];
```

**Destinatarios futuros (COMENTADOS - para activar después):**
```php
/* DESACTIVADO TEMPORALMENTE - Descomentar cuando esté listo

// 1. Creador del formulario
$recipients[] = [
    'email' => $creator['email'],
    'name' => $creator['name'] ?? 'Usuario',
    'type' => 'creator'
];

// 2. Asesor comercial y su jefe (si existe)
if (!empty($form['asesor_comercial_id'])) {
    // ... código para agregar asesor y jefe
}

*/
```

---

## 🔐 ACCESO AL SISTEMA

### Para Angie (Oficial de Cumplimiento):

**URL de acceso:**
```
https://pollo-fiesta.com/gestion-sagrilaft/public/login
```

**Credenciales iniciales:**
```
Email: oficialdecumplimiento@pollo-fiesta.com
Password: angie1404*
```

**Después del primer login:**
1. (Opcional) Cambiar contraseña si lo desea
2. Ir a "Mi Perfil" o "Configuración"
3. Subir firma digital (imagen PNG o JPG)

---

## 📧 FLUJO DE CORREOS ACTUAL

### Cuando se envía un formulario:
```
Usuario → Formulario → Sistema → Email a: pasantesistemas1@pollo-fiesta.com
```

### Cuando Angie aprueba/rechaza:
```
Angie → Aprueba/Rechaza → Sistema → Email a: pasantesistemas1@pollo-fiesta.com
                                   → Adjunto: PDF consolidado con:
                                      - Formulario principal
                                      - Declaración de fondos
                                      - Firma de Angie
                                      - Firma del usuario
                                      - Campos de Pollo Fiesta llenos
                                      - Documentos adjuntos
```

---

## 🎯 FIRMA DIGITAL DE ANGIE

### Cómo funciona:

1. **Angie sube su firma** en el panel de administración
2. **Se guarda en la tabla `users`:**
   - Campo: `firma_digital` (imagen en base64)
   - Campo: `firma_mime_type` (tipo: image/png o image/jpeg)

3. **Cuando Angie aprueba un formulario:**
   - El sistema obtiene su firma desde `$_SESSION['reviewer_id']`
   - La agrega al PDF en el campo "FIRMA DEL OFICIAL DE CUMPLIMIENTO"
   - La propaga a las declaraciones relacionadas

### Código relevante:
```php
// app/Controllers/ApprovalController.php (líneas 683-695)
$reviewerId = $_SESSION['reviewer_id'] ?? null;
if ($reviewerId) {
    $stmtRevisor = $db->prepare("SELECT firma_digital, firma_mime_type FROM users WHERE id = ? AND role = 'revisor' LIMIT 1");
    $stmtRevisor->execute([$reviewerId]);
    $firmaRevisor = $stmtRevisor->fetch(PDO::FETCH_ASSOC);
    
    if ($firmaRevisor && !empty($firmaRevisor['firma_digital'])) {
        $mainForm['firma_oficial_data'] = 'data:' . ($firmaRevisor['firma_mime_type'] ?? 'image/png') . ';base64,' . $firmaRevisor['firma_digital'];
        $mainForm['firma_oficial_cumplimiento_data'] = $mainForm['firma_oficial_data'];
    }
}
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [x] Sección "ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO" agregada
- [x] Sección "ESPACIO EXCLUSIVO PARA POLLO FIESTA" visible en la vista de aprobación
- [x] Script SQL para crear usuario de Angie
- [x] Script PHP para crear usuario de Angie
- [x] Script SQL para agregar campos de vinculación
- [x] Configuración de correos a pasantesistemas1@pollo-fiesta.com
- [x] Sistema de firma digital del revisor implementado
- [x] PDF consolidado con todos los campos llenos
- [ ] **PENDIENTE:** Ejecutar script para agregar campos de vinculación
- [ ] **PENDIENTE:** Ejecutar script para crear usuario de Angie
- [ ] **PENDIENTE:** Angie debe subir su firma digital

---

## 🚀 PRÓXIMOS PASOS

1. **Ejecutar migraciones:**
   ```bash
   # Agregar campos de vinculación
   mysql -u [usuario] -p [base_de_datos] < database/migrations/add_vinculacion_fields.sql
   
   # Crear usuario de Angie
   php database/migrations/setup_angie_user.php
   ```

2. **Enviar credenciales a Angie:**
   - Email: oficialdecumplimiento@pollo-fiesta.com
   - Password: angie1404*
   - URL: https://pollo-fiesta.com/gestion-sagrilaft/public/login

3. **Angie debe:**
   - Iniciar sesión
   - (Opcional) Cambiar contraseña
   - Subir firma digital

4. **Cuando esté lista la firma:**
   - Probar aprobación de un formulario
   - Verificar que el PDF tenga su firma
   - Verificar que el correo llegue correctamente

5. **Cuando todo esté probado:**
   - Descomentar las líneas en `ApprovalController.php` para enviar correos a los destinatarios reales
   - Actualizar `NotificationConfig.php` si es necesario

---

## 📞 SOPORTE

Si hay algún problema:
1. Revisar logs en `app/Logs/`
2. Verificar que el usuario existe: `SELECT * FROM users WHERE email = 'oficialdecumplimiento@pollo-fiesta.com'`
3. Verificar que la firma esté guardada: `SELECT firma_digital, firma_mime_type FROM users WHERE email = 'oficialdecumplimiento@pollo-fiesta.com'`
4. Verificar sesión del revisor: `$_SESSION['reviewer_id']`

---

**Fecha de creación:** 2026-04-14  
**Última actualización:** 2026-04-14  
**Responsable:** Sistemas Pollo Fiesta
