# DOCUMENTACIÓN TÉCNICA COMPLETA
# SISTEMA SAGRILAFT - GESTIÓN DE FORMULARIOS DE CUMPLIMIENTO

---

**Empresa:** Pollo Fiesta  
**Sistema:** SAGRILAFT (Sistema de Administración del Riesgo de Lavado de Activos y Financiación del Terrorismo)  
**Versión:** 1.0  
**Fecha:** Abril 2026  
**Autor:** Equipo de Desarrollo - Innovación Pollo Fiesta

---

## TABLA DE CONTENIDO

1. [INTRODUCCIÓN](#1-introducción)
2. [ARQUITECTURA DEL SISTEMA](#2-arquitectura-del-sistema)
3. [TECNOLOGÍAS UTILIZADAS](#3-tecnologías-utilizadas)
4. [ESTRUCTURA DEL PROYECTO](#4-estructura-del-proyecto)
5. [BASE DE DATOS](#5-base-de-datos)
6. [MÓDULOS Y FUNCIONALIDADES](#6-módulos-y-funcionalidades)
7. [FLUJOS DE TRABAJO](#7-flujos-de-trabajo)
8. [SISTEMA DE RUTAS](#8-sistema-de-rutas)
9. [SERVICIOS Y LIBRERÍAS](#9-servicios-y-librerías)
10. [CONFIGURACIÓN Y DESPLIEGUE](#10-configuración-y-despliegue)
11. [SEGURIDAD](#11-seguridad)
12. [MANTENIMIENTO Y SOPORTE](#12-mantenimiento-y-soporte)

---

## 1. INTRODUCCIÓN

### 1.1 Propósito del Sistema

El Sistema SAGRILAFT es una aplicación web diseñada para gestionar el proceso de debida diligencia y cumplimiento normativo
de clientes, proveedores y transportistas de Pollo Fiesta. Permite:

- Registro y validación de información de terceros
- Evaluación de riesgos de lavado de activos y financiación del terrorismo
- Generación automática de documentos PDF
- Flujo de aprobación por oficiales de cumplimiento
- Consulta en listas restrictivas (OFAC, ONU, Interpol, listas nacionales)
- Exportación de datos a Excel para análisis
- Gestión de firmas digitales y consolidación de documentos

### 1.2 Alcance

El sistema cubre todo el ciclo de vida del proceso SAGRILAFT:
- Captura de información de terceros
- Validación y verificación de datos
- Aprobación/rechazo por revisores
- Almacenamiento seguro de documentos
- Generación de reportes y auditoría

### 1.3 Usuarios del Sistema

- **Clientes/Proveedores/Transportistas**: Completan formularios de registro
- **Revisores/Oficiales de Cumplimiento**: Aprueban o rechazan formularios
- **Administradores**: Gestionan usuarios, vendedores y configuraciones
- **Sistema**: Envía notificaciones automáticas por email

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Patrón de Diseño

El sistema implementa el patrón **MVC (Model-View-Controller)** personalizado con las siguientes características:

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENTE (Navegador)                     │
└──────────────────────────┬──────────────────────────────────┘
                           │ HTTP Request
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    public/index.php                          │
│  - Carga .env                                                │
│  - Inicia sesión                                             │
│  - Ejecuta App\Core\App                                      │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    App\Core\Router                           │
│  - Registra rutas                                            │
│  - Ejecuta middlewares (auth, role)                          │
│  - Despacha a controlador                                    │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLADORES                             │
│  - AuthController                                            │
│  - FormController                                            │
│  - ApprovalController                                        │
│  - AdminController                                           │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    MODELOS                                   │
│  - User, Form, Reviewer                                      │
│  - Interacción con BD (PDO)                                  │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    SERVICIOS                                 │
│  - PdfService, MailService                                   │
│  - AuthService, Logger                                       │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    VISTAS (PHP Templates)                    │
│  - Renderiza HTML                                            │
│  - Incluye assets (CSS, JS)                                  │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Componentes Principales

#### 2.2.1 Core (Núcleo)
- **App.php**: Clase principal que inicializa el router y registra rutas
- **Router.php**: Maneja enrutamiento, middlewares y despacho de peticiones
- **Database.php**: Conexión Singleton a MySQL con PDO
- **Controller.php**: Clase base para todos los controladores

#### 2.2.2 Middlewares
- **AuthMiddleware**: Verifica autenticación de usuarios
- **RoleMiddleware**: Controla acceso por roles (admin, revisor, cliente)

#### 2.2.3 Servicios
- **AuthService**: Autenticación y gestión de sesiones
- **PdfService**: Generación y manipulación de PDFs
- **MailService**: Envío de emails con SMTP
- **Logger**: Sistema de logs para auditoría
- **FormPdfFiller**: Rellena plantillas PDF con datos de formularios

### 2.3 Flujo de una Petición

1. Usuario accede a URL (ej: `/forms/123`)
2. `.htaccess` redirige a `public/index.php`
3. `index.php` carga `.env` e inicia `App\Core\App`
4. `Router` busca ruta que coincida con `/forms/{id}`
5. Ejecuta middlewares (`auth`, `role`)
6. Despacha a `FormController@show` con parámetro `id=123`
7. Controlador consulta modelo `Form::findById(123)`
8. Renderiza vista `forms/show.php` con datos
9. Retorna HTML al navegador

---

## 3. TECNOLOGÍAS UTILIZADAS

### 3.1 Backend

| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| PHP | 8.2+ | Lenguaje principal |
| MySQL | 10.4+ | Base de datos |
| PDO | - | Abstracción de BD |
| Apache | 2.4+ | Servidor web |
| mod_rewrite | - | Reescritura de URLs |

### 3.2 Librerías PHP

| Librería | Versión | Propósito |
|----------|---------|-----------|
| FPDF | 1.86 | Generación básica de PDFs |
| FPDI | 2.6.0 | Importación y manipulación de PDFs |
| TCPDF | 6.7.5 | Generación avanzada de PDFs |
| PHPMailer | Nativo | Envío de emails SMTP |

### 3.3 Frontend

| Tecnología | Propósito |
|------------|-----------|
| HTML5 | Estructura |
| CSS3 | Estilos |
| JavaScript | Interactividad |
| Bootstrap 5 | Framework CSS |
| jQuery | Manipulación DOM |

### 3.4 Herramientas de Desarrollo

- Git para control de versiones
- Composer (opcional, no usado actualmente)
- phpMyAdmin para gestión de BD

---

## 4. ESTRUCTURA DEL PROYECTO

### 4.1 Árbol de Directorios

```
gestion-sagrilaft/
├── app/
│   ├── Controllers/          # Controladores MVC
│   │   ├── AdminController.php
│   │   ├── ApprovalController.php
│   │   ├── AuthController.php
│   │   ├── ExcelSyncController.php
│   │   ├── FormController.php
│   │   └── HomeController.php
│   ├── Core/                 # Núcleo del framework
│   │   ├── App.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   └── Router.php
│   ├── Helpers/              # Funciones auxiliares
│   │   └── EmailHelper.php
│   ├── Libraries/            # Librerías externas
│   │   ├── fpdf.php
│   │   ├── FPDI-2.6.0/
│   │   └── TCPDF-6.7.5/
│   ├── Middlewares/          # Middlewares de autenticación
│   │   ├── AuthMiddleware.php
│   │   └── RoleMiddleware.php
│   ├── Models/               # Modelos de datos
│   │   ├── User.php
│   │   ├── Form.php
│   │   ├── Reviewer.php
│   │   ├── FormAttachment.php
│   │   ├── Vendedor.php
│   │   └── ...
│   ├── Services/             # Servicios de negocio
│   │   ├── AuthService.php
│   │   ├── PdfService.php
│   │   ├── MailService.php
│   │   ├── Logger.php
│   │   ├── FormPdfFiller.php
│   │   └── ...
│   └── Views/                # Vistas (templates PHP)
│       ├── admin/
│       ├── approval/
│       ├── auth/
│       ├── forms/
│       ├── home/
│       └── layouts/
├── database/                 # Scripts SQL
│   ├── sagrilaft.sql
│   └── INSTALACION_COMPLETA.sql
├── public/                   # Directorio público (DocumentRoot)
│   ├── index.php             # Punto de entrada
│   ├── .htaccess             # Configuración Apache
│   ├── assets/               # Recursos estáticos
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── uploads/              # Archivos subidos (no usado, se usa BD)
├── .env                      # Configuración de entorno
├── .gitignore
└── .htaccess                 # Redirige a /public
```

### 4.2 Descripción de Carpetas Clave

#### app/Controllers/
Contiene los controladores que manejan la lógica de negocio:
- **AuthController**: Login, logout, recuperación de contraseña
- **FormController**: CRUD de formularios, generación de PDFs
- **ApprovalController**: Aprobación/rechazo por revisores
- **AdminController**: Gestión de usuarios, vendedores, logística
- **ExcelSyncController**: Exportación de datos a Excel
- **HomeController**: Página principal y registro simplificado

#### app/Models/
Modelos que representan entidades de la BD:
- **User**: Usuarios del sistema
- **Form**: Formularios SAGRILAFT
- **Reviewer**: Revisores/oficiales de cumplimiento
- **FormAttachment**: Archivos adjuntos
- **Vendedor**: Contactos/vendedores

#### app/Services/
Servicios especializados:
- **AuthService**: Autenticación JWT y sesiones
- **PdfService**: Generación y consolidación de PDFs
- **MailService**: Envío de emails con plantillas
- **Logger**: Sistema de logs
- **FormPdfFiller**: Rellena plantillas PDF con datos

#### app/Views/
Templates PHP organizados por módulo:
- **layouts/**: Plantillas base (header, footer)
- **forms/**: Vistas de formularios
- **approval/**: Dashboard de revisores
- **admin/**: Panel de administración

---


## 5. BASE DE DATOS

### 5.1 Diagrama Entidad-Relación

```
┌─────────────────┐         ┌─────────────────┐
│     users       │         │   reviewers     │
├─────────────────┤         ├─────────────────┤
│ id (PK)         │         │ id (PK)         │
│ name            │         │ name            │
│ email           │         │ email           │
│ password        │         │ password        │
│ role            │         │ created_at      │
│ document_type   │         └─────────────────┘
│ document_number │
│ person_type     │
│ phone           │
│ created_at      │
└────────┬────────┘
         │
         │ 1:N
         │
         ▼
┌─────────────────────────────────────────────────────────┐
│                      forms                              │
├─────────────────────────────────────────────────────────┤
│ id (PK)                                                 │
│ user_id (FK → users.id)                                 │
│ form_type (cliente_natural, cliente_juridica, etc.)    │
│ title                                                   │
│ company_name, nit, address, phone, email                │
│ activos, pasivos, patrimonio, ingresos, gastos         │
│ es_pep, cargo_pep, origen_fondos                        │
│ approval_status (pending, approved, rejected)           │
│ approval_token                                          │
│ approved_by (FK → reviewers.id)                         │
│ approved_at                                             │
│ related_form_id (FK → forms.id) [para declaraciones]    │
│ consulta_ofac, consulta_listas_nacionales, etc.        │
│ created_at, updated_at                                  │
└────────┬────────────────────────────────────────────────┘
         │
         │ 1:N
         │
         ▼
┌─────────────────────────────────────────────────────────┐
│              form_attachments                           │
├─────────────────────────────────────────────────────────┤
│ id (PK)                                                 │
│ form_id (FK → forms.id)                                 │
│ filename                                                │
│ file_data (BLOB) [archivo PDF almacenado]              │
│ file_size                                               │
│ uploaded_at                                             │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│           form_consolidated_pdfs                        │
├─────────────────────────────────────────────────────────┤
│ id (PK)                                                 │
│ form_id (FK → forms.id)                                 │
│ pdf_data (BLOB) [PDF consolidado y firmado]            │
│ signed (BOOLEAN)                                        │
│ created_at                                              │
└─────────────────────────────────────────────────────────┘

┌─────────────────┐         ┌─────────────────────────────┐
│   vendedores    │         │ vendedor_cliente_history    │
├─────────────────┤         ├─────────────────────────────┤
│ id (PK)         │◄────────│ vendedor_id (FK)            │
│ nombre          │         │ cliente_id (FK → users.id)  │
│ email           │         │ assigned_at                 │
│ telefono        │         │ assigned_by (FK → users.id) │
│ cargo           │         └─────────────────────────────┘
│ activo          │
└─────────────────┘

┌─────────────────────────────────────────────────────────┐
│              firmas_digitales                           │
├─────────────────────────────────────────────────────────┤
│ id (PK)                                                 │
│ user_id (FK → users.id)                                 │
│ firma_data (BLOB) [imagen de firma PNG/JPG]            │
│ created_at                                              │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│           password_reset_codes                          │
├─────────────────────────────────────────────────────────┤
│ id (PK)                                                 │
│ email                                                   │
│ code (6 dígitos)                                        │
│ expires_at                                              │
│ used (BOOLEAN)                                          │
│ created_at                                              │
└─────────────────────────────────────────────────────────┘
```

### 5.2 Descripción de Tablas Principales

#### 5.2.1 Tabla: users
Almacena todos los usuarios del sistema (clientes, proveedores, transportistas, admin, revisor).

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Identificador único |
| name | VARCHAR(255) | Nombre completo |
| email | VARCHAR(255) UNIQUE | Email (usado para login) |
| password | VARCHAR(255) | Hash bcrypt de contraseña |
| role | ENUM | cliente, proveedor, transportista, admin, revisor |
| document_type | VARCHAR(50) | cedula, nit, pasaporte, etc. |
| document_number | VARCHAR(50) | Número de documento |
| person_type | ENUM | natural, juridica |
| phone | VARCHAR(20) | Teléfono de contacto |
| address | TEXT | Dirección |
| city | VARCHAR(100) | Ciudad |
| company_name | VARCHAR(255) | Nombre de empresa (si aplica) |
| logistics_status | ENUM | pending, approved, rejected (para proveedores) |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

#### 5.2.2 Tabla: forms
Almacena todos los formularios SAGRILAFT con información completa.

**Campos de identificación:**
- id, user_id, form_type, title, status

**Campos de datos generales:**
- company_name, nit, address, ciudad, barrio, localidad
- telefono_fijo, celular, phone, empresa_email, fax

**Campos de actividad económica:**
- activity, codigo_ciiu, objeto_social

**Campos financieros:**
- activos, pasivos, patrimonio, ingresos, gastos
- otros_ingresos, detalle_otros_ingresos

**Campos tributarios:**
- tipo_contribuyente, regimen_tributario

**Campos de representante legal:**
- representante_nombre, representante_documento
- representante_tipo_doc, representante_profesion
- representante_nacimiento, representante_telefono

**Campos de accionistas:**
- accionistas (JSON serializado)

**Campos de cumplimiento:**
- es_pep, cargo_pep, familiares_pep
- origen_fondos, tiene_cuentas_exterior
- paises_cuentas_exterior

**Campos de aprobación:**
- approval_status (pending, approved, approved_pending, rejected, corrected)
- approval_token (UUID para enlace de aprobación)
- approved_by, approved_at
- rejection_reason, observations

**Campos internos (completados por revisor):**
- consulta_ofac, consulta_listas_nacionales
- consulta_onu, consulta_interpol
- consulta_listas_internas, consulta_google
- autorizacion_tratamiento_datos
- autorizacion_consulta_centrales

**Campos de cliente:**
- lista_precios, codigo_vendedor, tipo_pago
- cupo_credito, fecha_nacimiento, clase_cliente

**Campos de proveedor:**
- tipo_compania, persona_contacto
- tiene_certificacion, cual_certificacion

**Campos de importación:**
- pais, concepto_importacion
- declaracion_importacion, certificado_origen

**Relaciones:**
- related_form_id (para vincular declaraciones al formulario principal)

#### 5.2.3 Tabla: form_attachments
Almacena archivos PDF adjuntos por los usuarios.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Identificador único |
| form_id | INT | FK a forms.id |
| filename | VARCHAR(255) | Nombre original del archivo |
| file_data | LONGBLOB | Contenido del archivo PDF |
| file_size | INT | Tamaño en bytes |
| uploaded_at | TIMESTAMP | Fecha de carga |

**Nota**: Los archivos se almacenan directamente en la base de datos como BLOB, no en el filesystem.

#### 5.2.4 Tabla: form_consolidated_pdfs
Almacena PDFs consolidados (formulario + declaraciones + adjuntos + firma).

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Identificador único |
| form_id | INT | FK a forms.id |
| pdf_data | LONGBLOB | PDF consolidado completo |
| signed | BOOLEAN | Indica si tiene firma digital |
| created_at | TIMESTAMP | Fecha de generación |

#### 5.2.5 Tabla: reviewers
Almacena revisores/oficiales de cumplimiento.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Identificador único |
| name | VARCHAR(255) | Nombre completo |
| email | VARCHAR(255) UNIQUE | Email (usado para login) |
| password | VARCHAR(255) | Hash bcrypt de contraseña |
| created_at | TIMESTAMP | Fecha de creación |

#### 5.2.6 Tabla: vendedores
Almacena contactos/vendedores para asignar a clientes.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Identificador único |
| nombre | VARCHAR(255) | Nombre completo |
| email | VARCHAR(255) | Email de contacto |
| telefono | VARCHAR(20) | Teléfono |
| cargo | VARCHAR(100) | Cargo/posición |
| activo | BOOLEAN | Estado activo/inactivo |
| created_at | TIMESTAMP | Fecha de creación |

#### 5.2.7 Tabla: vendedor_cliente_history
Historial de asignaciones vendedor-cliente.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Identificador único |
| vendedor_id | INT | FK a vendedores.id |
| cliente_id | INT | FK a users.id |
| assigned_at | TIMESTAMP | Fecha de asignación |
| assigned_by | INT | FK a users.id (admin que asignó) |

#### 5.2.8 Tabla: firmas_digitales
Almacena firmas digitales de usuarios para firmar PDFs.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Identificador único |
| user_id | INT | FK a users.id |
| firma_data | LONGBLOB | Imagen de firma (PNG/JPG) |
| created_at | TIMESTAMP | Fecha de carga |

#### 5.2.9 Tabla: password_reset_codes
Códigos de recuperación de contraseña.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT AUTO_INCREMENT | Identificador único |
| email | VARCHAR(255) | Email del usuario |
| code | VARCHAR(6) | Código de 6 dígitos |
| expires_at | TIMESTAMP | Fecha de expiración (15 min) |
| used | BOOLEAN | Indica si ya fue usado |
| created_at | TIMESTAMP | Fecha de creación |

### 5.3 Índices y Optimizaciones

**Índices principales:**
- `users.email` (UNIQUE)
- `forms.user_id` (INDEX)
- `forms.approval_token` (UNIQUE)
- `forms.approval_status` (INDEX)
- `form_attachments.form_id` (INDEX)
- `form_consolidated_pdfs.form_id` (INDEX)

**Optimizaciones:**
- Uso de ENUM para campos con valores fijos
- Almacenamiento de JSON serializado para arrays (accionistas)
- BLOBs para archivos (evita problemas de filesystem)

---

## 6. MÓDULOS Y FUNCIONALIDADES

### 6.1 Módulo de Autenticación

**Controlador**: `AuthController`  
**Rutas**:
- `GET /admin-access` - Formulario de login
- `POST /login` - Procesar login
- `POST /logout` - Cerrar sesión
- `GET /password/forgot` - Solicitar código de recuperación
- `POST /password/send-code` - Enviar código por email
- `GET /password/reset` - Formulario de restablecimiento
- `POST /password/reset` - Procesar restablecimiento

**Funcionalidades**:
1. **Login de usuarios**:
   - Valida email y contraseña
   - Verifica hash bcrypt
   - Crea sesión PHP
   - Genera token JWT (opcional)
   - Redirige según rol (admin → /admin, revisor → /reviewer/dashboard, cliente → /forms)

2. **Recuperación de contraseña**:
   - Usuario ingresa email
   - Sistema genera código de 6 dígitos
   - Envía código por email
   - Código válido por 15 minutos
   - Usuario ingresa código y nueva contraseña
   - Sistema actualiza contraseña

3. **Logout**:
   - Destruye sesión PHP
   - Redirige a página de login

**Seguridad**:
- Tokens CSRF en todos los formularios
- Bcrypt para hash de contraseñas (cost 12)
- Límite de intentos de login (implementable)
- Códigos de recuperación de un solo uso

### 6.2 Módulo de Formularios

**Controlador**: `FormController`  
**Modelo**: `Form`

**Rutas principales**:
- `GET /form/create` - Formulario de creación (sin autenticación)
- `POST /form/store` - Guardar formulario
- `GET /forms` - Listar formularios del usuario
- `GET /forms/{id}` - Ver detalle de formulario
- `GET /forms/{id}/pdf` - Generar PDF
- `GET /forms/{id}/view` - Vista completa con declaraciones
- `POST /forms/{id}/consolidate` - Consolidar PDFs

**Funcionalidades**:

1. **Creación de formularios**:
   - Formulario multi-paso (datos generales → declaración de fondos)
   - Validación de campos obligatorios
   - Carga de archivos PDF adjuntos (hasta 20MB)
   - Generación automática de token de aprobación
   - Envío de email al revisor con enlace de aprobación

2. **Tipos de formularios**:
   - **FGF-08**: Cliente persona natural
   - **FGF-16**: Cliente persona jurídica
   - **FCO-05**: Proveedor persona natural
   - **FCO-06**: Proveedor persona jurídica
   - **FCO-07**: Proveedor internacional
   - **FCO-08**: Transportista
   - **Declaración de origen de fondos**: Vinculada al formulario principal

3. **Generación de PDFs**:
   - Usa plantillas PDF predefinidas
   - Rellena campos con datos del formulario
   - Soporta campos de texto, checkboxes, fechas
   - Deserializa JSON (accionistas, etc.)
   - Aplica firma digital del oficial de cumplimiento

4. **Consolidación de PDFs**:
   - Recopila: formulario principal + declaraciones + adjuntos
   - Ordena: 01_principal, 02_declaracion, 10+_adjuntos, 99_hoja_revisor
   - Usa FPDI para importar páginas
   - Aplica firma digital
   - Almacena en `form_consolidated_pdfs`

5. **Descarga de adjuntos**:
   - Usuarios autenticados: `/forms/attachment/{id}`
   - Revisores: `/reviewer/attachment/{id}`
   - Valida permisos antes de servir archivo

### 6.3 Módulo de Aprobación

**Controlador**: `ApprovalController`  
**Modelo**: `Form`, `Reviewer`

**Rutas**:
- `GET /reviewer/login` - Login de revisores
- `POST /reviewer/login` - Procesar login
- `GET /reviewer/dashboard` - Dashboard de revisores
- `GET /approval/{token}` - Ver formulario para aprobar
- `POST /approval/{token}` - Procesar decisión
- `POST /reviewer/logout` - Cerrar sesión

**Funcionalidades**:

1. **Dashboard de revisores**:
   - Lista todos los formularios del sistema
   - Filtros por estado (pending, approved, rejected)
   - Filtros por tipo de formulario
   - Búsqueda por nombre/NIT
   - Acceso a PDFs y adjuntos

2. **Proceso de aprobación**:
   - Revisor accede con token único (desde email)
   - Visualiza datos completos del formulario
   - Descarga adjuntos del usuario
   - Completa campos internos:
     - Consulta OFAC
     - Consulta listas nacionales
     - Consulta ONU
     - Consulta Interpol
     - Consulta listas internas
     - Consulta Google
   - Decide: Aprobar / Rechazar / Solicitar correcciones
   - Ingresa observaciones

3. **Post-aprobación**:
   - Sistema consolida PDFs automáticamente
   - Aplica firma digital
   - Envía email de notificación al usuario
   - Actualiza estado del formulario

4. **Estados de aprobación**:
   - `pending`: Pendiente de revisión
   - `approved`: Aprobado
   - `approved_pending`: Aprobado con observaciones
   - `rejected`: Rechazado
   - `corrected`: Corregido por usuario (vuelve a pending)

### 6.4 Módulo de Administración

**Controlador**: `AdminController`  
**Rutas**:
- `GET /admin` - Dashboard de administración
- `GET /admin/users` - Gestión de usuarios
- `POST /admin/users/create` - Crear usuario
- `POST /admin/users/{id}/update` - Actualizar usuario
- `POST /admin/users/{id}/delete` - Eliminar usuario
- `GET /admin/logistics` - Evaluación logística
- `POST /admin/logistics/evaluate` - Evaluar proveedor
- `GET /admin/vendedores` - Gestión de vendedores
- `POST /admin/vendedores/create` - Crear vendedor
- `POST /admin/vendedores/asignar` - Asignar vendedor a cliente

**Funcionalidades**:

1. **Gestión de usuarios**:
   - CRUD completo de usuarios
   - Asignación de roles
   - Cambio de contraseñas
   - Activación/desactivación de cuentas

2. **Evaluación logística de proveedores**:
   - Formulario de evaluación con criterios
   - Calificación numérica
   - Aprobación/rechazo logístico
   - Historial de evaluaciones

3. **Gestión de vendedores**:
   - CRUD de vendedores/contactos
   - Asignación de vendedores a clientes
   - Historial de asignaciones
   - Reportes de cartera por vendedor

### 6.5 Módulo de Exportación

**Controlador**: `ExcelSyncController`  
**Rutas**:
- `POST /admin/export/excel` - Exportar datos a Excel

**Funcionalidades**:

1. **Exportación a Excel**:
   - Genera archivo Excel en formato XML (MS Excel 2003)
   - Hojas separadas por tipo de formulario:
     - Clientes Naturales
     - Clientes Jurídicos
     - Proveedores Naturales
     - Proveedores Internacionales
     - Transportistas
   - Columnas agrupadas por sección:
     - Datos comunes
     - Datos generales
     - Actividad económica
     - Información financiera
     - Información tributaria
     - Representante legal
     - Accionistas
     - Fondos y PEP
     - Control y aprobación

2. **Filtros de exportación**:
   - Por estado de aprobación
   - Por rol (cliente, proveedor, transportista)
   - Por rango de fechas
   - Por vendedor asignado

3. **Formato del Excel**:
   - Cabecera con fondo oscuro
   - Filas alternadas para legibilidad
   - Estados con colores (verde=aprobado, rojo=rechazado, amarillo=pendiente)
   - Alineación automática de texto
   - Ajuste de ancho de columnas

---


## 7. FLUJOS DE TRABAJO

### 7.1 Flujo de Registro de Cliente/Proveedor

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Usuario accede a página principal (/)                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Completa formulario de registro                          │
│    - Tipo de formulario (cliente/proveedor/transportista)   │
│    - Tipo de persona (natural/jurídica)                     │
│    - Datos generales (nombre, NIT, dirección, etc.)         │
│    - Datos financieros (activos, pasivos, ingresos)         │
│    - Datos de cumplimiento (PEP, origen de fondos)          │
│    - Carga de archivos PDF adjuntos                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Sistema valida datos                                     │
│    - Campos obligatorios completos                          │
│    - Formato de archivos (solo PDF)                         │
│    - Tamaño de archivos (máx 20MB)                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Sistema guarda formulario en BD                          │
│    - Inserta registro en tabla 'forms'                      │
│    - Guarda archivos en 'form_attachments' (BLOB)           │
│    - Genera token único de aprobación (UUID)                │
│    - Estado inicial: 'pending'                              │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Sistema genera PDF del formulario                        │
│    - Usa plantilla PDF según tipo de formulario             │
│    - Rellena campos con datos del usuario                   │
│    - Guarda PDF temporal                                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Sistema envía email al revisor                           │
│    - Destinatario: pasantesistemas1@pollo-fiesta.com        │
│    - Asunto: "Nuevo formulario SAGRILAFT pendiente"         │
│    - Contenido: Resumen de datos + enlace de aprobación     │
│    - Enlace: /approval/{token}                              │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. Usuario ve página de confirmación                        │
│    - Mensaje: "Formulario enviado exitosamente"             │
│    - Instrucciones: "Recibirá notificación por email"       │
│    - Opción: Descargar PDF del formulario                   │
└─────────────────────────────────────────────────────────────┘
```

### 7.2 Flujo de Aprobación por Revisor

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Revisor recibe email con enlace de aprobación            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Revisor hace clic en enlace /approval/{token}            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Sistema valida token                                     │
│    - Busca formulario con approval_token                    │
│    - Verifica que no esté expirado                          │
│    - Verifica que no esté ya procesado                      │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Sistema muestra formulario completo                      │
│    - Datos del usuario                                      │
│    - Datos del formulario                                   │
│    - Archivos adjuntos (descargables)                       │
│    - PDF del formulario (visualizable)                      │
│    - Formularios relacionados (declaraciones)               │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Revisor completa campos internos                         │
│    - Consulta OFAC (Sí/No)                                  │
│    - Consulta listas nacionales (Sí/No)                     │
│    - Consulta ONU (Sí/No)                                   │
│    - Consulta Interpol (Sí/No)                              │
│    - Consulta listas internas (Sí/No)                       │
│    - Consulta Google (Sí/No)                                │
│    - Autorización tratamiento de datos (Sí/No)              │
│    - Autorización consulta centrales (Sí/No)                │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Revisor toma decisión                                    │
│    ┌─────────────┬─────────────┬─────────────┐             │
│    │  Aprobar    │  Rechazar   │  Correcciones│             │
│    └──────┬──────┴──────┬──────┴──────┬───────┘             │
└───────────┼─────────────┼─────────────┼─────────────────────┘
            │             │             │
            ▼             ▼             ▼
┌───────────────┐ ┌───────────────┐ ┌───────────────┐
│ Estado:       │ │ Estado:       │ │ Estado:       │
│ 'approved'    │ │ 'rejected'    │ │ 'approved_    │
│               │ │               │ │  pending'     │
└───────┬───────┘ └───────┬───────┘ └───────┬───────┘
        │                 │                 │
        └─────────────────┼─────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. Sistema actualiza formulario en BD                       │
│    - Actualiza approval_status                              │
│    - Guarda approved_by (ID del revisor)                    │
│    - Guarda approved_at (timestamp)                         │
│    - Guarda observations (si hay)                           │
│    - Guarda rejection_reason (si rechazado)                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. Sistema consolida PDFs (solo si aprobado)                │
│    - Recopila: formulario + declaraciones + adjuntos        │
│    - Ordena: 01_principal, 02_declaracion, 10+_adjuntos     │
│    - Agrega hoja de revisor con datos de aprobación         │
│    - Aplica firma digital del oficial de cumplimiento       │
│    - Guarda en 'form_consolidated_pdfs'                     │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 9. Sistema envía email de notificación al usuario           │
│    - Destinatario: email del usuario                        │
│    - Asunto: "Formulario SAGRILAFT [APROBADO/RECHAZADO]"    │
│    - Contenido: Resultado + observaciones                   │
│    - Adjunto: PDF consolidado (si aprobado)                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 10. Revisor ve página de confirmación                       │
│     - Mensaje: "Formulario procesado exitosamente"          │
│     - Opción: Volver al dashboard                           │
│     - Opción: Descargar PDF consolidado                     │
└─────────────────────────────────────────────────────────────┘
```

### 7.3 Flujo de Declaración de Origen de Fondos

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Usuario completa formulario principal                    │
│    - Formulario FGF-08 o FGF-16 (cliente)                   │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Sistema guarda formulario principal                      │
│    - Genera ID del formulario (ej: 123)                     │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Sistema redirige a /form/declaracion?form_id=123         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Usuario completa declaración de origen de fondos         │
│    - Origen de fondos (salario, negocio, herencia, etc.)    │
│    - Detalle del origen                                     │
│    - Declaración de PEP (Persona Expuesta Políticamente)    │
│    - Cargo PEP (si aplica)                                  │
│    - Familiares PEP (si aplica)                             │
│    - Cuentas en el exterior (Sí/No)                         │
│    - Países de cuentas (si aplica)                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Sistema guarda declaración                               │
│    - Inserta registro en tabla 'forms'                      │
│    - form_type = 'declaracion_origen_fondos'                │
│    - related_form_id = 123 (vincula al formulario principal)│
│    - user_id = mismo que formulario principal               │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Sistema genera PDF de declaración                        │
│    - Usa plantilla de declaración                           │
│    - Rellena con datos de la declaración                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. Al generar PDF consolidado, incluye ambos formularios    │
│    - 01_formulario_principal.pdf                            │
│    - 02_declaracion_fondos.pdf                              │
│    - 10+_adjuntos_usuario.pdf                               │
│    - 99_hoja_revisor.pdf                                    │
└─────────────────────────────────────────────────────────────┘
```

### 7.4 Flujo de Recuperación de Contraseña

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Usuario hace clic en "¿Olvidó su contraseña?"            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Usuario ingresa su email                                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Sistema valida email                                     │
│    - Verifica que exista en tabla 'users'                   │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Sistema genera código de 6 dígitos                       │
│    - Código aleatorio (ej: 123456)                          │
│    - Válido por 15 minutos                                  │
│    - Guarda en tabla 'password_reset_codes'                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Sistema envía email con código                           │
│    - Destinatario: email del usuario                        │
│    - Asunto: "Código de recuperación de contraseña"         │
│    - Contenido: Código de 6 dígitos                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Usuario ingresa código y nueva contraseña                │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. Sistema valida código                                    │
│    - Verifica que exista y no esté expirado                 │
│    - Verifica que no haya sido usado                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. Sistema actualiza contraseña                             │
│    - Hash bcrypt de nueva contraseña                        │
│    - Actualiza tabla 'users'                                │
│    - Marca código como usado                                │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 9. Usuario puede iniciar sesión con nueva contraseña        │
└─────────────────────────────────────────────────────────────┘
```

### 7.5 Flujo de Exportación a Excel

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Admin/Revisor accede a dashboard                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Selecciona formularios a exportar                        │
│    - Aplica filtros (estado, tipo, fechas)                  │
│    - Selecciona todos o específicos                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Hace clic en "Exportar a Excel"                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Sistema genera archivo Excel                             │
│    - Formato XML (MS Excel 2003)                            │
│    - Hojas separadas por tipo de formulario                 │
│    - Aplica estilos y formato                               │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Sistema descarga archivo                                 │
│    - Nombre: SAGRILAFT_Export_YYYY-MM-DD.xls                │
│    - Content-Type: application/vnd.ms-excel                 │
└─────────────────────────────────────────────────────────────┘
```

---

## 8. SISTEMA DE RUTAS

### 8.1 Rutas Públicas (sin autenticación)

| Método | Ruta | Controlador@Método | Descripción |
|--------|------|-------------------|-------------|
| GET | / | HomeController@index | Página principal |
| POST | /home/register | HomeController@register | Registro simplificado |
| GET | /form/create | FormController@createDirect | Formulario de creación |
| POST | /form/store | FormController@storeDirect | Guardar formulario |
| POST | /form/store-pdf | FormController@storePdf | Guardar con PDF |
| POST | /form/pdf-preview | FormController@pdfPreview | Vista previa de PDF |
| GET | /form/success | FormController@success | Página de confirmación |
| GET | /form/declaracion | FormController@showDeclaracion | Declaración de fondos |
| POST | /form/declaracion/store | FormController@storeDeclaracion | Guardar declaración |
| GET | /form/{id}/pdf | FormController@generatePdf | Generar PDF público |
| GET | /approval/{token} | ApprovalController@show | Ver formulario para aprobar |
| POST | /approval/{token} | ApprovalController@process | Procesar aprobación |

### 8.2 Rutas de Autenticación

| Método | Ruta | Controlador@Método | Descripción |
|--------|------|-------------------|-------------|
| GET | /admin-access | AuthController@showLogin | Formulario de login |
| GET | /login | AuthController@showLogin | Formulario de login (alias) |
| POST | /login | AuthController@login | Procesar login |
| POST | /logout | AuthController@logout | Cerrar sesión |
| GET | /password/forgot | PasswordResetController@showForgotForm | Solicitar código |
| POST | /password/send-code | PasswordResetController@sendCode | Enviar código |
| GET | /password/reset | PasswordResetController@showResetForm | Formulario reset |
| POST | /password/reset | PasswordResetController@resetPassword | Procesar reset |

### 8.3 Rutas Autenticadas (middleware: auth)

| Método | Ruta | Controlador@Método | Descripción |
|--------|------|-------------------|-------------|
| GET | /forms | FormController@index | Listar formularios |
| GET | /forms/create | FormController@create | Formulario de creación |
| POST | /forms | FormController@store | Guardar formulario |
| GET | /forms/{id} | FormController@show | Ver detalle |
| GET | /forms/{id}/view | FormController@viewComplete | Vista completa |
| GET | /forms/{id}/pdf | FormController@generatePdf | Generar PDF |
| POST | /forms/{id}/pollo-fiesta | FormController@savePolloFiesta | Guardar campos internos |
| GET | /forms/attachment/{id} | FormController@downloadAttachment | Descargar adjunto |
| GET | /profile | ProfileController@edit | Editar perfil |
| POST | /profile/update | ProfileController@update | Actualizar perfil |

### 8.4 Rutas de Revisor (middleware: auth)

| Método | Ruta | Controlador@Método | Descripción |
|--------|------|-------------------|-------------|
| GET | /reviewer/login | ApprovalController@login | Login de revisores |
| POST | /reviewer/login | ApprovalController@processLogin | Procesar login |
| GET | /reviewer/dashboard | ApprovalController@dashboard | Dashboard de revisores |
| POST | /reviewer/logout | ApprovalController@logout | Cerrar sesión |
| GET | /reviewer/attachment/{id} | FormController@downloadAttachmentReviewer | Descargar adjunto |
| GET | /reviewer/form/{id}/pdf | FormController@generatePdfReviewer | Ver PDF |
| POST | /forms/{id}/consolidate | FormController@consolidatePDFs | Consolidar PDFs |

### 8.5 Rutas de Administrador (middleware: auth, role:admin)

| Método | Ruta | Controlador@Método | Descripción |
|--------|------|-------------------|-------------|
| GET | /admin | AdminController@index | Dashboard admin |
| GET | /admin/users | AdminController@users | Gestión de usuarios |
| POST | /admin/users/create | AdminController@createUser | Crear usuario |
| POST | /admin/users/{id}/update | AdminController@updateUser | Actualizar usuario |
| POST | /admin/users/{id}/delete | AdminController@deleteUser | Eliminar usuario |
| GET | /admin/logistics | AdminController@logistics | Evaluación logística |
| POST | /admin/logistics/evaluate | AdminController@evaluateLogistics | Evaluar proveedor |
| GET | /admin/vendedores | AdminController@vendedores | Gestión de vendedores |
| POST | /admin/vendedores/create | AdminController@createVendedor | Crear vendedor |
| POST | /admin/vendedores/{id}/update | AdminController@updateVendedor | Actualizar vendedor |
| POST | /admin/vendedores/{id}/delete | AdminController@deleteVendedor | Eliminar vendedor |
| POST | /admin/vendedores/asignar | AdminController@asignarVendedor | Asignar vendedor |
| POST | /admin/export/excel | ExcelSyncController@downloadFiltered | Exportar a Excel |

### 8.6 Parámetros Dinámicos en Rutas

El router soporta parámetros dinámicos usando la sintaxis `{nombre}`:

```php
// Definición de ruta
$router->get('/forms/{id}', 'FormController@show');

// Acceso en controlador
public function show(string $id): void {
    $form = $this->formModel->findById((int)$id);
    // ...
}
```

**Ejemplos**:
- `/forms/123` → `$id = "123"`
- `/approval/abc-def-ghi` → `$token = "abc-def-ghi"`
- `/forms/attachment/456` → `$id = "456"`

---


## 9. SERVICIOS Y LIBRERÍAS

### 9.1 Servicios del Sistema

#### 9.1.1 AuthService
**Ubicación**: `app/Services/AuthService.php`

**Responsabilidades**:
- Autenticación de usuarios y revisores
- Generación y validación de tokens JWT
- Gestión de sesiones PHP
- Verificación de permisos por rol

**Métodos principales**:
```php
public function login(string $email, string $password, ?string $role): array|false
public function logout(): void
public function isAuthenticated(): bool
public function hasRole(string $role): bool
public function generateJwt(array $payload): string
public function validateJwt(string $token): array|false
```

**Ejemplo de uso**:
```php
$authService = new AuthService();
$result = $authService->login('user@example.com', 'password123', 'cliente');

if ($result) {
    // Login exitoso
    $_SESSION['user_id'] = $result['user']['id'];
    $_SESSION['user_role'] = $result['user']['role'];
}
```

#### 9.1.2 PdfService
**Ubicación**: `app/Services/PdfService.php`

**Responsabilidades**:
- Generación de PDFs a partir de plantillas
- Consolidación de múltiples PDFs en uno
- Aplicación de firmas digitales
- Gestión de metadatos de PDFs

**Métodos principales**:
```php
public function generateFromTemplate(string $template, array $data): string
public function consolidatePdfs(array $pdfPaths, string $outputPath): bool
public function addSignature(string $pdfPath, string $signatureImage): bool
public function addWatermark(string $pdfPath, string $text): bool
```

**Ejemplo de uso**:
```php
$pdfService = new PdfService();
$pdfPath = $pdfService->generateFromTemplate('FGF-08', [
    'company_name' => 'Empresa XYZ',
    'nit' => '123456789',
    // ...
]);
```

#### 9.1.3 MailService
**Ubicación**: `app/Services/MailService.php`

**Responsabilidades**:
- Envío de emails con SMTP
- Soporte para plantillas HTML
- Adjuntos de archivos
- Imágenes embebidas (CID)

**Configuración SMTP**:
```php
Host: smtp.office365.com
Port: 587
Encryption: TLS
User: innovacion@pollo-fiesta.com
Password: (desde .env)
From: innovacion@pollo-fiesta.com
From Name: SAGRILAFT - Pollo Fiesta
```

**Métodos principales**:
```php
public function send(string $to, string $subject, string $body, array $attachments = []): bool
public function sendTemplate(string $to, string $template, array $data): bool
public function addAttachment(string $path, string $name): void
public function embedImage(string $path, string $cid): void
```

**Ejemplo de uso**:
```php
$mailService = new MailService();
$mailService->send(
    'user@example.com',
    'Formulario SAGRILAFT Aprobado',
    '<h1>Su formulario ha sido aprobado</h1>',
    ['path/to/pdf.pdf']
);
```

#### 9.1.4 Logger
**Ubicación**: `app/Services/Logger.php`

**Responsabilidades**:
- Registro de eventos del sistema
- Niveles de log (info, warning, error, debug)
- Rotación de archivos de log
- Formato estructurado (JSON)

**Métodos principales**:
```php
public function info(string $message, array $context = []): void
public function warning(string $message, array $context = []): void
public function error(string $message, array $context = []): void
public function debug(string $message, array $context = []): void
```

**Ejemplo de uso**:
```php
$logger = new Logger();
$logger->info('User logged in', ['user_id' => 123, 'ip' => '192.168.1.1']);
$logger->error('Failed to send email', ['error' => $e->getMessage()]);
```

**Ubicación de logs**: `storage/logs/app.log`

#### 9.1.5 FormPdfFiller
**Ubicación**: `app/Services/FormPdfFiller.php`

**Responsabilidades**:
- Rellena plantillas PDF con datos de formularios
- Soporta múltiples tipos de formularios
- Deserializa datos JSON (accionistas, etc.)
- Aplica firma digital del oficial de cumplimiento

**Tipos de formularios soportados**:
- FGF-08: Cliente persona natural
- FGF-16: Cliente persona jurídica
- FCO-05: Proveedor persona natural
- FCO-06: Proveedor persona jurídica
- FCO-07: Proveedor internacional
- FCO-08: Transportista
- Declaración de origen de fondos

**Métodos principales**:
```php
public function fillForm(string $formType, array $data): string
public function addSignature(string $pdfPath, int $userId): bool
private function deserializeJsonFields(array $data): array
```

**Ejemplo de uso**:
```php
$filler = new FormPdfFiller();
$pdfPath = $filler->fillForm('FGF-08', [
    'company_name' => 'Juan Pérez',
    'nit' => '123456789',
    'accionistas' => json_encode([
        ['nombre' => 'Juan Pérez', 'porcentaje' => 100]
    ])
]);
```

### 9.2 Librerías Externas

#### 9.2.1 FPDF (v1.86)
**Ubicación**: `app/Libraries/fpdf.php`

**Propósito**: Generación básica de PDFs desde cero.

**Características**:
- Creación de páginas y secciones
- Texto con diferentes fuentes y tamaños
- Imágenes (JPEG, PNG, GIF)
- Tablas y celdas
- Líneas y rectángulos
- Encabezados y pies de página

**Ejemplo de uso**:
```php
require_once 'app/Libraries/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'Hello World!');
$pdf->Output('F', 'output.pdf');
```

**Documentación**: http://www.fpdf.org/

#### 9.2.2 FPDI (v2.6.0)
**Ubicación**: `app/Libraries/FPDI-2.6.0/`

**Propósito**: Importación y manipulación de PDFs existentes.

**Características**:
- Importar páginas de PDFs existentes
- Usar PDFs como plantillas
- Agregar contenido sobre PDFs existentes
- Concatenar múltiples PDFs
- Rotar y escalar páginas

**Ejemplo de uso**:
```php
require_once 'app/Libraries/FPDI-2.6.0/src/autoload.php';

use setasign\Fpdi\Fpdi;

$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile('template.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0, 210);
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(50, 50);
$pdf->Write(0, 'Texto sobre plantilla');
$pdf->Output('F', 'output.pdf');
```

**Documentación**: https://www.setasign.com/products/fpdi/

#### 9.2.3 TCPDF (v6.7.5)
**Ubicación**: `app/Libraries/TCPDF-6.7.5/`

**Propósito**: Generación avanzada de PDFs con soporte para HTML y CSS.

**Características**:
- Conversión de HTML a PDF
- Soporte para CSS
- Códigos de barras (1D y 2D)
- Firmas digitales
- Encriptación de PDFs
- Soporte para UTF-8 y múltiples idiomas

**Ejemplo de uso**:
```php
require_once 'app/Libraries/TCPDF-6.7.5/tcpdf.php';

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$html = '<h1>Título</h1><p>Contenido HTML</p>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('output.pdf', 'F');
```

**Documentación**: https://tcpdf.org/

### 9.3 Comparación de Librerías PDF

| Característica | FPDF | FPDI | TCPDF |
|----------------|------|------|-------|
| Generación básica | ✅ | ✅ | ✅ |
| Importar PDFs | ❌ | ✅ | ❌ |
| HTML a PDF | ❌ | ❌ | ✅ |
| Firmas digitales | ❌ | ❌ | ✅ |
| Códigos de barras | ❌ | ❌ | ✅ |
| Tamaño librería | Pequeño | Mediano | Grande |
| Complejidad | Baja | Media | Alta |
| Uso en proyecto | Generación básica | Consolidación | Alternativa |

### 9.4 Helpers

#### 9.4.1 EmailHelper
**Ubicación**: `app/Helpers/EmailHelper.php`

**Responsabilidades**:
- Funciones auxiliares para emails
- Validación de direcciones de email
- Formateo de contenido HTML
- Generación de plantillas

**Funciones principales**:
```php
function validateEmail(string $email): bool
function formatEmailBody(string $template, array $data): string
function sanitizeEmailContent(string $content): string
```

---

## 10. CONFIGURACIÓN Y DESPLIEGUE

### 10.1 Requisitos del Sistema

#### 10.1.1 Servidor Web
- Apache 2.4 o superior
- Módulo mod_rewrite habilitado
- Módulo mod_headers habilitado (opcional, para headers de seguridad)

#### 10.1.2 PHP
- Versión: 8.2 o superior
- Extensiones requeridas:
  - pdo_mysql (conexión a MySQL)
  - mbstring (manejo de strings multibyte)
  - gd (manipulación de imágenes)
  - zip (compresión de archivos)
  - openssl (encriptación y HTTPS)
  - curl (peticiones HTTP)
  - json (manejo de JSON)

#### 10.1.3 Base de Datos
- MySQL 10.4 o superior (MariaDB)
- O MySQL 8.0 o superior

#### 10.1.4 Recursos del Servidor
- RAM: Mínimo 512MB, recomendado 2GB
- Espacio en disco: Mínimo 500MB (sin contar archivos subidos)
- Procesador: 1 core mínimo, 2+ cores recomendado

### 10.2 Instalación

#### 10.2.1 Clonar o Descargar el Proyecto
```bash
# Opción 1: Clonar desde Git
git clone https://github.com/pollo-fiesta/gestion-sagrilaft.git
cd gestion-sagrilaft

# Opción 2: Descargar ZIP y extraer
unzip gestion-sagrilaft.zip
cd gestion-sagrilaft
```

#### 10.2.2 Configurar Apache

**Opción A: Subdirectorio (desarrollo)**

Colocar el proyecto en `htdocs/gestion-sagrilaft/` y configurar `.htaccess` en la raíz:

```apache
# .htaccess (raíz del proyecto)
RewriteEngine On
RewriteBase /gestion-sagrilaft/
RewriteCond %{REQUEST_URI} !^/gestion-sagrilaft/public/
RewriteRule ^(.*)$ public/$1 [L]
```

**Opción B: VirtualHost (producción)**

Crear VirtualHost apuntando a `public/`:

```apache
<VirtualHost *:80>
    ServerName sagrilaft.pollo-fiesta.com
    DocumentRoot /var/www/gestion-sagrilaft/public
    
    <Directory /var/www/gestion-sagrilaft/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sagrilaft-error.log
    CustomLog ${APACHE_LOG_DIR}/sagrilaft-access.log combined
</VirtualHost>
```

Habilitar el sitio:
```bash
sudo a2ensite sagrilaft.conf
sudo systemctl reload apache2
```

#### 10.2.3 Configurar Base de Datos

1. Crear base de datos:
```sql
CREATE DATABASE sagrilaft CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Crear usuario:
```sql
CREATE USER 'sagrilaft_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON sagrilaft.* TO 'sagrilaft_user'@'localhost';
FLUSH PRIVILEGES;
```

3. Importar esquema:
```bash
mysql -u sagrilaft_user -p sagrilaft < database/INSTALACION_COMPLETA.sql
```

O usar el instalador web:
```
http://localhost/gestion-sagrilaft/public/instalar_bd.php
```

#### 10.2.4 Configurar Variables de Entorno

Copiar `.env.example` a `.env` y editar:

```bash
cp .env.example .env
nano .env
```

Configurar valores:
```env
APP_NAME=SAGRILAFT
APP_ENV=production
APP_URL=https://sagrilaft.pollo-fiesta.com

DB_HOST=localhost
DB_NAME=sagrilaft
DB_USER=sagrilaft_user
DB_PASS=password_seguro

JWT_SECRET=generar_clave_aleatoria_segura_aqui
JWT_EXPIRATION=3600

MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USER=innovacion@pollo-fiesta.com
MAIL_PASS=contraseña_email
MAIL_FROM=innovacion@pollo-fiesta.com
MAIL_FROM_NAME=SAGRILAFT - Pollo Fiesta
MAIL_ALERT_TO=pasantesistemas1@pollo-fiesta.com
```

**Generar JWT_SECRET seguro**:
```bash
php -r "echo bin2hex(random_bytes(32));"
```

#### 10.2.5 Configurar Permisos

```bash
# Dar permisos de escritura a carpetas necesarias
chmod -R 755 storage/
chmod -R 755 public/uploads/
chown -R www-data:www-data storage/
chown -R www-data:www-data public/uploads/

# Proteger archivo .env
chmod 600 .env
```

#### 10.2.6 Verificar Instalación

Acceder a:
```
http://localhost/gestion-sagrilaft/public/
```

Debería mostrar la página principal del sistema.

### 10.3 Configuración de Producción

#### 10.3.1 Habilitar HTTPS

Instalar certificado SSL (Let's Encrypt):
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d sagrilaft.pollo-fiesta.com
```

Descomentar en `public/.htaccess`:
```apache
# Redirect to HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

#### 10.3.2 Optimizar PHP

Editar `php.ini`:
```ini
; Producción
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log

; Límites
memory_limit = 512M
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 300

; Seguridad
expose_php = Off
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1

; OPcache (acelera PHP)
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
```

#### 10.3.3 Configurar Backups Automáticos

Crear script de backup:
```bash
#!/bin/bash
# /usr/local/bin/backup-sagrilaft.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/sagrilaft"
DB_NAME="sagrilaft"
DB_USER="sagrilaft_user"
DB_PASS="password_seguro"

# Backup de base de datos
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup de archivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/gestion-sagrilaft

# Eliminar backups antiguos (más de 30 días)
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completado: $DATE"
```

Programar con cron:
```bash
sudo crontab -e

# Backup diario a las 2 AM
0 2 * * * /usr/local/bin/backup-sagrilaft.sh >> /var/log/backup-sagrilaft.log 2>&1
```

#### 10.3.4 Monitoreo y Logs

Configurar rotación de logs:
```bash
# /etc/logrotate.d/sagrilaft
/var/www/gestion-sagrilaft/storage/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 10.4 Actualización del Sistema

#### 10.4.1 Proceso de Actualización

1. Hacer backup completo
2. Descargar nueva versión
3. Reemplazar archivos (excepto `.env`)
4. Ejecutar migraciones de BD (si hay)
5. Limpiar caché (si aplica)
6. Verificar funcionamiento

```bash
# Backup
./backup-sagrilaft.sh

# Descargar nueva versión
git pull origin main

# Ejecutar migraciones (si hay)
mysql -u sagrilaft_user -p sagrilaft < database/migrations/2026_04_08_nueva_migracion.sql

# Verificar
curl -I https://sagrilaft.pollo-fiesta.com
```

### 10.5 Solución de Problemas Comunes

#### 10.5.1 Error 500 - Internal Server Error

**Causa**: Error de PHP o configuración incorrecta.

**Solución**:
1. Revisar logs de Apache: `tail -f /var/log/apache2/error.log`
2. Revisar logs de PHP: `tail -f /var/log/php/error.log`
3. Verificar permisos de archivos
4. Verificar configuración de `.env`

#### 10.5.2 Error de Conexión a Base de Datos

**Causa**: Credenciales incorrectas o servidor MySQL no disponible.

**Solución**:
1. Verificar que MySQL esté corriendo: `sudo systemctl status mysql`
2. Verificar credenciales en `.env`
3. Probar conexión manual: `mysql -u sagrilaft_user -p`

#### 10.5.3 Archivos No Se Suben

**Causa**: Límites de PHP o permisos incorrectos.

**Solución**:
1. Verificar `upload_max_filesize` y `post_max_size` en `php.ini`
2. Verificar permisos de `public/uploads/`
3. Revisar logs de Apache para errores específicos

#### 10.5.4 Emails No Se Envían

**Causa**: Configuración SMTP incorrecta o firewall bloqueando puerto 587.

**Solución**:
1. Verificar credenciales SMTP en `.env`
2. Probar conexión: `telnet smtp.office365.com 587`
3. Revisar logs de aplicación: `storage/logs/app.log`
4. Verificar que el firewall permita salida por puerto 587

---


## 11. SEGURIDAD

### 11.1 Medidas de Seguridad Implementadas

#### 11.1.1 Autenticación y Autorización

**Hashing de Contraseñas**:
- Algoritmo: Bcrypt (PASSWORD_BCRYPT)
- Cost factor: 12 (por defecto en PHP 8.2)
- Nunca se almacenan contraseñas en texto plano

```php
// Al crear usuario
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Al verificar
if (password_verify($inputPassword, $storedHash)) {
    // Contraseña correcta
}
```

**Tokens de Sesión**:
- Sesiones PHP con cookies HttpOnly
- Regeneración de ID de sesión después del login
- Timeout de sesión: 1 hora de inactividad

**Tokens JWT** (opcional):
- Algoritmo: HS256
- Secret key: Almacenado en `.env`
- Expiración: 1 hora (configurable)

#### 11.1.2 Protección CSRF

Todos los formularios incluyen tokens CSRF:

```php
// Generar token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validar token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('Token CSRF inválido');
}
```

**Implementación en vistas**:
```html
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <!-- Otros campos -->
</form>
```

#### 11.1.3 Validación de Entrada

**Sanitización de datos**:
```php
// Limpiar entrada
$input = htmlspecialchars(trim($_POST['field']), ENT_QUOTES, 'UTF-8');

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception('Email inválido');
}

// Validar números
$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
```

**Prepared Statements** (prevención de SQL Injection):
```php
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

#### 11.1.4 Protección de Archivos

**Validación de archivos subidos**:
```php
// Verificar tipo MIME
if ($_FILES['file']['type'] !== 'application/pdf') {
    throw new Exception('Solo se permiten archivos PDF');
}

// Verificar tamaño
if ($_FILES['file']['size'] > 20 * 1024 * 1024) { // 20MB
    throw new Exception('Archivo demasiado grande');
}

// Verificar extensión
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    throw new Exception('Extensión no permitida');
}
```

**Almacenamiento seguro**:
- Archivos almacenados como BLOB en base de datos (no en filesystem)
- Nombres de archivo sanitizados
- Validación de permisos antes de servir archivos

#### 11.1.5 Headers de Seguridad

Configurados en `public/.htaccess`:

```apache
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

**Descripción**:
- `X-Content-Type-Options`: Previene MIME sniffing
- `X-Frame-Options`: Previene clickjacking
- `X-XSS-Protection`: Activa filtro XSS del navegador
- `Referrer-Policy`: Controla información de referrer

#### 11.1.6 Protección de Archivos Sensibles

**`.htaccess` en raíz**:
```apache
# Proteger .env
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>

# Proteger archivos de configuración
<FilesMatch "\.(ini|log|sh|sql)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

**Deshabilitar listado de directorios**:
```apache
Options -Indexes
```

#### 11.1.7 Rate Limiting (recomendado implementar)

Para prevenir ataques de fuerza bruta:

```php
// Ejemplo de implementación básica
$attempts = $_SESSION['login_attempts'] ?? 0;
$lastAttempt = $_SESSION['last_login_attempt'] ?? 0;

if ($attempts >= 5 && time() - $lastAttempt < 900) { // 15 minutos
    die('Demasiados intentos. Intente en 15 minutos.');
}

// Incrementar intentos
$_SESSION['login_attempts'] = $attempts + 1;
$_SESSION['last_login_attempt'] = time();

// Resetear en login exitoso
if ($loginSuccess) {
    unset($_SESSION['login_attempts']);
    unset($_SESSION['last_login_attempt']);
}
```

### 11.2 Buenas Prácticas de Seguridad

#### 11.2.1 Gestión de Contraseñas

**Requisitos mínimos**:
- Longitud mínima: 6 caracteres (recomendado: 8+)
- Incluir mayúsculas, minúsculas, números y símbolos (recomendado)
- No permitir contraseñas comunes (implementable)

**Recuperación de contraseña**:
- Códigos de un solo uso
- Expiración de 15 minutos
- Envío solo por email verificado

#### 11.2.2 Control de Acceso

**Principio de mínimo privilegio**:
- Usuarios solo acceden a sus propios formularios
- Revisores acceden a todos los formularios (solo lectura)
- Administradores tienen acceso completo

**Validación de permisos**:
```php
// Verificar que el usuario sea dueño del formulario
if ($form['user_id'] !== $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Acceso denegado');
}
```

#### 11.2.3 Auditoría y Logging

**Eventos registrados**:
- Inicios de sesión (exitosos y fallidos)
- Creación/modificación/eliminación de formularios
- Aprobaciones/rechazos
- Cambios de contraseña
- Acceso a archivos sensibles

**Formato de logs**:
```json
{
    "timestamp": "2026-04-08 10:30:45",
    "level": "info",
    "message": "User logged in",
    "context": {
        "user_id": 123,
        "ip": "192.168.1.100",
        "user_agent": "Mozilla/5.0..."
    }
}
```

#### 11.2.4 Protección de Datos Sensibles

**Datos personales**:
- Almacenados encriptados en BD (recomendado para producción)
- Acceso restringido por rol
- Logs no contienen información sensible

**Cumplimiento GDPR/LOPD**:
- Consentimiento explícito para tratamiento de datos
- Derecho al olvido (eliminación de datos)
- Portabilidad de datos (exportación)

### 11.3 Checklist de Seguridad para Producción

- [ ] Cambiar `APP_ENV` a `production` en `.env`
- [ ] Generar `JWT_SECRET` aleatorio y seguro
- [ ] Cambiar contraseñas de base de datos
- [ ] Habilitar HTTPS con certificado válido
- [ ] Configurar headers de seguridad
- [ ] Deshabilitar `display_errors` en PHP
- [ ] Configurar permisos de archivos correctamente
- [ ] Implementar rate limiting en login
- [ ] Configurar backups automáticos
- [ ] Revisar logs regularmente
- [ ] Actualizar PHP y MySQL a últimas versiones
- [ ] Implementar firewall (UFW, iptables)
- [ ] Configurar fail2ban para proteger SSH
- [ ] Revisar código con herramientas de análisis estático
- [ ] Realizar pruebas de penetración

### 11.4 Vulnerabilidades Comunes y Prevención

| Vulnerabilidad | Prevención Implementada |
|----------------|-------------------------|
| SQL Injection | Prepared statements con PDO |
| XSS (Cross-Site Scripting) | htmlspecialchars() en todas las salidas |
| CSRF | Tokens CSRF en todos los formularios |
| Clickjacking | Header X-Frame-Options: SAMEORIGIN |
| File Upload | Validación de tipo MIME y extensión |
| Session Hijacking | HttpOnly cookies, regeneración de ID |
| Brute Force | Rate limiting (recomendado) |
| Directory Traversal | Validación de rutas, Options -Indexes |
| Information Disclosure | Ocultar errores en producción |
| Insecure Direct Object References | Validación de permisos por usuario |

---

## 12. MANTENIMIENTO Y SOPORTE

### 12.1 Tareas de Mantenimiento Rutinarias

#### 12.1.1 Diarias
- Revisar logs de errores
- Verificar que los emails se envíen correctamente
- Monitorear espacio en disco
- Verificar backups automáticos

#### 12.1.2 Semanales
- Revisar formularios pendientes de aprobación
- Verificar integridad de base de datos
- Analizar logs de acceso para detectar anomalías
- Revisar rendimiento del servidor

#### 12.1.3 Mensuales
- Actualizar PHP y MySQL a últimas versiones de seguridad
- Revisar y optimizar base de datos
- Limpiar logs antiguos
- Revisar y actualizar documentación
- Realizar pruebas de recuperación de backups

#### 12.1.4 Trimestrales
- Auditoría de seguridad completa
- Revisar y actualizar políticas de acceso
- Capacitación de usuarios
- Revisión de rendimiento y optimización

### 12.2 Monitoreo del Sistema

#### 12.2.1 Métricas Clave

**Disponibilidad**:
- Uptime del servidor: >99.9%
- Tiempo de respuesta: <2 segundos

**Uso de recursos**:
- CPU: <70% en promedio
- RAM: <80% en promedio
- Disco: <80% de capacidad

**Base de datos**:
- Tamaño de BD
- Número de formularios
- Consultas lentas (>1 segundo)

**Aplicación**:
- Errores por hora
- Formularios creados por día
- Emails enviados por día
- Usuarios activos

#### 12.2.2 Herramientas de Monitoreo

**Logs del sistema**:
```bash
# Logs de Apache
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log

# Logs de PHP
tail -f /var/log/php/error.log


# Logs de aplicación
tail -f /var/www/gestion-sagrilaft/storage/logs/app.log
```

**Monitoreo de recursos**:
```bash
# CPU y RAM
htop

# Espacio en disco
df -h

# Procesos de MySQL
mysqladmin -u root -p processlist

# Estado de Apache
sudo systemctl status apache2
```

**Herramientas externas** (recomendadas):
- Uptime Robot: Monitoreo de disponibilidad
- New Relic: Monitoreo de rendimiento
- Sentry: Tracking de errores
- Grafana + Prometheus: Dashboards de métricas

### 12.3 Optimización de Rendimiento

#### 12.3.1 Base de Datos

**Índices**:
```sql
-- Verificar índices existentes
SHOW INDEX FROM forms;

-- Agregar índices si es necesario
CREATE INDEX idx_approval_status ON forms(approval_status);
CREATE INDEX idx_created_at ON forms(created_at);
```

**Optimización de tablas**:
```sql
-- Analizar tablas
ANALYZE TABLE forms;
ANALYZE TABLE users;

-- Optimizar tablas
OPTIMIZE TABLE forms;
OPTIMIZE TABLE form_attachments;
```

**Consultas lentas**:
```sql
-- Habilitar log de consultas lentas
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;

-- Revisar consultas lentas
SELECT * FROM mysql.slow_log ORDER BY query_time DESC LIMIT 10;
```

#### 12.3.2 PHP

**OPcache**:
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

**Caché de sesiones**:
```ini
; php.ini
session.save_handler = redis
session.save_path = "tcp://127.0.0.1:6379"
```

#### 12.3.3 Apache

**Compresión**:
```apache
# Habilitar mod_deflate
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

**Caché de archivos estáticos**:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 12.4 Procedimientos de Respaldo y Recuperación

#### 12.4.1 Backup Completo

**Script de backup**:
```bash
#!/bin/bash
# backup-full.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/sagrilaft"
DB_NAME="sagrilaft"
DB_USER="sagrilaft_user"
DB_PASS="password_seguro"
APP_DIR="/var/www/gestion-sagrilaft"

# Crear directorio de backup
mkdir -p $BACKUP_DIR/$DATE

# Backup de base de datos
echo "Backing up database..."
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/$DATE/database.sql.gz

# Backup de archivos
echo "Backing up files..."
tar -czf $BACKUP_DIR/$DATE/files.tar.gz $APP_DIR

# Backup de configuración
echo "Backing up configuration..."
cp $APP_DIR/.env $BACKUP_DIR/$DATE/.env

# Crear checksum
echo "Creating checksum..."
cd $BACKUP_DIR/$DATE
sha256sum * > checksums.txt

echo "Backup completed: $BACKUP_DIR/$DATE"
```

#### 12.4.2 Restauración

**Restaurar base de datos**:
```bash
# Descomprimir backup
gunzip database.sql.gz

# Restaurar
mysql -u sagrilaft_user -p sagrilaft < database.sql
```

**Restaurar archivos**:
```bash
# Extraer backup
tar -xzf files.tar.gz -C /

# Restaurar permisos
chown -R www-data:www-data /var/www/gestion-sagrilaft
chmod -R 755 /var/www/gestion-sagrilaft
```

**Verificar integridad**:
```bash
# Verificar checksums
cd /backups/sagrilaft/20260408_020000
sha256sum -c checksums.txt
```

### 12.5 Soporte y Contacto

#### 12.5.1 Equipo de Desarrollo

**Contacto principal**:
- Email: innovacion@pollo-fiesta.com
- Teléfono: [Número de contacto]
- Horario: Lunes a Viernes, 8:00 AM - 5:00 PM

**Soporte técnico**:
- Email: pasantesistemas1@pollo-fiesta.com
- Nivel 1: Problemas de usuarios (login, formularios)
- Nivel 2: Problemas técnicos (servidor, base de datos)
- Nivel 3: Desarrollo y cambios en código

#### 12.5.2 Procedimiento de Reporte de Errores

1. **Identificar el error**:
   - Captura de pantalla
   - Mensaje de error completo
   - Pasos para reproducir

2. **Recopilar información**:
   - URL donde ocurrió
   - Navegador y versión
   - Usuario afectado
   - Fecha y hora

3. **Enviar reporte**:
   - Email a soporte técnico
   - Incluir toda la información recopilada
   - Indicar prioridad (crítico, alto, medio, bajo)

4. **Seguimiento**:
   - Ticket de soporte asignado
   - Actualizaciones por email
   - Resolución y cierre

#### 12.5.3 Niveles de Prioridad

| Prioridad | Descripción | Tiempo de Respuesta |
|-----------|-------------|---------------------|
| Crítico | Sistema no disponible | 1 hora |
| Alto | Funcionalidad principal afectada | 4 horas |
| Medio | Funcionalidad secundaria afectada | 1 día |
| Bajo | Mejora o problema menor | 1 semana |

### 12.6 Documentación Adicional

#### 12.6.1 Manuales de Usuario

- **Manual de Usuario Cliente**: Cómo completar formularios
- **Manual de Usuario Revisor**: Cómo aprobar/rechazar formularios
- **Manual de Usuario Administrador**: Gestión de usuarios y configuración

#### 12.6.2 Documentación Técnica

- **Documentación de API** (si aplica)
- **Guía de Desarrollo**: Cómo agregar nuevas funcionalidades
- **Guía de Estilo de Código**: Convenciones y buenas prácticas
- **Diagramas de Arquitectura**: Diagramas UML y de flujo

#### 12.6.3 Recursos Externos

- **PHP Manual**: https://www.php.net/manual/es/
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **Apache Documentation**: https://httpd.apache.org/docs/
- **FPDF Documentation**: http://www.fpdf.org/
- **FPDI Documentation**: https://www.setasign.com/products/fpdi/

---

## APÉNDICES

### Apéndice A: Glosario de Términos

| Término | Definición |
|---------|------------|
| SAGRILAFT | Sistema de Administración del Riesgo de Lavado de Activos y Financiación del Terrorismo |
| PEP | Persona Expuesta Políticamente |
| OFAC | Office of Foreign Assets Control (Oficina de Control de Activos Extranjeros de EE.UU.) |
| CIIU | Clasificación Industrial Internacional Uniforme |
| NIT | Número de Identificación Tributaria |
| CSRF | Cross-Site Request Forgery (Falsificación de Petición en Sitios Cruzados) |
| XSS | Cross-Site Scripting (Secuencias de Comandos en Sitios Cruzados) |
| JWT | JSON Web Token |
| PDO | PHP Data Objects |
| BLOB | Binary Large Object |
| SMTP | Simple Mail Transfer Protocol |
| SSL/TLS | Secure Sockets Layer / Transport Layer Security |
| MVC | Model-View-Controller |

### Apéndice B: Códigos de Estado HTTP

| Código | Significado | Uso en el Sistema |
|--------|-------------|-------------------|
| 200 | OK | Petición exitosa |
| 201 | Created | Recurso creado exitosamente |
| 301 | Moved Permanently | Redirección permanente |
| 302 | Found | Redirección temporal |
| 400 | Bad Request | Datos de entrada inválidos |
| 401 | Unauthorized | No autenticado |
| 403 | Forbidden | No autorizado (sin permisos) |
| 404 | Not Found | Recurso no encontrado |
| 500 | Internal Server Error | Error del servidor |
| 503 | Service Unavailable | Servicio no disponible |

### Apéndice C: Tipos de Formularios

| Código | Nombre | Descripción |
|--------|--------|-------------|
| FGF-08 | Cliente Persona Natural | Formulario para clientes personas naturales |
| FGF-16 | Cliente Persona Jurídica | Formulario para clientes personas jurídicas |
| FCO-05 | Proveedor Persona Natural | Formulario para proveedores personas naturales |
| FCO-06 | Proveedor Persona Jurídica | Formulario para proveedores personas jurídicas |
| FCO-07 | Proveedor Internacional | Formulario para proveedores internacionales |
| FCO-08 | Transportista | Formulario para empresas de transporte |
| - | Declaración de Origen de Fondos | Declaración complementaria vinculada al formulario principal |

### Apéndice D: Estados de Aprobación

| Estado | Descripción | Siguiente Acción |
|--------|-------------|------------------|
| pending | Pendiente de revisión | Revisor debe aprobar/rechazar |
| approved | Aprobado | Proceso completado |
| approved_pending | Aprobado con observaciones | Usuario debe revisar observaciones |
| rejected | Rechazado | Usuario debe corregir y reenviar |
| corrected | Corregido por usuario | Vuelve a estado pending |

### Apéndice E: Roles de Usuario

| Rol | Permisos | Acceso |
|-----|----------|--------|
| cliente | Crear y ver sus propios formularios | /forms, /profile |
| proveedor | Crear y ver sus propios formularios | /forms, /profile |
| transportista | Crear y ver sus propios formularios | /forms, /profile |
| revisor | Ver y aprobar/rechazar todos los formularios | /reviewer/dashboard, /approval/* |
| admin | Acceso completo al sistema | /admin/*, todas las rutas |

### Apéndice F: Variables de Entorno

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| APP_NAME | Nombre de la aplicación | SAGRILAFT |
| APP_ENV | Entorno (development, production) | production |
| APP_URL | URL base de la aplicación | https://sagrilaft.pollo-fiesta.com |
| DB_HOST | Host de la base de datos | localhost |
| DB_NAME | Nombre de la base de datos | sagrilaft |
| DB_USER | Usuario de la base de datos | sagrilaft_user |
| DB_PASS | Contraseña de la base de datos | password_seguro |
| JWT_SECRET | Clave secreta para JWT | abc123... |
| JWT_EXPIRATION | Tiempo de expiración de JWT (segundos) | 3600 |
| MAIL_HOST | Host del servidor SMTP | smtp.office365.com |
| MAIL_PORT | Puerto del servidor SMTP | 587 |
| MAIL_USER | Usuario de email | innovacion@pollo-fiesta.com |
| MAIL_PASS | Contraseña de email | contraseña |
| MAIL_FROM | Email remitente | innovacion@pollo-fiesta.com |
| MAIL_FROM_NAME | Nombre del remitente | SAGRILAFT - Pollo Fiesta |
| MAIL_ALERT_TO | Email para alertas | pasantesistemas1@pollo-fiesta.com |

---

## HISTORIAL DE CAMBIOS

| Versión | Fecha | Autor | Cambios |
|---------|-------|-------|---------|
| 1.0 | 2026-04-08 | Equipo de Desarrollo | Versión inicial de la documentación |

---

## CONCLUSIÓN

Este documento proporciona una visión completa del Sistema SAGRILAFT, desde su arquitectura y funcionalidades hasta su configuración, seguridad y mantenimiento. Es una guía de referencia para desarrolladores, administradores de sistemas y personal de soporte.

Para cualquier consulta o aclaración, contactar al equipo de desarrollo en innovacion@pollo-fiesta.com.

---

**FIN DEL DOCUMENTO**
