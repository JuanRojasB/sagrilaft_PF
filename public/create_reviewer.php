<?php
/**
 * Script para crear usuario revisor
 * 
 * Acceso: http://localhost/gestion-sagrilaft/public/create_reviewer.php
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
    <title>Crear Usuario Revisor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px;
        }
        .result {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .result.error {
            background: #fef2f2;
            border-color: #fca5a5;
        }
        .result h3 {
            color: #166534;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .result.error h3 {
            color: #991b1b;
        }
        .result pre {
            background: white;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 13px;
            color: #1e293b;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
            text-align: center;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .info {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👤 Crear Usuario Revisor</h1>
            <p>Sistema SAGRILAFT</p>
        </div>
        
        <div class="content">
            <?php
            try {
                $db = \App\Core\Database::getConnection();
                
                // Verificar si ya existe el revisor
                $stmt = $db->prepare("SELECT id, email, role FROM users WHERE email = ?");
                $stmt->execute(['juan.david.rojas.burbano0@gmail.com']);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    // Si existe pero no es revisor, actualizar
                    if ($existing['role'] !== 'revisor') {
                        $stmt = $db->prepare("UPDATE users SET role = 'revisor' WHERE id = ?");
                        $stmt->execute([$existing['id']]);
                        ?>
                        <div class="result">
                            <h3>✅ Usuario actualizado a revisor</h3>
                            <pre><?php
                            echo "Email: juan.david.rojas.burbano0@gmail.com\n";
                            echo "Password: password\n";
                            echo "Rol: revisor\n";
                            echo "\nEl usuario ya existía y fue actualizado al rol de revisor.";
                            ?></pre>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="info">
                            ℹ️ El usuario revisor ya existe en la base de datos.
                        </div>
                        <div class="result">
                            <h3>✅ Usuario revisor existente</h3>
                            <pre><?php
                            echo "Email: juan.david.rojas.burbano0@gmail.com\n";
                            echo "Password: password\n";
                            echo "Rol: revisor\n";
                            echo "\nPuedes usar estas credenciales para acceder.";
                            ?></pre>
                        </div>
                        <?php
                    }
                } else {
                    // Crear nuevo usuario revisor
                    $stmt = $db->prepare("
                        INSERT INTO users (email, password, role, document_type, document_number, phone, created_at)
                        VALUES (?, ?, 'revisor', 'CC', '1234567890', '3001234567', NOW())
                    ");
                    $stmt->execute([
                        'juan.david.rojas.burbano0@gmail.com',
                        password_hash('password', PASSWORD_DEFAULT)
                    ]);
                    ?>
                    <div class="result">
                        <h3>✅ Usuario revisor creado exitosamente</h3>
                        <pre><?php
                        echo "Email: juan.david.rojas.burbano0@gmail.com\n";
                        echo "Password: password\n";
                        echo "Rol: revisor\n";
                        echo "\nAhora puedes acceder al sistema con estas credenciales.";
                        ?></pre>
                    </div>
                    <?php
                }
                
                $appUrl = $_ENV['APP_URL'] ?? 'http://localhost/gestion-sagrilaft/public';
                ?>
                
                <a href="<?= $appUrl ?>/reviewer/login" class="btn">
                    🔐 Ir al Login de Revisor
                </a>
                
                <?php
            } catch (Exception $e) {
                ?>
                <div class="result error">
                    <h3>❌ Error</h3>
                    <pre><?= htmlspecialchars($e->getMessage()) ?></pre>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</body>
</html>
