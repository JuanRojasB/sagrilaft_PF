<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
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
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">403</div>
        <h1>Acceso Denegado</h1>
        <div class="error-code">Error 403</div>
        <p>No tienes permisos para acceder a esta página. Si crees que esto es un error, por favor contacta al administrador del sistema.</p>
        <a href="/gestion-sagrilaft/public/" class="btn btn-primary">← Volver al Inicio</a>
    </div>
</body>
</html>
