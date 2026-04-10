<!--
    Vista: Login de Revisor
    
    Descripción:
    - Pantalla de inicio de sesión exclusiva para revisores
    - Valida credenciales y redirige al dashboard de revisor
    - Solo usuarios con rol 'revisor' pueden acceder
    
    Diferencias con login normal:
    - No requiere seleccionar tipo de usuario
    - Redirige a dashboard de revisor en lugar de formularios
    - Valida que el usuario tenga rol 'revisor'
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Revisor - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            font-family: sans-serif;
            background: #ffffff;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-page {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
        }
        
        .auth-card {
            max-width: 420px;
            width: 100%;
            margin: 1.5rem auto 2.5rem;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 1rem;
            padding: 1.75rem 1.75rem 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #0f172a;
        }
        
        .auth-illustration {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 0.85rem;
        }
        
        .auth-illustration-img {
            width: 140px;
            height: auto;
            display: block;
            filter: drop-shadow(0 12px 22px rgba(56, 189, 248, 0.25));
            opacity: 0.95;
        }
        
        .auth-title {
            margin: 0 0 0.35rem;
            font-size: 1.3rem;
            text-align: center;
            color: #0f172a;
        }
        
        .auth-subtitle {
            margin: 0 0 1.1rem;
            font-size: 0.9rem;
            color: #475569;
            text-align: center;
        }
        
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
        
        .form-field {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .form-field label {
            font-size: 0.85rem;
            color: #1e293b;
        }
        
        .form-field input {
            border-radius: 0.7rem;
            border: 1px solid rgba(71, 85, 105, 0.4);
            background: rgba(255, 255, 255, 0.95);
            color: #0f172a;
            padding: 0.6rem 0.75rem;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        
        .form-field input::placeholder {
            color: #94a3b8;
        }
        
        .form-field input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.4);
            background: #ffffff;
        }
        
        .auth-actions {
            margin-top: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .primary-button {
            border: none;
            border-radius: 999px;
            padding: 0.65rem 1rem;
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, #1d4ed8, #38bdf8);
            color: #ffffff;
            box-shadow: 0 10px 15px -3px rgba(30, 64, 175, 0.4), 0 4px 6px -4px rgba(30, 64, 175, 0.3);
            transition: transform 0.1s ease, box-shadow 0.1s ease, filter 0.1s ease;
        }
        
        .primary-button:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }
        
        .primary-button:active {
            transform: translateY(0);
            box-shadow: 0 5px 10px -3px rgba(59, 130, 246, 0.3), 0 2px 4px -4px rgba(59, 130, 246, 0.2);
        }
        
        .primary-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            padding: 0.875rem;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 0.5rem;
            text-align: center;
            margin-top: 1rem;
        }
        
        .status-badge {
            padding: 0.875rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-align: center;
            margin-top: 1rem;
        }
        
        .status-active {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-illustration">
            <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=5" alt="SAGRILAFT" class="auth-illustration-img">
        </div>
        
        <h2 class="auth-title">Revisor SAGRILAFT</h2>
        
        <form id="loginForm" class="auth-form" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="form-field">
                <label for="email">Usuario</label>
                <input type="text" id="email" name="email" required 
                       placeholder="juan.david.rojas.burbano0@gmail.com" autocomplete="username">
            </div>
            
            <div class="form-field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required 
                       placeholder="••••••••" autocomplete="current-password">
            </div>
            
            <div class="auth-actions">
                <button type="submit" class="primary-button">Iniciar Sesión</button>
            </div>
        </form>
        
        <div id="message" style="margin-top: 1rem;"></div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const messageDiv = document.getElementById('message');
            const button = e.target.querySelector('button[type="submit"]');
            
            button.disabled = true;
            button.textContent = 'Iniciando...';
            
            try {
                const response = await fetch('/gestion-sagrilaft/public/reviewer/login', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.className = 'status-badge status-active';
                    messageDiv.textContent = data.message;
                    setTimeout(() => window.location.href = data.redirect, 1000);
                } else {
                    messageDiv.className = 'error-message';
                    messageDiv.textContent = data.error;
                    button.disabled = false;
                    button.textContent = 'Iniciar Sesión';
                }
            } catch (error) {
                messageDiv.className = 'error-message';
                messageDiv.textContent = 'Error de conexión';
                button.disabled = false;
                button.textContent = 'Iniciar Sesión';
            }
        });
    </script>
</body>
</html>
