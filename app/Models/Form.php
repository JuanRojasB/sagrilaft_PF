<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo de Formulario
 * 
 * Gestiona todas las operaciones relacionadas con formularios en la base de datos.
 * Incluye funciones para crear, leer, actualizar, eliminar y gestionar aprobaciones.
 * 
 * @package App\Models
 */
class Form
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener todos los formularios de un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de formularios del usuario
     */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM forms WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Buscar formulario por ID
     * 
     * @param int $id ID del formulario
     * @return array|null Datos del formulario o null si no existe
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM forms WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $form = $stmt->fetch();
        
        return $form ?: null;
    }

    /**
     * Crear nuevo formulario
     * 
     * @param array $data Datos del formulario (user_id, title, content, company_name, nit, activity, address, phone, status)
     * @return int ID del formulario creado
     */
    /**
     * Crear nuevo formulario
     * 
     * @param array $data Datos del formulario
     * @return int ID del formulario creado
     */
    public function create(array $data): int
    {
        // Asegurar que content y title nunca estén vacíos
        $data['content'] = !empty($data['content']) ? trim($data['content']) : 'N/A';
        $data['title']   = !empty($data['title'])   ? trim($data['title'])   : 'Sin título';

        // Todos los campos permitidos (columnas que existen en la tabla)
        $allowed = [
            'user_id', 'title', 'content', 'company_name', 'nit', 'dv', 'activity', 'address', 'phone', 'status',
            'form_type', 'person_type', 'related_form_id',
            'celular', 'ciudad', 'barrio', 'localidad', 'pais', 'departamento', 'codigo_ciiu',
            'telefono_fijo', 'telefono', 'fax', 'email',
            'activos', 'pasivos', 'patrimonio', 'ingresos', 'gastos',
            'otros_ingresos', 'detalle_otros_ingresos',
            'representante_nombre', 'representante_documento', 'representante_tipo_doc',
            'representante_profesion', 'representante_nacimiento',
            'representante_telefono', 'representante_residencia', 'representante_lugar_nacimiento',
            'representante_email', 'representante_cargo', 'representante_nacionalidad',
            'origen_fondos', 'es_pep', 'cargo_pep',
            'tiene_cuentas_exterior', 'pais_cuentas_exterior',
            'autoriza_centrales_riesgo', 'autoriza_centrales',
            'consulta_ofac', 'consulta_listas_nacionales', 'consulta_onu', 'consulta_interpol',
            'tipo_contribuyente', 'regimen_tributario', 'responsable_iva',
            'vinculacion', 'fecha_vinculacion', 'actualizacion',
            'nombre_establecimiento',
            'lista_precios', 'codigo_vendedor', 'forma_pago',
            'fecha_nacimiento', 'mes_nacimiento', 'dia_nacimiento',
            'nombre_vendedor', 'clase_cliente', 'descripcion_firma',
            'recibe', 'director_cartera', 'gerencia_comercial', 'nombre_oficial',
            'director_compras', 'firma_oficial', 'firma_vendedor',
            'observaciones',
            'rut', 'objeto_social', 'nombre_comercial',
            'tipo_pago', 'contado', 'credito', 'mixto',
            'accionistas', 'referencias_comerciales', 'asesor_comercial_id',
            // Empresa / proveedor
            'tipo_compania', 'certificacion', 'numero_registro', 'sitio_web',
            'productos_servicios', 'pagina_web', 'fecha_constitucion',
            'numero_empleados', 'tiempo_mercado',
            // Importacion internacional
            'incoterm', 'forma_pago_internacional', 'tiempo_entrega',
            'puerto_origen', 'agente_aduanal', 'certificado_origen',
            // Bancario
            'banco', 'tipo_cuenta', 'numero_cuenta',
            // Firmas internas
            'preparo', 'reviso', 'nombre_firmante',
            // Declaracion de fondos
            'nombre_declarante', 'tipo_documento', 'numero_documento', 'calidad',
            'empresa', 'nit_empresa', 'origen_recursos',
            'periodo_pep', 'familiar_pep', 'familiar_pep_detalle',
            'vinculo_pep', 'vinculo_pep_detalle',
            'ingresos_mensuales', 'egresos_mensuales',
            'total_activos', 'total_pasivos', 'patrimonio_neto',
            'opera_moneda_extranjera', 'paises_operacion',
            'cuentas_exterior', 'cuentas_exterior_detalle',
            'verificado_por', 'fecha_verificacion',
            'fecha_declaracion', 'ciudad_declaracion',
            'nombre_firma_final', 'documento_firma',
            'firma_declarante',
            // Empleado
            'empleado_nombre', 'empleado_cedula', 'empleado_cargo', 'empleado_fecha_nacimiento',
            'approval_status', 'approval_token',
        ];

        $columns = [];
        $values  = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '') {
                $columns[] = $field;
                $values[]  = $data[$field];
            }
        }

        // Asegurar campos obligatorios
        if (!in_array('user_id', $columns))  { $columns[] = 'user_id';  $values[] = $data['user_id'] ?? 0; }
        if (!in_array('title', $columns))    { $columns[] = 'title';    $values[] = $data['title']; }
        if (!in_array('content', $columns))  { $columns[] = 'content';  $values[] = $data['content']; }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $colList      = implode(', ', $columns);

        $sql  = "INSERT INTO forms ($colList) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        
        $insertedId = (int)$this->db->lastInsertId();
        error_log("Form INSERT - SQL: $sql");
        error_log("Form INSERT - Last Insert ID: $insertedId");
        error_log("Form INSERT - Columns: $colList");

        return $insertedId;
    }


    /**
     * Actualizar formulario
     * 
     * @param int $id ID del formulario
     * @param array $data Datos a actualizar (title, content, status)
     * @return bool true si se actualizó correctamente
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE forms SET title = ?, content = ?, status = ?, updated_at = NOW() 
             WHERE id = ?"
        );
        
        return $stmt->execute([
            $data['title'],
            json_encode($data['content']),
            $data['status'],
            $id
        ]);
    }

    /**
     * Eliminar formulario
     * 
     * @param int $id ID del formulario
     * @return bool true si se eliminó correctamente
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM forms WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Actualizar token de aprobación
     * 
     * @param int $id ID del formulario
     * @param string $token Token único de aprobación
     * @return bool true si se actualizó correctamente
     */
    public function updateApprovalToken(int $id, string $token): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE forms SET approval_token = ? WHERE id = ?"
        );
        return $stmt->execute([$token, $id]);
    }

    /**
     * Buscar formulario por token de aprobación
     * 
     * @param string $token Token de aprobación
     * @return array|null Datos del formulario o null si no existe
     */
    public function findByApprovalToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM forms WHERE approval_token = ? LIMIT 1");
        $stmt->execute([$token]);
        $form = $stmt->fetch();
        
        return $form ?: null;
    }

    /**
     * Actualizar estado de aprobación
     * 
     * @param int $id ID del formulario
     * @param string $status Estado (approved, rejected, pending)
     * @param string $approvedBy Nombre del revisor
     * @param string $observations Observaciones del revisor
     * @return bool true si se actualizó correctamente
     */
    public function updateApprovalStatus(int $id, string $status, string $approvedBy, string $observations): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE forms 
             SET approval_status = ?, 
                 approved_by = ?, 
                 approval_observations = ?, 
                 approval_date = NOW(),
                 reviewed_at = NOW(),
                 reviewed_by = ?,
                 reviewed_by_name = ?,
                 updated_at = NOW()
             WHERE id = ?"
        );
        
        return $stmt->execute([$status, $approvedBy, $observations, $approvedBy, $approvedBy, $id]);
    }

    /**
     * Obtener el creador del formulario
     * 
     * @param int $formId ID del formulario
     * @return array|null Datos del usuario creador o null
     */
    public function getFormCreator(int $formId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT u.* FROM users u 
             INNER JOIN forms f ON f.user_id = u.id 
             WHERE f.id = ? LIMIT 1"
        );
        $stmt->execute([$formId]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    /**
     * Obtener todos los formularios pendientes de revisión
     * 
     * @return array Lista de formularios pendientes con información del creador
     */
    public function getPendingForms(): array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, u.name as creator_name, u.email as creator_email, u.role 
             FROM forms f
             INNER JOIN users u ON f.user_id = u.id
             WHERE f.approval_status = 'pending'
             AND (f.related_form_id IS NULL OR f.related_form_id = 0)
             AND (f.form_type IS NULL OR f.form_type NOT LIKE 'declaracion%')
             ORDER BY f.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener todos los formularios (para dashboard de revisor)
     * 
     * @return array Lista de todos los formularios con información del creador
     */
    public function getAllForms(): array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, u.name as creator_name, u.email as creator_email, u.role 
             FROM forms f
             INNER JOIN users u ON f.user_id = u.id
             WHERE (f.related_form_id IS NULL OR f.related_form_id = 0)
             AND (f.form_type IS NULL OR f.form_type NOT LIKE 'declaracion%')
             ORDER BY f.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
     * Obtener conexión a la base de datos
     * 
     * @return PDO Conexión PDO
     */
    public function getConnection(): PDO
    {
        return $this->db;
    }
    
    /**
     * Buscar formularios relacionados del mismo usuario con el mismo NIT
     * que tengan estado 'approved_pending' (con observaciones)
     * 
     * @param int $userId ID del usuario
     * @param string $nit NIT/Cédula a buscar
     * @param int|null $excludeFormId ID del formulario actual a excluir
     * @return array Lista de formularios relacionados con observaciones pendientes
     */
    public function findRelatedFormsWithObservations(int $userId, string $nit, ?int $excludeFormId = null): array
    {
        $sql = "SELECT * FROM forms 
                WHERE user_id = ? 
                AND nit = ? 
                AND approval_status = 'approved_pending'";
        
        $params = [$userId, $nit];
        
        if ($excludeFormId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeFormId;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Marcar un formulario como corregido por otro formulario
     * 
     * @param int $oldFormId ID del formulario que fue corregido
     * @param int $newFormId ID del formulario que lo corrige
     * @return bool true si se actualizó correctamente
     */
    public function markAsCorrected(int $oldFormId, int $newFormId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE forms 
             SET approval_status = 'corrected',
                 corrected_by_form_id = ?,
                 updated_at = NOW()
             WHERE id = ?"
        );
        
        return $stmt->execute([$newFormId, $oldFormId]);
    }
}
