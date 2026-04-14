# 📧 CORREOS CONFIGURADOS - Sistema SAGRILAFT

## ✅ CORREOS ACTUALIZADOS

### OFICIAL DE CUMPLIMIENTO
- **Angie Paola Martínez**: `oficialdecumplimiento@pollo-fiesta.com`

### CARTERA (2 correos)
- **Cartera General**: `cartera@pollo-fiesta.com`
- **Eymis Carey**: `eymis.carey@pollo-fiesta.com`

### COMPRAS
- **Compras General**: `compras@pollo-fiesta.com`

### CONTABILIDAD (2 correos)
- **Esperanza Aguilar**: `esperanza.aguilar@pollo-fiesta.com`
- **Alejandra Camargo**: `alejandra.camargo@pollo-fiesta.com`

### TESORERÍA
- **Asistente Tesorería**: `asistesoreria@pollo-fiesta.com`

### RUTAS/LOGÍSTICA
- **Control de Rutas**: `controlderutas@pollo-fiesta.com`

### GESTIÓN HUMANA
- **Selección de Personal**: `seleccionpersonal@pollo-fiesta.com`

---

## 📋 DISTRIBUCIÓN DE CORREOS POR TIPO

### CLIENTES - APROBADO
Reciben:
- ✅ Cliente (creador del formulario)
- ✅ Asesor Comercial (si existe)
- ✅ Gerente Comercial (jefe del asesor, si existe)
- ✅ `cartera@pollo-fiesta.com`
- ✅ `eymis.carey@pollo-fiesta.com`
- ✅ `oficialdecumplimiento@pollo-fiesta.com`

### CLIENTES - RECHAZADO
Reciben:
- ✅ Asesor Comercial (si existe)
- ✅ Gerente Comercial (jefe del asesor, si existe)

---

### PROVEEDORES - APROBADO
Reciben:
- ✅ `compras@pollo-fiesta.com`
- ✅ `esperanza.aguilar@pollo-fiesta.com`
- ✅ `alejandra.camargo@pollo-fiesta.com`
- ✅ `asistesoreria@pollo-fiesta.com`
- ✅ Área solicitante (si existe en el formulario)

### PROVEEDORES - RECHAZADO
Reciben:
- ✅ `compras@pollo-fiesta.com`
- ✅ Área solicitante (si existe en el formulario)

---

### TRANSPORTISTAS - APROBADO
Reciben:
- ✅ `controlderutas@pollo-fiesta.com`

### TRANSPORTISTAS - RECHAZADO
Reciben:
- ✅ `controlderutas@pollo-fiesta.com`

---

### EMPLEADOS - APROBADO
Reciben:
- ✅ `seleccionpersonal@pollo-fiesta.com`

### EMPLEADOS - RECHAZADO
Reciben:
- ✅ `seleccionpersonal@pollo-fiesta.com`

---

## 🔄 MODO ACTUAL: TEST

**Todos los correos se envían a**: `pasantesistemas1@pollo-fiesta.com`

Para activar el envío a los correos reales:
1. Abrir: `app/Config/EmailRecipientsConfig.php`
2. Línea 12: Cambiar `const TEST_MODE = true;` a `const TEST_MODE = false;`
3. Guardar archivo

---

## 📝 CORREOS PENDIENTES (Para agregar después)

Si necesitas agregar más correos específicos, estos son los que faltan:

### CARTERA (opcional - ya tienes el general):
- Camila 1: `pendiente`
- Camila 2: `pendiente`
- Directora Cartera: `pendiente`

### COMPRAS (opcional - ya tienes el general):
- Briyith: `pendiente`

### TESORERÍA (opcional - ya tienes asistente):
- Keyner: `pendiente`

### RUTAS (opcional - ya tienes control):
- Diego (Gerente Logístico): `pendiente`

### GESTIÓN HUMANA (opcional - ya tienes selección):
- Yohanna: `pendiente`
- Elsa: `pendiente`

---

## ✅ ESTADO ACTUAL

- **Total correos configurados**: 9
- **Modo**: TEST (todos van a pasantesistemas1@pollo-fiesta.com)
- **Archivo**: `app/Config/EmailRecipientsConfig.php`
- **Última actualización**: Abril 2026

---

## 🚀 CÓMO AGREGAR MÁS CORREOS

Cuando tengas más correos, solo necesitas:

1. Abrir: `app/Config/EmailRecipientsConfig.php`
2. Buscar la sección correspondiente (CLIENTES, PROVEEDORES, etc.)
3. Agregar línea:
   ```php
   $recipients[] = ['email' => 'nuevo@pollo-fiesta.com', 'name' => 'Nombre', 'type' => 'tipo'];
   ```
4. Guardar archivo

No necesitas tocar ningún otro archivo, todo está centralizado.
