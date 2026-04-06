<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo: Asesor Comercial
 * 
 * Gestiona los asesores comerciales y sus jefes
 */
class AsesorComercial
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener todos los asesores activos
     * 
     * @return array
     */
    public function getAllActive(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM asesores_comerciales 
            WHERE activo = TRUE 
            ORDER BY nombre_completo ASC
        ");
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtener asesores agrupados por sede
     * 
     * @return array
     */
    public function getAllGroupedBySede(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM asesores_comerciales 
            WHERE activo = TRUE 
            ORDER BY sede ASC, nombre_completo ASC
        ");
        
        $asesores = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Agrupar por sede
        $grouped = [];
        foreach ($asesores as $asesor) {
            $sede = $asesor['sede'] ?? 'Sin sede';
            if (!isset($grouped[$sede])) {
                $grouped[$sede] = [];
            }
            $grouped[$sede][] = $asesor;
        }
        
        return $grouped;
    }

    /**
     * Obtener asesor por ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM asesores_comerciales 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtener asesor por cédula
     * 
     * @param string $cc
     * @return array|null
     */
    public function findByCC(string $cc): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM asesores_comerciales 
            WHERE empleado_cc = ?
        ");
        $stmt->execute([$cc]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtener información del jefe de un asesor
     * 
     * @param int $asesorId
     * @return array|null
     */
    public function getJefeInfo(int $asesorId): ?array
    {
        $asesor = $this->findById($asesorId);
        
        if (!$asesor || empty($asesor['jefe_email'])) {
            return null;
        }
        
        return [
            'nombre' => $asesor['jefe_nombre'],
            'email' => $asesor['jefe_email']
        ];
    }
}
