<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Enviado - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; padding: var(--spacing-lg); min-height: 100vh; }
        .container { background: var(--bg-card); border: 1px solid var(--border-accent); border-radius: var(--radius-lg); padding: 3rem 2.5rem; max-width: 520px; width: 100%; text-align: center; box-shadow: var(--shadow-lg); }
        .logo { width: 56px; height: 56px; border-radius: 50%; object-fit: contain; margin: 0 auto 1.25rem; }
        h1 { color: var(--text-primary); font-size: 1.625rem; margin-bottom: 1rem; font-weight: 700; letter-spacing: -0.5px; }
        .status { display: inline-block; padding: 0.375rem 1rem; background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: var(--radius-full); font-size: var(--text-sm); font-weight: 600; margin-bottom: 1.25rem; text-transform: uppercase; letter-spacing: 0.5px; }
        p { color: var(--text-secondary); font-size: var(--text-base); line-height: 1.6; margin-bottom: 0.75rem; }
        .note { color: var(--text-muted); font-size: var(--text-sm); margin-bottom: 2rem; font-style: italic; }
        .btn-primary { display: inline-block; padding: 0.75rem 2rem; background: linear-gradient(135deg, #1d4ed8, #38bdf8); color: #ffffff !important; border-radius: var(--radius-full); font-weight: 600; font-size: var(--text-base); text-decoration: none; transition: filter 0.15s ease; }
        .btn-primary:hover { filter: brightness(1.1); }
    </style>
</head>
<body>
    <div class="container">
        <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=4" alt="Logo" class="logo">
        <h1>Formulario Enviado</h1>
        <div class="status">En Revisión</div>
        <p>Tu formulario ha sido recibido correctamente y está siendo revisado por nuestro equipo.</p>
        <p class="note">Recibirás una notificación por email con el resultado.</p>
        <a href="<?= $_ENV['APP_URL'] ?>" class="btn btn-primary">Enviar Otro Formulario</a>
    </div>
</body>
</html>
