<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo de Usuario
 * 
 * Gestiona todas las operaciones relacionadas con usuarios en la base de datos.
 * Incluye funciones para crear, leer, actualizar y eliminar usuarios.
 * 
 * @package App\Models
 */
class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Buscar usuario por email
     * 
     * @param string $email Email del usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    /**
     * Buscar usuario por ID
     * 
     * @param int $id ID del usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    /**
     * Crear nuevo usuario
     * 
     * @param array $data Datos del usuario (name, email, password, role, document_type, document_number, etc.)
     * @return int ID del usuario creado
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, document_type, document_number, 
             person_type, phone, address, city, company_name, logistics_status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'], // Already hashed in controller
            $data['role'] ?? 'cliente',
            $data['document_type'] ?? 'cedula',
            $data['document_number'] ?? null,
            $data['person_type'] ?? 'natural',
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['city'] ?? null,
            $data['company_name'] ?? null,
            $data['logistics_status'] ?? 'pending'
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Buscar usuario por documento
     * 
     * @param string $documentType Tipo de documento (cedula, nit, pasaporte)
     * @param string $documentNumber Número de documento
     * @return array|null Datos del usuario o null si no existe
     */
    public function findByDocument(string $documentType, string $documentNumber): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE document_type = ? AND document_number = ? LIMIT 1"
        );
        $stmt->execute([$documentType, $documentNumber]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    /**
     * Actualizar usuario
     * 
     * @param int $id ID del usuario
     * @param array $data Datos a actualizar
     * @return bool true si se actualizó correctamente
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Eliminar usuario
     * 
     * @param int $id ID del usuario
     * @return bool true si se eliminó correctamente
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Obtener todos los usuarios
     * 
     * @return array Lista de todos los usuarios ordenados por fecha de creación
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
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
