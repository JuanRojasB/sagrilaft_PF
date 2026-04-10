<?php

namespace App\Models;

use PDO;

/**
 * Modelo Vendedor
 * 
 * Gestiona los vendedores (contactos externos, no usuarios del sistema).
 * Los vendedores solo reciben notificaciones por email cuando se les asigna un cliente.
 * 
 * Campos:
 * - id: ID del vendedor
 * - nombre: Nombre completo del vendedor
 * - email: Email del vendedor (único)
 * - telefono: Teléfono de contacto
 * - activo: Estado (1=activo, 0=inactivo)
 * - created_at: Fecha de creación
 * - updated_at: Fecha de última actualización
 * 
 * @package App\Models
 */
class Vendedor
{
    private PDO $db;

    public function __construct()
    {
        $this->db = \App\Core\Database::getConnection();
    }

    /**
     * Obtener todos los vendedores activos
     * 
     * @return array Lista de vendedores
     */
    public function all(): array
    {
        $stmt = $this->db->prepare("
            SELECT v.*,
                   COUNT(u.id) as clientes_count
            FROM vendedores v
            LEFT JOIN users u ON u.vendedor_id = v.id
            WHERE v.activo = 1
            GROUP BY v.id
            ORDER BY v.nombre
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener vendedor por ID
     * 
     * @param int $id ID del vendedor
     * @return array|false Datos del vendedor o false si no existe
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM vendedores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener vendedor por email
     * 
     * @param string $email Email del vendedor
     * @return array|false Datos del vendedor o false si no existe
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM vendedores WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Crear nuevo vendedor
     * 
     * @param array $data Datos del vendedor (nombre, email, telefono)
     * @return int ID del vendedor creado
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO vendedores (nombre, email, telefono, activo)
            VALUES (?, ?, ?, 1)
        ");
        
        $stmt->execute([
            $data['nombre'],
            $data['email'],
            $data['telefono'] ?? null
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar vendedor
     * 
     * @param int $id ID del vendedor
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        
        if (isset($data['nombre'])) {
            $fields[] = 'nombre = ?';
            $values[] = $data['nombre'];
        }
        
        if (isset($data['email'])) {
            $fields[] = 'email = ?';
            $values[] = $data['email'];
        }
        
        if (isset($data['telefono'])) {
            $fields[] = 'telefono = ?';
            $values[] = $data['telefono'];
        }
        
        if (isset($data['activo'])) {
            $fields[] = 'activo = ?';
            $values[] = $data['activo'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE vendedores SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Desactivar vendedor (soft delete)
     * 
     * @param int $id ID del vendedor
     * @return bool True si se desactivó correctamente
     */
    public function deactivate(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE vendedores SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Eliminar vendedor permanentemente
     * 
     * @param int $id ID del vendedor
     * @return bool True si se eliminó correctamente
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM vendedores WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Obtener clientes asignados a un vendedor
     * 
     * @param int $vendedorId ID del vendedor
     * @return array Lista de clientes
     */
    public function getClientes(int $vendedorId): array
    {
        $stmt = $this->db->prepare("
            SELECT u.*,
                   (SELECT COUNT(*) FROM forms WHERE user_id = u.id) as formularios_count
            FROM users u
            WHERE u.vendedor_id = ? AND u.role = 'cliente'
            ORDER BY u.name
        ");
        $stmt->execute([$vendedorId]);
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
}
