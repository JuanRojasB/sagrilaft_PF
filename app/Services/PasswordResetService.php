<?php

namespace App\Services;

use PDO;

/**
 * Servicio de Recuperación de Contraseña
 * 
 * Gestiona el proceso de recuperación de contraseña mediante código por email.
 * 
 * Flujo:
 * 1. Usuario solicita recuperación con su email
 * 2. Se genera código de 6 dígitos con expiración de 15 minutos
 * 3. Se envía código por email
 * 4. Usuario ingresa código y nueva contraseña
 * 5. Se valida código y se actualiza contraseña
 * 
 * @package App\Services
 */
class PasswordResetService
{
    private PDO $db;
    private Logger $logger;
    private MailService $mailService;

    public function __construct()
    {
        $this->db = \App\Core\Database::getConnection();
        $this->logger = new Logger();
        $this->mailService = new MailService();
    }

    /**
     * Generar código de recuperación y enviarlo por email
     * 
     * @param string $email Email del usuario
     * @return bool True si se envió el código
     */
    public function sendResetCode(string $email): bool
    {
        // Verificar que el email existe
        $stmt = $this->db->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->logger->warning('Password reset requested for non-existent email', ['email' => $email]);
            return false;
        }

        // Generar código de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Calcular expiración (15 minutos)
        $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60));

        // Invalidar códigos anteriores del mismo email
        $stmt = $this->db->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND used = 0");
        $stmt->execute([$email]);

        // Guardar nuevo código
        $stmt = $this->db->prepare("
            INSERT INTO password_resets (email, token, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$email, $code, $expiresAt]);

        // Enviar email con el código
        try {
            
            $body = "
            <html>
            <head>
                <style>
                    body { 
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                        line-height: 1.6; 
                        color: #333; 
                        background: #f5f5f5;
                        margin: 0;
                        padding: 0;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 40px auto; 
                        background: white;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    }
                    .header { 
                        background: linear-gradient(135deg, #1d4ed8, #38bdf8); 
                        color: white; 
                        padding: 40px 30px; 
                        text-align: center; 
                    }
                    .header h1 {
                        margin: 0;
                        font-size: 28px;
                        font-weight: 600;
                    }
                    .content { 
                        padding: 40px 30px; 
                    }
                    .code-box {
                        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
                        border: 2px solid #3b82f6;
                        border-radius: 12px;
                        padding: 30px;
                        text-align: center;
                        margin: 30px 0;
                    }
                    .code {
                        font-size: 48px;
                        font-weight: 700;
                        color: #1d4ed8;
                        letter-spacing: 8px;
                        font-family: 'Courier New', monospace;
                    }
                    .warning {
                        background: #fef3c7;
                        border-left: 4px solid #f59e0b;
                        padding: 15px;
                        margin: 20px 0;
                        border-radius: 4px;
                    }
                    .footer {
                        background: #f9fafb;
                        padding: 20px 30px;
                        text-align: center;
                        color: #6b7280;
                        font-size: 14px;
                    }
                    .button {
                        display: inline-block;
                        padding: 12px 30px;
                        background: linear-gradient(135deg, #1d4ed8, #38bdf8);
                        color: white;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: 600;
                        margin: 20px 0;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🔐 Recuperación de Contraseña</h1>
                        <p style='margin: 10px 0 0; opacity: 0.9;'>SAGRILAFT</p>
                    </div>
                    <div class='content'>
                        <p style='font-size: 16px; margin-bottom: 10px;'>Hola <strong>{$user['name']}</strong>,</p>
                        <p style='color: #6b7280;'>Recibimos una solicitud para restablecer tu contraseña. Usa el siguiente código para continuar:</p>
                        
                        <div class='code-box'>
                            <p style='margin: 0 0 10px; color: #6b7280; font-size: 14px;'>Tu código de verificación es:</p>
                            <div class='code'>{$code}</div>
                            <p style='margin: 15px 0 0; color: #6b7280; font-size: 14px;'>Este código expira en <strong>15 minutos</strong></p>
                        </div>

                        <div class='warning'>
                            <strong>⚠️ Importante:</strong>
                            <ul style='margin: 10px 0 0; padding-left: 20px;'>
                                <li>No compartas este código con nadie</li>
                                <li>Si no solicitaste este cambio, ignora este email</li>
                                <li>El código solo se puede usar una vez</li>
                            </ul>
                        </div>

                        <p style='color: #6b7280; margin-top: 30px;'>Si tienes problemas, contacta a soporte.</p>
                    </div>
                    <div class='footer'>
                        <p style='margin: 0;'>Este es un email automático, por favor no respondas.</p>
                        <p style='margin: 5px 0 0;'>© " . date('Y') . " SAGRILAFT - Todos los derechos reservados</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $this->mailService->sendViaSMTP($email, 'Código de Recuperación de Contraseña - SAGRILAFT', $body);

            $this->logger->info('Password reset code sent', ['email' => $email]);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send password reset email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validar código de recuperación
     * 
     * @param string $email Email del usuario
     * @param string $code Código de 6 dígitos
     * @return bool True si el código es válido
     */
    public function validateCode(string $email, string $code): bool
    {
        $stmt = $this->db->prepare("
            SELECT * FROM password_resets 
            WHERE email = ? 
            AND token = ? 
            AND used = 0 
            AND expires_at > NOW()
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$email, $code]);
        $reset = $stmt->fetch();

        if (!$reset) {
            $this->logger->warning('Invalid or expired password reset code', [
                'email' => $email,
                'code' => $code
            ]);
            return false;
        }

        return true;
    }

    /**
     * Restablecer contraseña con código válido
     * 
     * @param string $email Email del usuario
     * @param string $code Código de 6 dígitos
     * @param string $newPassword Nueva contraseña
     * @return bool True si se actualizó la contraseña
     */
    public function resetPassword(string $email, string $code, string $newPassword): bool
    {
        // Validar código
        if (!$this->validateCode($email, $code)) {
            return false;
        }

        // Actualizar contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        // Marcar código como usado
        $stmt = $this->db->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND token = ?");
        $stmt->execute([$email, $code]);

        $this->logger->info('Password reset successful', ['email' => $email]);

        // Enviar email de confirmación
        try {
            $stmt = $this->db->prepare("SELECT name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #10b981, #34d399); color: white; padding: 30px; border-radius: 8px; text-align: center; }
                    .content { background: #f9fafb; padding: 30px; border-radius: 8px; margin-top: 20px; }
                    .success-icon { font-size: 48px; margin-bottom: 10px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <div class='success-icon'>✓</div>
                        <h2 style='margin: 0;'>Contraseña Actualizada</h2>
                    </div>
                    <div class='content'>
                        <p>Hola <strong>{$user['name']}</strong>,</p>
                        <p>Tu contraseña ha sido actualizada exitosamente.</p>
                        <p>Ya puedes iniciar sesión con tu nueva contraseña.</p>
                        <p style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;'>
                            Si no realizaste este cambio, contacta inmediatamente a soporte.
                        </p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $this->mailService->sendViaSMTP($email, 'Contraseña Actualizada - SAGRILAFT', $body);
        } catch (\Exception $e) {
            $this->logger->warning('Failed to send password reset confirmation', ['error' => $e->getMessage()]);
        }

        return true;
    }

    /**
     * Limpiar códigos expirados (ejecutar periódicamente)
     */
    public function cleanExpiredCodes(): void
    {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE expires_at < NOW() OR used = 1");
        $stmt->execute();
        
        $this->logger->info('Expired password reset codes cleaned');
    }
}
