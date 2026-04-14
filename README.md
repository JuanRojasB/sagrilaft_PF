# 🏢 Sistema SAGRILAFT - Pollo Fiesta

Sistema de gestión de formularios SAGRILAFT para cumplimiento normativo.

## 🎯 Estado: FUNCIONAL ✅

**URL Producción:** https://pollo-fiesta.com/gestion-sagrilaft/public/index.php

## 🚀 Instalación Rápida

### 1. Base de Datos
```sql
CREATE DATABASE sagrilaft CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Importar en phpMyAdmin: `database/INSTALACION_COMPLETA.sql`  
(Este archivo incluye TODAS las tablas, datos iniciales y migraciones)

### 2. Configurar .env
```env
DB_HOST=localhost
DB_NAME=sagrilaft
DB_USER=root
DB_PASS=

MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=tu-email@dominio.com
MAIL_PASS=tu_contraseña
```

### 3. Acceder
```
http://localhost/gestion-sagrilaft/public/index.php
```

## 👤 Usuarios por Defecto

**Oficial de Cumplimiento:**
- Email: `angie.cumplimiento@pollo-fiesta.com`
- Contraseña: `Angie2024!`

**Administrador:**
- Email: `admin@pollo-fiesta.com`
- Contraseña: `Admin2024!`

## 📚 Documentación

Ver **DOCUMENTACION.md** para:
- Instalación en cPanel
- Configuración completa
- Solución de problemas
- Arquitectura técnica
- Seguridad

## ⚙️ Características

- ✅ Registro público sin login
- ✅ Formularios dinámicos por tipo de usuario
- ✅ Dashboard de aprobación con filtros
- ✅ Sistema de correcciones
- ✅ Generación automática de PDFs
- ✅ Firma digital
- ✅ Notificaciones por email
- ✅ Exportación a Excel

## 🛠️ Stack Tecnológico

- PHP 8.0+ | MySQL 8.0+
- JavaScript (Vanilla) | Chart.js
- FPDF/FPDI

## 📞 Soporte

**Email:** pasantesistemas1@pollo-fiesta.com  
**Logs:** `storage/logs/error.log`

---

**Versión:** 1.0 | **Última actualización:** Abril 2026
