<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo de Revisor
 * 
 * Gestiona las operaciones relacionadas con revisores.
 * Los revisores son usuarios con rol 'revisor' que aprueban/rechazan formularios.
 * 
 * @package App\Models
 */
class Reviewer
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Buscar revisor por email
     * 
     * @param string $email Email del revisor
     * @return array|null Datos del revisor o null si no existe
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM reviewers WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $reviewer = $stmt->fetch();
        
        return $reviewer ?: null;
    }

    /**
     * Verificar credenciales del revisor
     * 
     * @param string $email Email del revisor
     * @param string $password Contraseña del revisor
     * @return array|null Datos del revisor si las credenciales son válidas, null si no
     */
    public function verifyCredentials(string $email, string $password): ?array
    {
        $reviewer = $this->findByEmail($email);
        
        if (!$reviewer) {
            return null;
        }

        if (password_verify($password, $reviewer['password'])) {
            return $reviewer;
        }

        return null;
    }
}
