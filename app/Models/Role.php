<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo de Rol
 * 
 * Gestiona los roles del sistema (admin, revisor, cliente, proveedor).
 * Nota: Actualmente el sistema usa roles directamente en la tabla users,
 * este modelo está disponible para futuras extensiones.
 * 
 * @package App\Models
 */
class Role
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener todos los roles
     * 
     * @return array Lista de todos los roles ordenados por nombre
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM roles ORDER BY name");
        return $stmt->fetchAll();
    }

    /**
     * Buscar rol por ID
     * 
     * @param int $id ID del rol
     * @return array|null Datos del rol o null si no existe
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $role = $stmt->fetch();
        
        return $role ?: null;
    }

    /**
     * Buscar rol por nombre
     * 
     * @param string $name Nombre del rol (admin, revisor, cliente, proveedor)
     * @return array|null Datos del rol o null si no existe
     */
    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $role = $stmt->fetch();
        
        return $role ?: null;
    }
}
