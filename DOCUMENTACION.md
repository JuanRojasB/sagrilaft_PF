# 📘 Sistema SAGRILAFT - Documentación Completa

## 🎯 Estado Actual: FUNCIONAL ✅

La aplicación está completamente configurada y funcionando en:
```
https://pollo-fiesta.com/gestion-sagrilaft/public/index.php
```

---

## 📋 URLs Principales

### Acceso Público
- **Registro:** `https://pollo-fiesta.com/gestion-sagrilaft/public/index.php`
- **Formulario completo:** `https://pollo-fiesta.com/gestion-sagrilaft/public/index.php?route=/form/create`

### Acceso Administrativo
- **Login Admin/Revisor:** `https://pollo-fiesta.com/gestion-sagrilaft/public/index.php?route=/admin-access`
- **Dashboard Revisor:** `https://pollo-fiesta.com/gestion-sagrilaft/public/index.php?route=/reviewer/dashboard`
- **Dashboard Admin:** `https://pollo-fiesta.com/gestion-sagrilaft/public/index.php?route=/admin`

---

## 🚀 Instalación en cPanel

### 1. Estructura de Archivos
```
public_html/
├── gestion-sagrilaft/          # Raíz del proyecto
│   ├── .htaccess              # Redirige a /public
│   ├── .env                   # Configuración
│   ├── app/                   # Código de la aplicación
│   ├── database/              # SQL de instalación
│   ├── public/                # Directorio público
│   │   ├── .htaccess         # Configuración principal
│   │   ├── index.php         # Punto de entrada
│   │   └── assets/           # CSS, JS, imágenes
│   └── storage/              # Archivos generados
```

### 2. Configurar Base de Datos

**En cPanel > MySQL® Databases:**
1. Crear base de datos: `gestion_sagrilaft`
2. Crear usuario: `admin_sagrilaft`
3. Asignar todos los privilegios

**En phpMyAdmin:**
1. Seleccionar la base de datos
2. Importar: `database/INSTALACION_COMPLETA.sql`
3. Ejecutar:
```sql
SET GLOBAL max_allowed_packet=67108864;
```

### 3. Configurar .env

```env
# URL de la aplicación
APP_URL=https://pollo-fiesta.com/gestion-sagrilaft/public
APP_ENV=production

# Base de datos
DB_HOST=localhost
DB_NAME=wwpoll_gestion_sagrilaft
DB_USER=wwpoll_admin_sagrilaft
DB_PASS=v=^!RpZ67S&0_~j8

# Email (Office365)
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=innovacion@pollo-fiesta.com
MAIL_PASS=Sistemas2026*
MAIL_FROM=innovacion@pollo-fiesta.com
MAIL_FROM_NAME=SAGRILAFT - Pollo Fiesta
MAIL_ALERT_TO=pasantesistemas1@pollo-fiesta.com

# Seguridad
JWT_SECRET=tu_clave_secreta_aqui
```

### 4. Configurar Permisos

```bash
# Permisos para carpetas
chmod 755 gestion-sagrilaft/
chmod 755 gestion-sagrilaft/public/
chmod 775 gestion-sagrilaft/storage/

# Permisos para archivos
chmod 644 gestion-sagrilaft/.env
chmod 644 gestion-sagrilaft/.htaccess
chmod 644 gestion-sagrilaft/public/.htaccess
```

### 5. Configurar PHP (cPanel > MultiPHP INI Editor)

```ini
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 300
memory_limit = 512M
display_errors = Off
log_errors = On
```

---

## 👤 Usuarios por Defecto

**Oficial de Cumplimiento:**
- Email: `angie.cumplimiento@pollo-fiesta.com`
- Contraseña: `Angie2024!`

**Administrador:**
- Email: `admin@pollo-fiesta.com`
- Contraseña: `Admin2024!`

---

## ✅ Funcionalidades Verificadas

### Registro y Formularios
- ✅ Registro inicial (página principal)
- ✅ Formulario completo con PDF
- ✅ Upload de archivos (hasta 20MB)
- ✅ Generación de PDF
- ✅ Preview de PDF
- ✅ Declaración de origen de fondos
- ✅ Registro de empleados

### Autenticación
- ✅ Login de admin
- ✅ Login de revisor
- ✅ Logout
- ✅ Sesiones seguras

### Dashboard Revisor
- ✅ Ver formularios pendientes
- ✅ Aprobar/rechazar formularios
- ✅ Ver PDFs completos
- ✅ Descargar archivos adjuntos
- ✅ Consolidar PDFs
- ✅ Filtros y búsqueda
- ✅ Firma digital

### Dashboard Admin
- ✅ Gestión de usuarios
- ✅ Ver todos los formularios
- ✅ Gestión de vendedores
- ✅ Evaluación logística
- ✅ Exportar a Excel

### Notificaciones
- ✅ Email de nuevo formulario
- ✅ Email de aprobación
- ✅ Email de rechazo
- ✅ Links de aprobación directa

---

## 🔧 Arquitectura Técnica

### Flujo de Peticiones

```
Usuario → WordPress (.htaccess)
    ↓
    ¿Es /gestion-sagrilaft/?
    ↓ SÍ
    index.php (raíz) → Redirige a public/
    ↓
    public/index.php
    ↓
    ¿Tiene parámetro route?
    ↓ SÍ
    Router procesa la ruta
    ↓
    Controlador ejecuta acción
    ↓
    Vista renderiza respuesta
```

### Sistema de Rutas

La aplicación funciona sin mod_rewrite usando el parámetro `?route=`:

**Formato:**
```
index.php?route=/ruta/deseada
```

**Ejemplos:**
- `index.php?route=/form/create`
- `index.php?route=/reviewer/dashboard`
- `index.php?route=/approval/{token}`

### Estructura MVC

```
app/
├── Controllers/        # Lógica de negocio
│   ├── HomeController.php
│   ├── FormController.php
│   ├── AuthController.php
│   ├── ApprovalController.php
│   └── AdminController.php
├── Models/            # Acceso a datos
│   ├── Form.php
│   ├── User.php
│   └── Vendedor.php
├── Views/             # Presentación
│   ├── home/
│   ├── forms/
│   ├── approval/
│   └── admin/
├── Core/              # Framework
│   ├── App.php
│   ├── Router.php
│   ├── Controller.php
│   └── Database.php
└── Services/          # Servicios
    ├── AuthService.php
    └── EmailService.php
```

---

## 🔐 Seguridad

### Implementado
- ✅ CSRF tokens en formularios
- ✅ Validación de sesiones
- ✅ Sanitización de inputs
- ✅ Headers de seguridad
- ✅ Archivo .env protegido
- ✅ ModSecurity configurado
- ✅ SQL injection prevention

### Recomendaciones Producción
- ⚠️ Cambiar JWT_SECRET en .env
- ⚠️ Activar HTTPS redirect
- ⚠️ Configurar backups automáticos
- ⚠️ Revisar logs regularmente

---

## 🛠️ Solución de Problemas

### Error 500 - Internal Server Error
```bash
# Verificar permisos
chmod 644 .htaccess
chmod 644 public/.htaccess

# Revisar logs
tail -f storage/logs/error.log
```

### Error 404 - Not Found
- Verificar que la URL incluya `index.php?route=`
- Confirmar que el archivo `public/index.php` existe

### Archivos no suben
```bash
# Verificar permisos de storage
chmod -R 775 storage/uploads/

# Verificar configuración PHP
upload_max_filesize = 20M
post_max_size = 25M
```

### Base de datos no conecta
1. Verificar credenciales en `.env`
2. Confirmar que la base de datos existe
3. Verificar privilegios del usuario

### Emails no se envían
1. Verificar credenciales SMTP en `.env`
2. Probar conexión al servidor SMTP
3. Revisar logs: `storage/logs/error.log`

---

## 📊 Base de Datos

### Tablas Principales

```sql
users                  -- Usuarios del sistema
forms                  -- Formularios principales
form_attachments       -- Archivos adjuntos
form_empleados         -- Empleados registrados
vendedores            -- Asesores comerciales
password_resets       -- Recuperación de contraseña
consolidated_pdfs     -- PDFs consolidados
firmas_digitales      -- Firmas de revisores
```

### Backup

```bash
# Desde cPanel > phpMyAdmin > Export
# O desde SSH:
mysqldump -u usuario -p wwpoll_gestion_sagrilaft > backup.sql
```

---

## 🎨 Assets y Recursos

### Ubicación
```
public/assets/
├── css/
│   └── style.css
├── js/
│   └── app.js
└── img/
    └── orb-logo.png
```

### Carga de Assets
Los assets se cargan con rutas absolutas:
```html
<link href="/gestion-sagrilaft/public/assets/css/style.css" rel="stylesheet">
<script src="/gestion-sagrilaft/public/assets/js/app.js"></script>
```

---

## 📧 Sistema de Notificaciones

### Configuración SMTP (Office365)
```env
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=innovacion@pollo-fiesta.com
MAIL_PASS=Sistemas2026*
```

### Tipos de Emails
1. **Nuevo formulario:** Notifica a revisores
2. **Aprobación:** Confirma al usuario
3. **Rechazo:** Solicita correcciones
4. **Corrección enviada:** Notifica nueva versión

### Links en Emails
Todos los links usan el formato:
```
https://pollo-fiesta.com/gestion-sagrilaft/public/index.php?route=/approval/{token}
```

---

## 🚀 Mejoras Futuras (Opcional)

### URLs Limpias con Subdominio
Crear subdominio en cPanel:
```
Subdominio: sagrilaft.pollo-fiesta.com
Document Root: /home/usuario/public_html/gestion-sagrilaft/public
```

Esto permitiría URLs como:
```
https://sagrilaft.pollo-fiesta.com/form/create
https://sagrilaft.pollo-fiesta.com/reviewer/dashboard
```

### Optimizaciones
- Implementar caché de vistas
- Comprimir assets (CSS/JS)
- Optimizar consultas SQL
- Implementar CDN para assets

---

## 📞 Soporte y Contacto

**Email:** pasantesistemas1@pollo-fiesta.com

**Logs:**
- Aplicación: `storage/logs/error.log`
- cPanel: Errors (últimos errores del servidor)

---

## 📝 Changelog

### v1.0 - 2026-04-13
- ✅ Configuración inicial en cPanel
- ✅ Sistema de rutas sin mod_rewrite
- ✅ Todas las URLs convertidas a formato `index.php?route=`
- ✅ Integración con WordPress
- ✅ Sistema de emails funcional
- ✅ Dashboards de revisor y admin
- ✅ Generación de PDFs
- ✅ Firma digital
- ✅ Exportación a Excel

---

## 🎉 Conclusión

La aplicación está **100% funcional** y lista para producción. Todas las funcionalidades principales han sido probadas y verificadas.

**Estado:** ✅ PRODUCCIÓN
**Versión:** 1.0
**Fecha:** 2026-04-13

---

*Documentación generada para Pollo Fiesta S.A.*
