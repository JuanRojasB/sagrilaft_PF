# 📚 Documentación Completa - Sistema SAGRILAFT

## 📋 Índice
1. [Descripción General](#descripción-general)
2. [Instalación](#instalación)
3. [Configuración](#configuración)
4. [Características Principales](#características-principales)
5. [Asesores Comerciales](#asesores-comerciales)
6. [Sistema de Correcciones](#sistema-de-correcciones)
7. [Generación de PDFs](#generación-de-pdfs)
8. [URLs de Prueba](#urls-de-prueba)

---

## 📖 Descripción General

Sistema de gestión de formularios SAGRILAFT para Pollo Fiesta. Permite a clientes, proveedores y transportistas registrarse y completar formularios de cumplimiento que son revisados por el Oficial de Cumplimiento.

### Tecnologías
- PHP 8.0+
- MySQL 8.0+
- JavaScript (Vanilla)
- Chart.js para gráficas
- FPDF/FPDI para generación de PDFs

---

## 🚀 Instalación

### Requisitos Previos
- XAMPP/WAMP/LAMP con PHP 8.0+
- MySQL 8.0+
- Extensiones PHP: PDO, GD, mbstring, zip

### Pasos de Instalación

1. **Clonar/Copiar el proyecto**
   ```bash
   # Copiar a la carpeta htdocs de XAMPP
   C:\xampp\htdocs\gestion-sagrilaft\
   ```

2. **Crear base de datos**
   ```sql
   CREATE DATABASE sagrilaft CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Ejecutar migración principal**
   - Abrir phpMyAdmin
   - Seleccionar base de datos `sagrilaft`
   - Ir a pestaña "SQL"
   - Ejecutar: `database/schema_complete_sagrilaft.sql`

4. **Ejecutar migraciones adicionales**
   ```sql
   -- Actividades económicas
   source database/migration_actividades_economicas_completo.sql;
   
   -- Asesores comerciales
   source database/migration_asesores_comerciales.sql;
   ```

5. **Configurar .env**
   ```env
   DB_HOST=localhost
   DB_NAME=sagrilaft
   DB_USER=root
   DB_PASS=
   
   MAIL_HOST=smtp.office365.com
   MAIL_PORT=587
   MAIL_USER=auxiliarsistemas@pollo-fiesta.com
   MAIL_PASS=tu_contrasena
   MAIL_FROM=auxiliarsistemas@pollo-fiesta.com
   MAIL_FROM_NAME=SAGRILAFT - Pollo Fiesta
   ```

6. **Acceder al sistema**
   ```
   http://localhost/gestion-sagrilaft/public/
   ```

---

## ⚙️ Configuración

### Usuarios por Defecto

**Oficial de Cumplimiento (Angie):**
- Email: `angie.cumplimiento@pollo-fiesta.com`
- Contraseña: `Angie2024!`
- Rol: `revisor`

**Administrador:**
- Email: `admin@pollo-fiesta.com`
- Contraseña: `Admin2024!`
- Rol: `admin`

### Configuración de Correo (Outlook/Office 365)

```env
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=auxiliarsistemas@pollo-fiesta.com
MAIL_PASS=contraseña_de_la_cuenta
MAIL_FROM=auxiliarsistemas@pollo-fiesta.com
MAIL_FROM_NAME=SAGRILAFT - Pollo Fiesta
```

**Nota:** Si la cuenta tiene autenticación multifactor (MFA), genera una contraseña de aplicación en https://account.microsoft.com/security

---

## 🎯 Características Principales

### 1. Registro Sin Login
- Clientes, proveedores y transportistas pueden registrarse sin crear cuenta
- Formulario inicial captura datos básicos
- Sistema genera usuario automáticamente

### 2. Formularios Dinámicos
- **Cliente Natural:** FGF-08
- **Cliente Jurídica:** FGF-16
- **Proveedor Natural:** FCO-05
- **Proveedor Jurídica:** FCO-02
- **Proveedor Internacional:** FCO-04
- **Declaración de Fondos:** FGF-17 / FCO-03

### 3. Dashboard de Aprobación
- Filtros por estado, rol, búsqueda
- Ordenamiento por fecha, ID, estado
- Gráficas de estadísticas
- Paginación configurable (25, 50, 100 resultados)

### 4. Sistema de Aprobación
- Aprobar sin observaciones
- Aprobar con observaciones (requiere corrección)
- Rechazar
- Notificaciones por correo automáticas

---

## 👥 Asesores Comerciales

### Configuración

El sistema permite asignar un asesor comercial a cada formulario. Cuando el Oficial de Cumplimiento aprueba o rechaza un formulario, se envían notificaciones al asesor y su jefe.

### Asesores Registrados

**Total:** 30 asesores comerciales

**Distribuidora Fiesta Toberin (D05):** 3 asesores
- Jefe: RODRIGUEZ ESDGAR GERMAN (gerenciacomercial1@pollo-fiesta.com)

**UND Funcional Asadero - Empresarial (U01):** 17 asesores
- Jefe: RODRIGUEZ ESDGAR GERMAN (gerenciacomercial1@pollo-fiesta.com)

**UND Funcional Moderno S3 (U03):** 10 asesores
- Jefe: BENITO GUEVARA HERNAN MATEO (gerenciacomercial3@pollo-fiesta.com)

### Modo Prueba

**Estado Actual:** Solo se envían correos a `juan.david.rojas.burbano0@gmail.com`

**Para Activar Producción:**
1. Abrir `app/Controllers/ApprovalController.php`
2. Buscar línea ~560: "Lista de destinatarios"
3. Comentar sección de prueba
4. Descomentar sección de producción

### Flujo de Notificaciones (Producción)

Cuando se aprueba/rechaza un formulario:
1. ✉️ Creador del formulario
2. ✉️ Asesor comercial asignado
3. ✉️ Jefe del asesor comercial

---

## 🔄 Sistema de Correcciones

### Flujo de Correcciones

1. **Oficial aprueba con observaciones**
   - Estado: `approved_pending`
   - Usuario recibe correo con observaciones
   - Formulario queda pendiente de corrección

2. **Usuario corrige el formulario**
   - Crea nuevo formulario con correcciones
   - Selecciona formulario anterior que está corrigiendo
   - Sistema vincula ambos formularios

3. **Oficial revisa corrección**
   - Ve formulario nuevo con referencia al anterior
   - Puede aprobar completamente
   - Al aprobar, marca el anterior como "corregido"

### Estados de Formularios

- `pending`: Pendiente de revisión
- `approved`: Aprobado sin observaciones
- `approved_pending`: Aprobado con observaciones (requiere corrección)
- `corrected`: Formulario anterior que fue corregido
- `rejected`: Rechazado

---

## 📄 Generación de PDFs

### Sistema de PDFs

El sistema genera PDFs automáticamente cuando se crea un formulario:

1. **PDF del Formulario**
   - Generado desde plantilla oficial
   - Incluye todos los datos del formulario
   - Almacenado en base de datos como BLOB

2. **Documentos Adjuntos**
   - RUT, Cédula, Cámara de Comercio, etc.
   - Almacenados en base de datos como BLOB
   - Máximo 10MB por archivo

3. **PDF Consolidado**
   - Combina formulario + documentos adjuntos
   - Generado al aprobar el formulario
   - Firmado digitalmente por el revisor

### Firma Digital

Los revisores pueden subir su firma digital:
1. Ir a perfil de revisor
2. Subir imagen de firma (PNG/JPG)
3. Firma se aplica automáticamente a PDFs aprobados

---

## 🔗 URLs de Prueba

### Acceso Público
```
http://localhost/gestion-sagrilaft/public/
```

### Dashboard de Aprobación (Angie)
```
http://localhost/gestion-sagrilaft/public/reviewer/login
Email: angie.cumplimiento@pollo-fiesta.com
Contraseña: Angie2024!
```

### Panel Administrativo
```
http://localhost/gestion-sagrilaft/public/admin/login
Email: admin@pollo-fiesta.com
Contraseña: Admin2024!
```

### Aprobar Formulario Directamente
```
http://localhost/gestion-sagrilaft/public/approval/{token}
```
(El token se envía por correo al revisor)

---

## 📊 Base de Datos

### Tablas Principales

- `users`: Usuarios del sistema
- `forms`: Formularios SAGRILAFT
- `form_attachments`: Documentos adjuntos (BLOB)
- `form_consolidated_pdfs`: PDFs consolidados y firmados (BLOB)
- `asesores_comerciales`: Asesores comerciales
- `actividades_economicas`: Códigos CIIU
- `firmas_digitales`: Firmas digitales de revisores

### Configuración MySQL

Para archivos grandes (PDFs), configurar en `my.ini`:
```ini
max_allowed_packet=64M
```

---

## 🛠️ Mantenimiento

### Logs

Los logs se guardan en:
```
storage/logs/app.log
```

### Backup de Base de Datos

```bash
mysqldump -u root -p sagrilaft > backup_sagrilaft_$(date +%Y%m%d).sql
```

### Limpiar Archivos Temporales

Los archivos temporales se limpian automáticamente después de enviar correos.

---

## 📞 Soporte

Para problemas o dudas:
1. Revisar logs en `storage/logs/app.log`
2. Verificar configuración en `.env`
3. Verificar que las migraciones se ejecutaron correctamente

---

## 📝 Notas Importantes

1. **Seguridad:**
   - Cambiar contraseñas por defecto en producción
   - Configurar HTTPS en producción
   - Restringir acceso a archivos sensibles

2. **Performance:**
   - Los PDFs se almacenan en base de datos (BLOB)
   - Sistema 100% portable, no depende de archivos locales
   - Configurar `max_allowed_packet` para archivos grandes

3. **Correos:**
   - Verificar configuración SMTP en `.env`
   - Probar envío de correos antes de producción
   - Modo prueba activo por defecto para asesores comerciales

---

**Última actualización:** Febrero 2026
**Versión:** 2.0
