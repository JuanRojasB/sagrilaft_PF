<?php

namespace App\Models;

use PDO;

/**
 * Modelo Documento
 * 
 * Gestiona los documentos subidos por usuarios para evaluación.
 * Los revisores pueden aprobar, rechazar o solicitar revisión.
 * 
 * Estados:
 * - pendiente: Documento subido, esperando revisión
 * - revision: Revisor solicita correcciones
 * - aprobado: Documento aprobado
 * - rechazado: Documento rechazado
 * 
 * @package App\Models
 */
class Documento
{
    private PDO $db;

    public function __construct()
    {
        $this->db = \App\Core\Database::getConnection();
    }

    /**
     * Obtener todos los documentos de un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de documentos
     */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, 
                   u.name as revisor_name
            FROM documentos d
            LEFT JOIN users u ON d.revisado_por = u.id
            WHERE d.user_id = ?
            ORDER BY d.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener documento por ID
     * 
     * @param int $id ID del documento
     * @return array|false Datos del documento o false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM documentos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Crear nuevo documento
     * 
     * @param array $data Datos del documento
     * @return int ID del documento creado
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO documentos 
            (user_id, nombre_archivo, ruta_archivo, tipo_documento, tamano_bytes, extension, estado)
            VALUES (?, ?, ?, ?, ?, ?, 'pendiente')
        ");
        
        $stmt->execute([
            $data['user_id'],
            $data['nombre_archivo'],
            $data['ruta_archivo'],
            $data['tipo_documento'],
            $data['tamano_bytes'],
            $data['extension']
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar estado del documento
     * 
     * @param int $id ID del documento
     * @param string $estado Nuevo estado
     * @param int $revisorId ID del revisor
     * @param string|null $observaciones Observaciones
     * @return bool True si se actualizó
     */
    public function updateEstado(int $id, string $estado, int $revisorId, ?string $observaciones = null): bool
    {
        // Obtener estado anterior para historial
        $doc = $this->findById($id);
        
        $stmt = $this->db->prepare("
            UPDATE documentos 
            SET estado = ?, 
                observaciones = ?, 
                revisado_por = ?,
                fecha_revision = NOW()
            WHERE id = ?
        ");
        
        $result = $stmt->execute([$estado, $observaciones, $revisorId, $id]);
        
        // Registrar en historial
        if ($result && $doc) {
            $this->registrarHistorial($id, $doc['user_id'], $revisorId, $doc['estado'], $estado, $observaciones);
        }
        
        return $result;
    }

    /**
     * Registrar cambio en historial
     */
    private function registrarHistorial(int $documentoId, int $userId, int $revisorId, string $estadoAnterior, string $estadoNuevo, ?string $observaciones): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO historial_evaluaciones 
            (documento_id, user_id, revisor_id, estado_anterior, estado_nuevo, observaciones)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$documentoId, $userId, $revisorId, $estadoAnterior, $estadoNuevo, $observaciones]);
    }

    /**
     * Obtener todos los documentos pendientes de revisión
     * 
     * @return array Lista de documentos agrupados por usuario
     */
    public function getPendientes(): array
    {
        $stmt = $this->db->prepare("
            SELECT u.id as user_id,
                   u.name,
                   u.email,
                   u.role,
                   u.documentos_estado,
                   COUNT(d.id) as total_documentos,
                   SUM(CASE WHEN d.estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                   SUM(CASE WHEN d.estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                   SUM(CASE WHEN d.estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados,
                   SUM(CASE WHEN d.estado = 'revision' THEN 1 ELSE 0 END) as en_revision
            FROM users u
            INNER JOIN documentos d ON d.user_id = u.id
            GROUP BY u.id
            HAVING pendientes > 0 OR en_revision > 0
            ORDER BY u.name
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener todos los usuarios con documentos
     * 
     * @return array Lista de usuarios con estadísticas
     */
    public function getAllWithStats(): array
    {
        $stmt = $this->db->prepare("
            SELECT u.id as user_id,
                   u.name,
                   u.email,
                   u.role,
                   u.documentos_estado,
                   COUNT(d.id) as total_documentos,
                   SUM(CASE WHEN d.estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                   SUM(CASE WHEN d.estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                   SUM(CASE WHEN d.estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados,
                   SUM(CASE WHEN d.estado = 'revision' THEN 1 ELSE 0 END) as en_revision
            FROM users u
            LEFT JOIN documentos d ON d.user_id = u.id
            WHERE u.role IN ('cliente', 'proveedor', 'transportista')
            GROUP BY u.id
            ORDER BY u.name
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Eliminar documento
     * 
     * @param int $id ID del documento
     * @return bool True si se eliminó
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM documentos WHERE id = ?");
        return $stmt->execute([$id]);
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
}
