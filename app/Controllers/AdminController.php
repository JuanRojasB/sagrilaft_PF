<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\Logger;

/**
 * Controlador de Administración
 * 
 * Gestiona el panel de administración y la gestión de usuarios.
 * Solo accesible para usuarios con rol 'admin'.
 * 
 * Funciones principales:
 * - index: Dashboard de administración
 * - users: Listar todos los usuarios
 * - createUser: Crear nuevo usuario
 * - updateUser: Actualizar usuario existente
 * - deleteUser: Eliminar usuario
 * 
 * Nota: Al editar el propio usuario, actualiza la sesión automáticamente.
 * 
 * @package App\Controllers
 */
class AdminController extends Controller
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Show admin dashboard
     */
    public function index(): void
    {
        $this->view('admin/dashboard');
    }

    /**
     * Show users management
     */
    public function users(): void
    {
        $userModel = new User();
        $users = $userModel->all();
        
        $this->view('admin/users', [
            'users' => $users,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Crear nuevo usuario
     */
    public function createUser(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $name = $this->input('name');
        $email = $this->input('email');
        $password = $this->input('password');
        $role = $this->input('role', 'cliente');

        if (!$name || !$email || !$password) {
            $this->json(['error' => 'Todos los campos son requeridos'], 400);
        }

        if (strlen($password) < 6) {
            $this->json(['error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
        }

        $userModel = new User();
        
        // Check if email already exists
        if ($userModel->findByEmail($email)) {
            $this->json(['error' => 'El email ya está registrado'], 400);
        }

        try {
            $userId = $userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => $role,
                'document_type' => $this->input('document_type', 'cedula'),
                'document_number' => $this->input('document_number'),
                'person_type' => $this->input('person_type', 'natural'),
                'phone' => $this->input('phone'),
                'address' => $this->input('address'),
                'city' => $this->input('city'),
                'company_name' => $this->input('company_name'),
                'logistics_status' => 'pending'
            ]);

            $this->logger->info('User created by admin', [
                'user_id' => $userId,
                'email' => $email,
                'role' => $role
            ]);

            $this->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('User creation failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al crear usuario'], 500);
        }
    }

    /**
     * Update user
     */
    public function updateUser(string $id): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $name = $this->input('name');
        $email = $this->input('email');
        $password = $this->input('password');
        $role = $this->input('role', 'cliente');

        if (!$name || !$email) {
            $this->json(['error' => 'Nombre y email son requeridos'], 400);
        }

        if ($password && strlen($password) < 6) {
            $this->json(['error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
        }

        $userModel = new User();
        $user = $userModel->findById((int)$id);

        if (!$user) {
            $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Check if email is taken by another user
        $existingUser = $userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $id) {
            $this->json(['error' => 'El email ya está registrado'], 400);
        }

        try {
            $data = [
                'name' => $name,
                'email' => $email,
                'role' => $role
            ];

            if ($password) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $userModel->update((int)$id, $data);

            // Si el usuario editado es el mismo que está logueado, actualizar la sesión
            if ($_SESSION['user_id'] == $id) {
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;
            }

            $this->logger->info('User updated by admin', [
                'user_id' => $id,
                'email' => $email
            ]);

            $this->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('User update failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al actualizar usuario'], 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(string $id): void
    {
        // Don't allow deleting yourself
        if ($_SESSION['user_id'] == $id) {
            $this->json(['error' => 'No puedes eliminar tu propio usuario'], 400);
        }

        $userModel = new User();
        $user = $userModel->findById((int)$id);

        if (!$user) {
            $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        try {
            $userModel->delete((int)$id);

            $this->logger->info('User deleted by admin', [
                'user_id' => $id,
                'email' => $user['email']
            ]);

            $this->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('User deletion failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al eliminar usuario'], 500);
        }
    }

    /**
     * Mostrar panel de evaluación logística
     * 
     * Lista todos los proveedores y transportistas para evaluación.
     */
    public function logistics(): void
    {
        $userModel = new User();
        
        // Obtener solo proveedores y transportistas
        $stmt = $userModel->getConnection()->prepare(
            "SELECT * FROM users WHERE role IN ('proveedor', 'transportista') ORDER BY logistics_status ASC, created_at DESC"
        );
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        $this->view('admin/logistics', [
            'users' => $users,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Evaluar estado logístico de un usuario
     * 
     * Actualiza el estado (pending, apta, no_apta) y observaciones.
     */
    public function evaluateLogistics(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $userId = $this->input('user_id');
        $status = $this->input('logistics_status');
        $notes = $this->input('logistics_notes');

        if (!$userId || !$status) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }

        if (!in_array($status, ['pending', 'apta', 'no_apta'])) {
            $this->json(['error' => 'Estado inválido'], 400);
        }

        $userModel = new User();
        $user = $userModel->findById((int)$userId);

        if (!$user) {
            $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        if (!in_array($user['role'], ['proveedor', 'transportista'])) {
            $this->json(['error' => 'Solo se pueden evaluar proveedores y transportistas'], 400);
        }

        try {
            $userModel->update((int)$userId, [
                'logistics_status' => $status,
                'logistics_notes' => $notes
            ]);

            $this->logger->info('Logistics evaluation updated', [
                'user_id' => $userId,
                'status' => $status,
                'evaluated_by' => $_SESSION['user_id']
            ]);

            // Enviar notificación por email al usuario
            try {
                $mailService = new \App\Services\MailService();
                $statusText = $status === 'apta' ? 'APTA' : ($status === 'no_apta' ? 'NO APTA' : 'PENDIENTE');
                $statusColor = $status === 'apta' ? '#10b981' : ($status === 'no_apta' ? '#ef4444' : '#f59e0b');
                
                // Obtener imagen de firma embebida
                $signatureImage = \App\Helpers\EmailHelper::getSignatureImage();
                
                $body = \App\Helpers\EmailHelper::emailHeader('Evaluación Logística SAGRILAFT') . "
                    <div class='badge' style='background:rgba(16,185,129,0.15);color:#4ade80;border:1px solid rgba(16,185,129,0.35);padding:7px 16px;border-radius:4px;font-weight:700;font-size:12px;display:inline-block;margin-bottom:16px;'>{$statusText}</div>
                    <p class='msg'>Estimado/a <strong style='color:#e2e8f0;'>{$user['name']}</strong>, tu evaluación logística ha sido actualizada por Pollo Fiesta S.A.</p>
                    <div class='info-item' style='margin-bottom:8px;'>
                        <span class='info-label'>Resultado</span>
                        <span class='info-value'>{$statusText}</span>
                    </div>
                    " . (!empty($notes) ? "
                    <div class='obs-box'>
                        <strong>Observaciones</strong>
                        <p>" . nl2br(htmlspecialchars($notes)) . "</p>
                    </div>" : "") . "
                    <p style='font-size:12px;color:#475569;margin-top:16px;'>Puedes ver más detalles en tu perfil del sistema.</p>
                " . \App\Helpers\EmailHelper::emailFooter();
                
                $imgs = [];
                $lp = \App\Helpers\EmailHelper::getLogoImagePath(); if ($lp) $imgs['logo'] = $lp;
                $sp = \App\Helpers\EmailHelper::getSignatureImagePath(); if ($sp) $imgs['signature'] = $sp;
                $mailService->sendViaSMTPWithImages($user['email'], 'SAGRILAFT - Evaluación Logística', $body, $imgs);
            } catch (\Exception $e) {
                $this->logger->warning('Logistics notification email failed', ['error' => $e->getMessage()]);
            }

            $this->json([
                'success' => true,
                'message' => 'Evaluación guardada exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Logistics evaluation failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al guardar evaluación'], 500);
        }
    }

    /**
     * Descargar documento de un usuario (admin)
     * 
     * Permite a los administradores descargar documentos de proveedores/transportistas.
     */
    public function downloadUserDocument(string $userId, string $type): void
    {
        $userModel = new User();
        $user = $userModel->findById((int)$userId);

        if (!$user) {
            http_response_code(404);
            echo 'Usuario no encontrado';
            return;
        }

        $filepath = null;
        $filename = null;

        if ($type === 'rut' && !empty($user['rut_file'])) {
            $filepath = __DIR__ . '/../../' . $user['rut_file'];
            $filename = 'RUT_' . $user['name'] . '_' . $user['document_number'] . '.' . pathinfo($filepath, PATHINFO_EXTENSION);
        } elseif ($type === 'chamber' && !empty($user['chamber_commerce_file'])) {
            $filepath = __DIR__ . '/../../' . $user['chamber_commerce_file'];
            $filename = 'CamaraComercio_' . $user['name'] . '_' . $user['document_number'] . '.' . pathinfo($filepath, PATHINFO_EXTENSION);
        }

        if (!$filepath || !file_exists($filepath)) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        // Determinar tipo MIME
        $mimeType = mime_content_type($filepath);

        // Enviar archivo
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    /**
     * Mostrar panel de gestión de vendedores
     * 
     * Lista vendedores (contactos), clientes y permite asignar vendedores a clientes.
     */
    public function vendedores(): void
    {
        $vendedorModel = new \App\Models\Vendedor();
        $userModel = new User();
        $db = $userModel->getConnection();
        
        // Obtener vendedores con conteo de clientes
        $vendedores = $vendedorModel->all();
        
        // Obtener clientes con información de vendedor
        $stmt = $db->prepare("
            SELECT c.*, v.nombre as vendedor_name
            FROM users c
            LEFT JOIN vendedores v ON c.vendedor_id = v.id
            WHERE c.role = 'cliente'
            ORDER BY c.name
        ");
        $stmt->execute();
        $clientes = $stmt->fetchAll();
        
        // Obtener historial de asignaciones
        $stmt = $db->prepare("
            SELECT h.*, 
                   c.name as cliente_name,
                   v.nombre as vendedor_name
            FROM vendedor_cliente_history h
            INNER JOIN users c ON h.cliente_id = c.id
            INNER JOIN vendedores v ON h.vendedor_id = v.id
            ORDER BY h.fecha_asignacion DESC
            LIMIT 50
        ");
        $stmt->execute();
        $historial = $stmt->fetchAll();
        
        $this->view('admin/vendedores', [
            'vendedores' => $vendedores,
            'clientes' => $clientes,
            'historial' => $historial,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Asignar vendedor a cliente
     * 
     * Crea la relación entre vendedor (contacto) y cliente y guarda en historial.
     * Envía notificación por email al vendedor y al cliente.
     */
    public function asignarVendedor(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $clienteId = $this->input('cliente_id');
        $vendedorId = $this->input('vendedor_id');

        if (!$clienteId || !$vendedorId) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }

        $userModel = new User();
        $vendedorModel = new \App\Models\Vendedor();
        $db = $userModel->getConnection();
        
        $cliente = $userModel->findById((int)$clienteId);
        $vendedor = $vendedorModel->findById((int)$vendedorId);

        if (!$cliente || !$vendedor) {
            $this->json(['error' => 'Cliente o vendedor no encontrado'], 404);
        }

        if ($cliente['role'] !== 'cliente') {
            $this->json(['error' => 'Solo se pueden asignar vendedores a clientes'], 400);
        }

        try {
            // Si ya tenía un vendedor, cerrar la asignación anterior en el historial
            if (!empty($cliente['vendedor_id'])) {
                $stmt = $db->prepare("
                    UPDATE vendedor_cliente_history 
                    SET fecha_desasignacion = NOW(),
                        motivo = 'Cambio de vendedor'
                    WHERE cliente_id = ? 
                    AND vendedor_id = ?
                    AND fecha_desasignacion IS NULL
                ");
                $stmt->execute([$clienteId, $cliente['vendedor_id']]);
            }

            // Asignar nuevo vendedor
            $userModel->update((int)$clienteId, [
                'vendedor_id' => $vendedorId
            ]);

            // Registrar en historial
            $stmt = $db->prepare("
                INSERT INTO vendedor_cliente_history 
                (cliente_id, vendedor_id, asignado_por, fecha_asignacion)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$clienteId, $vendedorId, $_SESSION['user_id']]);

            $this->logger->info('Vendedor asignado a cliente', [
                'cliente_id' => $clienteId,
                'vendedor_id' => $vendedorId,
                'asignado_por' => $_SESSION['user_id']
            ]);

            // Enviar notificación por email al cliente
            try {
                $mailService = new \App\Services\MailService();
                
                // Obtener imagen de firma embebida
                $signatureImage = \App\Helpers\EmailHelper::getSignatureImage();
                
                // Email al cliente
                $bodyCliente = \App\Helpers\EmailHelper::emailHeader('Vendedor Asignado — Pollo Fiesta S.A.') . "
                    <div class='badge badge-blue' style='background:rgba(59,130,246,0.15);color:#60a5fa;border:1px solid rgba(59,130,246,0.35);padding:7px 16px;border-radius:4px;font-weight:700;font-size:12px;display:inline-block;margin-bottom:16px;'>Vendedor Asignado</div>
                    <p class='msg'>Estimado/a <strong style='color:#e2e8f0;'>{$cliente['name']}</strong>, se te ha asignado un vendedor en el sistema SAGRILAFT de Pollo Fiesta S.A.</p>
                    <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                        <tr>
                            <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
                                <div class='info-item'><span class='info-label'>Vendedor</span><span class='info-value'>{$vendedor['nombre']}</span></div>
                            </td>
                            <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
                                <div class='info-item'><span class='info-label'>Email</span><span class='info-value'>{$vendedor['email']}</span></div>
                            </td>
                        </tr>
                        " . (!empty($vendedor['telefono']) ? "
                        <tr>
                            <td colspan='2' style='padding:0 0 6px 0;'>
                                <div class='info-item'><span class='info-label'>Teléfono</span><span class='info-value'>{$vendedor['telefono']}</span></div>
                            </td>
                        </tr>" : "") . "
                    </table>
                    <p style='font-size:13px;color:#94a3b8;margin-top:12px;'>Tu vendedor estará disponible para atender tus consultas y gestionar tus solicitudes.</p>
                " . \App\Helpers\EmailHelper::emailFooter();
                
                $imgs = [];
                $lp = \App\Helpers\EmailHelper::getLogoImagePath(); if ($lp) $imgs['logo'] = $lp;
                $sp = \App\Helpers\EmailHelper::getSignatureImagePath(); if ($sp) $imgs['signature'] = $sp;
                $mailService->sendViaSMTPWithImages($cliente['email'], 'SAGRILAFT - Vendedor Asignado', $bodyCliente, $imgs);
                
                // Email al vendedor
                $bodyVendedor = \App\Helpers\EmailHelper::emailHeader('Nuevo Cliente Asignado — Pollo Fiesta S.A.') . "
                    <div class='badge badge-blue' style='background:rgba(59,130,246,0.15);color:#60a5fa;border:1px solid rgba(59,130,246,0.35);padding:7px 16px;border-radius:4px;font-weight:700;font-size:12px;display:inline-block;margin-bottom:16px;'>Nuevo Cliente</div>
                    <p class='msg'>Hola <strong style='color:#e2e8f0;'>{$vendedor['nombre']}</strong>, se te ha asignado un nuevo cliente en el sistema SAGRILAFT de Pollo Fiesta S.A.</p>
                    <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                        <tr>
                            <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
                                <div class='info-item'><span class='info-label'>Cliente</span><span class='info-value'>{$cliente['name']}</span></div>
                            </td>
                            <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
                                <div class='info-item'><span class='info-label'>Email</span><span class='info-value'>{$cliente['email']}</span></div>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:0 6px 6px 0;width:50%;vertical-align:top;'>
                                " . (!empty($cliente['phone']) ? "<div class='info-item'><span class='info-label'>Teléfono</span><span class='info-value'>{$cliente['phone']}</span></div>" : "") . "
                            </td>
                            <td style='padding:0 0 6px 0;width:50%;vertical-align:top;'>
                                " . (!empty($cliente['company_name']) ? "<div class='info-item'><span class='info-label'>Empresa</span><span class='info-value'>{$cliente['company_name']}</span></div>" : "") . "
                            </td>
                        </tr>
                    </table>
                    <p style='font-size:13px;color:#94a3b8;margin-top:12px;'>Por favor, ponte en contacto con el cliente para iniciar la gestión.</p>
                " . \App\Helpers\EmailHelper::emailFooter();
                
                $imgs2 = [];
                $lp2 = \App\Helpers\EmailHelper::getLogoImagePath(); if ($lp2) $imgs2['logo'] = $lp2;
                $sp2 = \App\Helpers\EmailHelper::getSignatureImagePath(); if ($sp2) $imgs2['signature'] = $sp2;
                $mailService->sendViaSMTPWithImages($vendedor['email'], 'SAGRILAFT - Nuevo Cliente Asignado', $bodyVendedor, $imgs2);
            } catch (\Exception $e) {
                $this->logger->warning('Vendor assignment notification failed', ['error' => $e->getMessage()]);
            }

            $this->json([
                'success' => true,
                'message' => 'Vendedor asignado exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Vendor assignment failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al asignar vendedor'], 500);
        }
    }

    /**
     * Crear nuevo vendedor (contacto)
     */
    public function createVendedor(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $nombre = $this->input('nombre');
        $email = $this->input('email');
        $telefono = $this->input('telefono');

        if (!$nombre || !$email) {
            $this->json(['error' => 'Nombre y email son requeridos'], 400);
        }

        $vendedorModel = new \App\Models\Vendedor();
        
        // Verificar si el email ya existe
        if ($vendedorModel->findByEmail($email)) {
            $this->json(['error' => 'El email ya está registrado'], 400);
        }

        try {
            $vendedorId = $vendedorModel->create([
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono
            ]);

            $this->logger->info('Vendedor created', [
                'vendedor_id' => $vendedorId,
                'email' => $email
            ]);

            $this->json([
                'success' => true,
                'message' => 'Vendedor creado exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Vendedor creation failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al crear vendedor'], 500);
        }
    }

    /**
     * Actualizar vendedor (contacto)
     */
    public function updateVendedor(string $id): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        $nombre = $this->input('nombre');
        $email = $this->input('email');
        $telefono = $this->input('telefono');

        if (!$nombre || !$email) {
            $this->json(['error' => 'Nombre y email son requeridos'], 400);
        }

        $vendedorModel = new \App\Models\Vendedor();
        $vendedor = $vendedorModel->findById((int)$id);

        if (!$vendedor) {
            $this->json(['error' => 'Vendedor no encontrado'], 404);
        }

        // Verificar si el email está tomado por otro vendedor
        $existingVendedor = $vendedorModel->findByEmail($email);
        if ($existingVendedor && $existingVendedor['id'] != $id) {
            $this->json(['error' => 'El email ya está registrado'], 400);
        }

        try {
            $vendedorModel->update((int)$id, [
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono
            ]);

            $this->logger->info('Vendedor updated', [
                'vendedor_id' => $id,
                'email' => $email
            ]);

            $this->json([
                'success' => true,
                'message' => 'Vendedor actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Vendedor update failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al actualizar vendedor'], 500);
        }
    }

    /**
     * Eliminar vendedor (contacto)
     */
    public function deleteVendedor(string $id): void
    {
        $vendedorModel = new \App\Models\Vendedor();
        $vendedor = $vendedorModel->findById((int)$id);

        if (!$vendedor) {
            $this->json(['error' => 'Vendedor no encontrado'], 404);
        }

        // Verificar si tiene clientes asignados
        $clientes = $vendedorModel->getClientes((int)$id);
        if (!empty($clientes)) {
            $this->json(['error' => 'No se puede eliminar un vendedor con clientes asignados'], 400);
        }

        try {
            $vendedorModel->delete((int)$id);

            $this->logger->info('Vendedor deleted', [
                'vendedor_id' => $id,
                'email' => $vendedor['email']
            ]);

            $this->json([
                'success' => true,
                'message' => 'Vendedor eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Vendedor deletion failed', ['error' => $e->getMessage()]);
            $this->json(['error' => 'Error al eliminar vendedor'], 500);
        }
    }

    /**
     * Mostrar vista de sincronización con Excel
     */
    public function excelSync(): void
    {
        $this->view('admin/excel_sync');
    }
}
