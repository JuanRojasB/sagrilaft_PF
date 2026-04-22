<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Services\Logger;

/**
 * Controlador de Firmas Digitales
 * 
 * Maneja la subida y gestión de firmas digitales para usuarios y revisores
 */
class SignatureController extends Controller
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Mostrar interfaz de gestión de firmas
     */
    public function index(): void
    {
        // Verificar autenticación
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['reviewer_id'])) {
            $this->redirect('/login');
        }

        $userId = $_SESSION['user_id'] ?? $_SESSION['reviewer_id'];
        $userType = isset($_SESSION['reviewer_id']) ? 'revisor' : 'usuario';

        // Obtener firma actual
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
        $stmt->execute([$userId]);
        $firmaActual = $stmt->fetch();

        $this->view('signatures/index', [
            'firma_actual' => $firmaActual,
            'user_type' => $userType,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Subir nueva firma digital
     */
    public function upload(): void
    {
        header('Content-Type: application/json');

        // Verificar autenticación
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['reviewer_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }

        // Validar CSRF
        if (!$this->validateCsrf()) {
            echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
            exit;
        }

        if (!isset($_FILES['firma']) || $_FILES['firma']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No se recibió el archivo']);
            exit;
        }

        $file = $_FILES['firma'];
        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PNG o JPG']);
            exit;
        }

        if ($file['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'El archivo no debe superar 2MB']);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'] ?? $_SESSION['reviewer_id'];
            $imageData = file_get_contents($file['tmp_name']);
            $mimeType = $file['type'];

            $db = Database::getConnection();
            
            // Desactivar firmas anteriores del usuario
            $stmt = $db->prepare("UPDATE firmas_digitales SET activa = 0 WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Insertar nueva firma activa
            $stmt = $db->prepare("
                INSERT INTO firmas_digitales (user_id, firma_data, firma_size, mime_type, activa, created_at) 
                VALUES (?, ?, ?, ?, 1, NOW())
            ");
            $stmt->execute([
                $userId,
                $imageData,
                $file['size'],
                $mimeType
            ]);

            $this->logger->info('Firma digital subida', [
                'user_id' => $userId,
                'file_size' => $file['size'],
                'mime_type' => $mimeType
            ]);

            echo json_encode(['success' => true, 'message' => 'Firma guardada correctamente']);
        } catch (\Exception $e) {
            $this->logger->error('Error al guardar firma', ['error' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Error al guardar la firma: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Ver firma actual del usuario
     */
    public function view(): void
    {
        // Verificar autenticación
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['reviewer_id'])) {
            http_response_code(403);
            echo 'Acceso denegado';
            exit;
        }

        $userId = $_SESSION['user_id'] ?? $_SESSION['reviewer_id'];

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT firma_data, mime_type FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
        $stmt->execute([$userId]);
        $firma = $stmt->fetch();

        if (!$firma || empty($firma['firma_data'])) {
            http_response_code(404);
            echo 'Firma no encontrada';
            exit;
        }

        header('Content-Type: ' . $firma['mime_type']);
        header('Content-Length: ' . strlen($firma['firma_data']));
        header('Cache-Control: private, max-age=3600');
        echo $firma['firma_data'];
        exit;
    }

    /**
     * Eliminar firma actual
     */
    public function delete(): void
    {
        header('Content-Type: application/json');

        // Verificar autenticación
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['reviewer_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            exit;
        }

        // Validar CSRF
        if (!$this->validateCsrf()) {
            echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'] ?? $_SESSION['reviewer_id'];

            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM firmas_digitales WHERE user_id = ?");
            $stmt->execute([$userId]);

            $this->logger->info('Firma digital eliminada', ['user_id' => $userId]);

            echo json_encode(['success' => true, 'message' => 'Firma eliminada correctamente']);
        } catch (\Exception $e) {
            $this->logger->error('Error al eliminar firma', ['error' => $e->getMessage()]);
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la firma']);
        }
        exit;
    }
}