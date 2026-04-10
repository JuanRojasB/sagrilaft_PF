# Funcionalidad: Registro de Empleados

## Descripción General

Se ha agregado un nuevo tipo de usuario "Empleado" al sistema SAGRILAFT que permite registrar empleados de forma simple y rápida. Esta funcionalidad está diseñada para facilitar el registro masivo de empleados (hasta 40 o más) con un formulario simplificado.

## Características Principales

### 1. Formulario Simplificado
El formulario de empleado incluye únicamente los campos esenciales:
- **Nombre Completo**: Nombre del empleado
- **Cédula**: Número de identificación
- **Cargo**: Puesto que ocupa
- **Fecha de Nacimiento**: Fecha de nacimiento del empleado
- **PDF de Cédula** (OPCIONAL): Documento de identidad en formato PDF

### 2. PDF Opcional
El campo de PDF de cédula es **opcional** porque:
- Permite enviar múltiples empleados sin adjuntar el mismo PDF repetidamente
- Útil cuando varios empleados comparten un documento consolidado
- Reduce el tiempo de carga cuando se registran muchos empleados

### 3. Notificaciones Automáticas
- Los registros de empleados se envían automáticamente a **Angie** para aprobación
- Email configurado: `angie@pollo-fiesta.com`
- El email incluye todos los datos del empleado para revisión rápida

### 4. Código de Formato
- **Nomenclatura**: FD-09
- **Título**: RECURSOS HUMANOS - REGISTRO DE EMPLEADO
- **Versión**: 1

## Flujo de Trabajo

### Para el Usuario que Registra

1. **Acceder al Sistema**
   - Ir a la página principal del sistema SAGRILAFT
   - Completar los datos iniciales (nombre, email, teléfono)

2. **Seleccionar Tipo de Usuario**
   - En "Tipo de Usuario", seleccionar **"Empleado"**
   - El campo "Tipo de Persona" se oculta automáticamente (no es necesario para empleados)

3. **Completar el Formulario**
   - Ingresar nombre completo del empleado
   - Ingresar número de cédula
   - Ingresar cargo
   - Seleccionar fecha de nacimiento
   - (Opcional) Adjuntar PDF de la cédula

4. **Enviar**
   - Hacer clic en "Enviar Formulario"
   - El sistema genera el PDF y envía notificación a Angie

5. **Registros Múltiples**
   - Para registrar más empleados, repetir el proceso
   - No es necesario adjuntar el PDF en cada registro si ya se adjuntó previamente

### Para Angie (Revisora)

1. **Recibir Notificación**
   - Llega un email con el asunto: "SAGRILAFT - Nuevo Formulario para Aprobar"
   - El email contiene:
     - Nombre completo del empleado
     - Número de cédula
     - Cargo
     - Fecha de nacimiento
     - Lista de documentos adjuntos (si los hay)

2. **Revisar Formulario**
   - Hacer clic en el botón "Revisar Registro" del email
   - Se abre el formulario completo con todos los datos

3. **Aprobar o Rechazar**
   - **Aprobar**: Si los datos son correctos
   - **Rechazar**: Si hay errores o falta información
   - Agregar comentarios si es necesario

## Instalación y Configuración

### 1. Ejecutar Migración de Base de Datos

```bash
# Ejecutar el script SQL de migración
mysql -u usuario -p sagrilaft < database/migrations/add_empleado_type.sql
```

Este script:
- Agrega el tipo 'empleado' al ENUM de form_type
- Crea los campos específicos para empleados:
  - `empleado_nombre`
  - `empleado_cedula`
  - `empleado_cargo`
  - `empleado_fecha_nacimiento`
  - `empleado_pdf_cedula_required`
- Agrega índice para búsqueda por cédula

### 2. Configurar Email de Angie

El email de Angie está configurado en:
```php
// app/Config/NotificationConfig.php
const EMPLEADO_REVIEWER_EMAIL = 'angie@pollo-fiesta.com';
const EMPLEADO_REVIEWER_NAME = 'Angie';
```

Para cambiar el email, editar este archivo.

### 3. Verificar Archivos Creados/Modificados

**Archivos Nuevos:**
- `database/migrations/add_empleado_type.sql` - Migración de BD
- `app/Views/forms/pdf_forms/empleado.php` - Formulario de empleado
- `app/Config/NotificationConfig.php` - Configuración de notificaciones
- `docs/EMPLEADO_FEATURE.md` - Esta documentación

**Archivos Modificados:**
- `app/Controllers/FormController.php` - Lógica de formularios y notificaciones
- `app/Views/home/index.php` - Selector de tipo de usuario
- `app/Views/approval/dashboard.php` - Dashboard de aprobación
- `app/Views/approval/form.php` - Formulario de aprobación
- `app/Views/approval/already_processed.php` - Vista de procesados

## Casos de Uso

### Caso 1: Registro Individual con PDF
```
Usuario registra 1 empleado y adjunta su PDF de cédula
→ Se envía formulario con PDF adjunto
→ Angie recibe notificación con datos y PDF
→ Angie aprueba/rechaza
```

### Caso 2: Registro Masivo sin PDF Repetido
```
Usuario registra 40 empleados
→ Adjunta PDF consolidado en el primer registro
→ Los siguientes 39 registros NO incluyen PDF
→ Angie recibe 40 notificaciones
→ Revisa y aprueba/rechaza cada uno
```

### Caso 3: Registro sin PDF
```
Usuario registra empleados sin adjuntar PDF
→ El PDF se puede adjuntar después si es necesario
→ O se puede aprobar sin PDF si no es requerido
```

## Preguntas Frecuentes

**¿Es obligatorio adjuntar el PDF de la cédula?**
No, el PDF es opcional. Puede enviarse sin documento adjunto.

**¿Puedo enviar varios empleados a la vez?**
No directamente, pero puede enviar múltiples formularios uno tras otro de forma rápida.

**¿Qué pasa si me equivoco en un dato?**
Angie puede rechazar el formulario con comentarios indicando el error. Luego puede volver a enviarlo corregido.

**¿Puedo cambiar el email de Angie?**
Sí, editando el archivo `app/Config/NotificationConfig.php`.

**¿Los empleados aparecen en el dashboard de aprobación?**
Sí, aparecen con el código FD-09 y pueden filtrarse por tipo.

## Soporte Técnico

Para problemas o dudas sobre esta funcionalidad, contactar al equipo de desarrollo.
