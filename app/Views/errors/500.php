<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Servidor - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/font-scale-enhanced.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: var(--bg-card);
            border: 1px solid var(--error-border);
            border-radius: var(--radius-lg);
            padding: 48px 40px;
            max-width: 500px;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }
        .error-icon {
            font-size: 64px;
            margin-bottom: 24px;
            filter: grayscale(1);
        }
        h1 {
            color: var(--error-light);
            font-size: 28px;
            margin-bottom: 12px;
            font-weight: 700;
        }
        .error-code {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 24px;
            font-weight: 600;
        }
        p {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">500</div>
        <h1>Error del Servidor</h1>
        <div class="error-code">Error 500</div>
        <p>Ocurrió un error inesperado en el servidor. Por favor intenta de nuevo más tarde o contacta al administrador si el problema persiste.</p>
        <div class="buttons">
            <button onclick="location.reload()" class="btn btn-secondary">Reintentar</button>
            <a href="/gestion-sagrilaft/public/" class="btn btn-primary">← Volver al Inicio</a>
        </div>
    </div>
</body>
</html>
