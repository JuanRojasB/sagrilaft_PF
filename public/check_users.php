<?php
/**
 * Script para verificar usuarios en la base de datos
 */

// Cargar variables de entorno
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

require_once __DIR__ . '/../app/Core/Database.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Usuarios</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f1f5f9;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            color: #1e293b;
            font-size: 14px;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .role-revisor {
            background: #dbeafe;
            color: #1e40af;
        }
        .role-admin {
            background: #fce7f3;
            color: #9f1239;
        }
        .role-user {
            background: #f0fdf4;
            color: #166534;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #2563eb;
        }
        .actions {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Usuarios en el Sistema</h1>
        </div>
        
        <div class="content">
            <?php
            try {
                $db = \App\Core\Database::getConnection();
                $stmt = $db->query("SELECT id, email, role, document_type, document_number, created_at FROM users ORDER BY created_at DESC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($users)) {
                    echo "<p>No hay usuarios en la base de datos.</p>";
                } else {
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Documento</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= $user['role'] ?>">
                                        <?= strtoupper($user['role']) ?>
                                    </span>
                                </td>
                                <td><?= $user['document_type'] ?> <?= $user['document_number'] ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php if ($user['role'] !== 'revisor'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="action" value="make_reviewer">
                                        <button type="submit" class="btn">Hacer Revisor</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php
                }
                
                // Procesar acción de hacer revisor
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'make_reviewer') {
                    $userId = (int)$_POST['user_id'];
                    $stmt = $db->prepare("UPDATE users SET role = 'revisor' WHERE id = ?");
                    $stmt->execute([$userId]);
                    echo "<script>alert('Usuario actualizado a revisor'); window.location.reload();</script>";
                }
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
            
            <div class="actions">
                <a href="create_reviewer.php" class="btn">+ Crear Nuevo Revisor</a>
                <a href="<?= $_ENV['APP_URL'] ?? '/' ?>" class="btn" style="background: #64748b;">← Volver</a>
            </div>
        </div>
    </div>
</body>
</html>
