<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo de Adjuntos
 * 
 * Gestiona los archivos adjuntos asociados a los formularios.
 * Permite crear, leer y eliminar adjuntos.
 * 
 * @package App\Models
 */
class Attachment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener adjuntos por ID de formulario
     * 
     * @param int $formId ID del formulario
     * @return array Lista de adjuntos del formulario
     */
    public function getByFormId(int $formId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM form_attachments WHERE form_id = ? ORDER BY uploaded_at DESC"
        );
        $stmt->execute([$formId]);
        return $stmt->fetchAll();
    }

    /**
     * Crear nuevo adjunto
     * 
     * @param array $data Datos del adjunto (form_id, filename, filepath, filesize)
     * @return int ID del adjunto creado
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO form_attachments (form_id, filename, filepath, filesize) 
             VALUES (?, ?, ?, ?)"
        );
        
        $stmt->execute([
            $data['form_id'],
            $data['filename'],
            $data['filepath'],
            $data['filesize']
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Buscar adjunto por ID
     * 
     * @param int $id ID del adjunto
     * @return array|false Datos del adjunto o false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM form_attachments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Eliminar adjunto
     * 
     * @param int $id ID del adjunto
     * @return bool true si se eliminó correctamente
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM form_attachments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar adjuntos de un formulario
     * 
     * @param int $formId ID del formulario
     * @return int Cantidad de adjuntos
     */
    public function countByFormId(int $formId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM form_attachments WHERE form_id = ?");
        $stmt->execute([$formId]);
        return (int)$stmt->fetchColumn();
    }
}
