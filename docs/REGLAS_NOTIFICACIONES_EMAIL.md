# Reglas de Notificaciones por Email - SAGRILAFT

## Clientes (Persona Natural y Persona Jurídica)

### Clientes Aprobados
- **Destinatarios principales**: Vendedor, Área/Departamento de Cartera
- **Con copia (CC)**: Gerente Comercial de la Sede

### Clientes Pendientes por Aprobar o Con Observaciones
- **Destinatarios principales**: Vendedor
- **Con copia (CC)**: Gerente Comercial de la Sede
- **Contenido**: Incluir observaciones o pendientes correspondientes

### Clientes No Aprobados
- **Destinatarios principales**: Vendedor
- **Con copia (CC)**: Gerente Comercial

---

## Proveedores (Persona Natural y Persona Jurídica)

### Proveedores Aprobados
- **Destinatarios principales**: Área de Compras, Tesorería, Contabilidad

### Proveedores Pendientes por Aprobar o Con Observaciones
- **Destinatarios principales**: Área de Compras

### Proveedores No Aprobados
- **Destinatarios principales**: Área de Compras

### Nota Especial - Áreas Externas
Cuando hay áreas externas que solicitan vinculación de proveedores (Mantenimiento, Gestión Humana, Contabilidad, Producción, Gerencia, Logística, etc.), se envía correo **con copia (CC)** a estas áreas para cada uno de los casos anteriores.

---

## Transportadores (Persona Natural y Persona Jurídica)

### Transportadores Aprobados
- **Destinatarios principales**: Daniela, Ing. Diego Rodríguez

### Transportadores Pendientes por Aprobar o Con Observaciones
- **Destinatarios principales**: Daniela, Ing. Diego Rodríguez

### Transportadores No Aprobados
- **Destinatarios principales**: Daniela, Ing. Diego Rodríguez

---

## Gestión Humana (Persona Natural)

### Todos los Estados (Aprobado, Pendiente, No Aprobado)
- **Destinatarios principales**: 
  - Sra. Elsa
  - Dra. Johana
  - Lorena de Selección

---

## Implementación Técnica

### Variables de Entorno Requeridas (.env)
```env
# Clientes
MAIL_VENDEDOR=vendedor@empresa.com
MAIL_CARTERA=cartera@empresa.com
MAIL_GERENTE_COMERCIAL=gerente.comercial@empresa.com

# Proveedores
MAIL_COMPRAS=compras@empresa.com
MAIL_TESORERIA=tesoreria@empresa.com
MAIL_CONTABILIDAD=contabilidad@empresa.com

# Transportadores
MAIL_DANIELA=daniela@empresa.com
MAIL_ING_DIEGO=diego.rodriguez@empresa.com

# Gestión Humana
MAIL_ELSA=elsa@empresa.com
MAIL_JOHANA=johana@empresa.com
MAIL_LORENA_SELECCION=lorena.seleccion@empresa.com

# Áreas externas (opcional, separadas por coma)
MAIL_AREAS_EXTERNAS=mantenimiento@empresa.com,produccion@empresa.com
```

### Estructura de Datos en Base de Datos

#### Tabla: forms
- `user_type`: 'cliente' | 'proveedor' | 'transportador' | 'gestion_humana'
- `person_type`: 'natural' | 'juridica'
- `approval_status`: 'pending' | 'approved' | 'rejected'
- `approval_observations`: TEXT (observaciones del revisor)
- `area_solicitante`: VARCHAR (para proveedores con áreas externas)

### Lógica de Envío

```php
function getEmailRecipients($form) {
    $userType = $form['user_type'];
    $status = $form['approval_status'];
    $to = [];
    $cc = [];
    
    switch($userType) {
        case 'cliente':
            $to[] = $_ENV['MAIL_VENDEDOR'];
            if ($status === 'approved') {
                $to[] = $_ENV['MAIL_CARTERA'];
            }
            $cc[] = $_ENV['MAIL_GERENTE_COMERCIAL'];
            break;
            
        case 'proveedor':
            $to[] = $_ENV['MAIL_COMPRAS'];
            if ($status === 'approved') {
                $to[] = $_ENV['MAIL_TESORERIA'];
                $to[] = $_ENV['MAIL_CONTABILIDAD'];
            }
            // Agregar áreas externas si existen
            if (!empty($form['area_solicitante'])) {
                $cc[] = $form['area_solicitante'];
            }
            break;
            
        case 'transportador':
            $to[] = $_ENV['MAIL_DANIELA'];
            $to[] = $_ENV['MAIL_ING_DIEGO'];
            break;
            
        case 'gestion_humana':
            $to[] = $_ENV['MAIL_ELSA'];
            $to[] = $_ENV['MAIL_JOHANA'];
            $to[] = $_ENV['MAIL_LORENA_SELECCION'];
            break;
    }
    
    return ['to' => $to, 'cc' => $cc];
}
```

---

## Notas de Implementación

1. **Pendiente**: Implementar la lógica de envío diferenciado según tipo y estado
2. **Pendiente**: Agregar campos en la base de datos para `user_type` y `area_solicitante`
3. **Pendiente**: Configurar variables de entorno con los correos reales
4. **Pendiente**: Crear templates de email específicos para cada tipo de notificación
5. **Pendiente**: Implementar sistema de tracking de emails enviados

---

**Fecha de documentación**: 2 de marzo de 2026
**Estado**: Documentado - Pendiente de implementación
