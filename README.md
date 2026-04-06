# 🏢 Sistema SAGRILAFT - Pollo Fiesta

Sistema de gestión de formularios SAGRILAFT para cumplimiento normativo.

## 🚀 Instalación Rápida

1. **Copiar proyecto a htdocs**
   ```
   C:\xampp\htdocs\gestion-sagrilaft\
   ```

2. **Crear base de datos**
   ```sql
   CREATE DATABASE sagrilaft CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Ejecutar migración única en phpMyAdmin**
   - Abrir phpMyAdmin
   - Seleccionar base de datos `sagrilaft`
   - Ir a pestaña "SQL"
   - Abrir archivo `database/INSTALACION_COMPLETA.sql`
   - Copiar TODO el contenido
   - Pegar y ejecutar

4. **Configurar .env**
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
   ```

5. **Acceder**
   ```
   http://localhost/gestion-sagrilaft/public/
   ```

## 👤 Usuarios por Defecto

**Oficial de Cumplimiento:**
- Email: `angie.cumplimiento@pollo-fiesta.com`
- Contraseña: `Angie2024!`

**Administrador:**
- Email: `admin@pollo-fiesta.com`
- Contraseña: `Admin2024!`

## 📚 Documentación Completa

Ver `DOCUMENTACION_COMPLETA.md` para:
- Guía de instalación detallada
- Configuración de asesores comerciales
- Sistema de correcciones
- Generación de PDFs
- URLs de prueba

## 🗂️ Estructura del Proyecto

```
gestion-sagrilaft/
├── app/                 # Código de la aplicación
│   ├── Controllers/     # Controladores
│   ├── Models/          # Modelos
│   ├── Views/           # Vistas
│   ├── Services/        # Servicios
│   └── Core/            # Núcleo del sistema
├── database/
│   └── INSTALACION_COMPLETA.sql  # UN SOLO ARCHIVO SQL
├── public/              # Punto de entrada
├── storage/             # Logs
├── .env                 # Configuración
├── README.md            # Este archivo
└── DOCUMENTACION_COMPLETA.md
```

## ⚙️ Características

- ✅ Registro sin login para clientes/proveedores
- ✅ Formularios dinámicos según tipo de usuario
- ✅ Dashboard de aprobación con filtros y gráficas
- ✅ Sistema de correcciones con observaciones
- ✅ Generación automática de PDFs
- ✅ Firma digital de documentos
- ✅ Notificaciones por correo
- ✅ Asignación de asesores comerciales
- ✅ Almacenamiento en base de datos (100% portable)

## 🛠️ Tecnologías

- PHP 8.0+
- MySQL 8.0+
- JavaScript (Vanilla)
- Chart.js
- FPDF/FPDI

## 📞 Soporte

Revisar logs en: `storage/logs/app.log`

---

**Versión:** 2.0 | **Última actualización:** Febrero 2026
