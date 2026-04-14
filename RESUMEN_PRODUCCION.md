# SAGRILAFT - Resumen de Producción

## URLs del Sistema

### Para Usuarios
- **Página principal**: `https://pollo-fiesta.com/gestion-sagrilaft/public/index.php`
- **Crear formulario**: `https://pollo-fiesta.com/gestion-sagrilaft/public/index.php?route=/form/create`

### Para Revisores
- **Login revisor**: `https://pollo-fiesta.com/gestion-sagrilaft/public/reviewer-login.php`
- **Dashboard**: Después del login, se redirige automáticamente

### Para Aprobación por Email
- **Aprobar formulario**: `https://pollo-fiesta.com/gestion-sagrilaft/public/index.php?route=/approval/{TOKEN}`
  (El token se envía automáticamente por email)

## Archivos Clave

### Configuración
- `.env` - Configuración de base de datos, email, etc.
- `public/index.php` - Punto de entrada principal
- `public/reviewer-login.php` - Acceso directo para revisores

### Base de Datos
- `database/INSTALACION_COMPLETA.sql` - Script completo de instalación

### Documentación
- `DOCUMENTACION.md` - Documentación técnica completa
- `README.md` - Guía de inicio rápido

## Estructura del Proyecto

```
gestion-sagrilaft/
├── app/                    # Código de la aplicación
│   ├── Controllers/        # Controladores
│   ├── Models/            # Modelos de datos
│   ├── Views/             # Vistas HTML
│   ├── Core/              # Sistema core (Router, Database, etc.)
│   ├── Helpers/           # Funciones auxiliares
│   └── Services/          # Servicios (PDF, Email, etc.)
├── config/                # Archivos de configuración
├── database/              # Scripts SQL
├── public/                # Carpeta pública (acceso web)
│   ├── assets/           # CSS, JS, imágenes
│   ├── uploads/          # Archivos subidos por usuarios
│   ├── index.php         # Punto de entrada principal
│   └── reviewer-login.php # Acceso directo revisores
├── storage/               # Almacenamiento
│   ├── logs/             # Logs del sistema
│   ├── pdf/              # PDFs generados
│   ├── templates/        # Plantillas PDF (7 PDFs + 1 Excel)
│   └── uploads/          # Uploads temporales
├── vendor/                # Dependencias (si usas Composer)
├── .env                   # Configuración de entorno
├── composer.json          # Dependencias PHP
├── DOCUMENTACION.md       # Documentación completa
└── README.md              # Guía rápida
```

## Notas Importantes

1. **Sin .htaccess**: El proyecto NO usa archivos `.htaccess` porque causan error 500 en el servidor
2. **URLs con index.php**: Todas las URLs deben incluir `index.php` explícitamente
3. **URLs absolutas**: El código usa URLs absolutas configuradas en `.env` (APP_URL)
4. **WordPress**: El proyecto convive con WordPress sin modificar su configuración

## Credenciales de Prueba

### Revisor
- Usuario: Juan
- Contraseña: (configurada en la base de datos)

## Mantenimiento

### Logs
Los logs se guardan en `storage/logs/` con formato: `app-YYYY-MM-DD.log`

### Backups
Hacer backup regular de:
- Base de datos
- Carpeta `storage/uploads/`
- Carpeta `public/uploads/`
- Archivo `.env`

### Actualizaciones
1. Hacer backup completo
2. Subir nuevos archivos
3. Ejecutar migraciones SQL si es necesario
4. Verificar logs para errores

## Soporte

Para problemas técnicos, revisar:
1. Logs en `storage/logs/`
2. Configuración en `.env`
3. Permisos de carpetas (755 para carpetas, 644 para archivos)
4. Conexión a base de datos

## Estado del Proyecto

✅ Configuración de producción completa
✅ Base de datos configurada
✅ Sistema de emails configurado
✅ PDFs y plantillas incluidas
✅ URLs absolutas configuradas
✅ Sin archivos de prueba
✅ Documentación completa
✅ Listo para producción
