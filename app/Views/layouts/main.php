<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SAGRILAFT' ?></title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/font-scale-enhanced.css">
    <style>
        .app { min-height: 100vh; display: flex; flex-direction: column; }
        .app-header { padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-secondary); }
        .brand { display: flex; align-items: center; gap: 0.75rem; }
        .brand-mark { display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary)); color: #ffffff; font-weight: 700; font-size: 0.95rem; }
        .brand-text h1 { font-size: 1.25rem; }
        .brand-text p { margin: 0.2rem 0 0; font-size: 0.85rem; color: var(--text-muted); }
        .header-actions { display: flex; gap: 0.75rem; align-items: center; }
        .user-info { font-size: 0.85rem; color: var(--text-secondary); }
        .app-main { flex: 1; padding: 1.5rem; max-width: 1200px; width: 100%; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="app">
        <header class="app-header">
            <div class="brand">
                <div class="brand-mark">S</div>
                <div class="brand-text">
                    <h1>SAGRILAFT</h1>
                    <p>Sistema de Gestión</p>
                </div>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="header-actions">
                <span class="user-info"><?= htmlspecialchars($_SESSION['user_email']) ?></span>
                <form method="POST" action="<?= $_ENV['APP_URL'] ?>/logout" style="margin: 0;">
                    <button type="submit" class="btn btn-error">Cerrar Sesión</button>
                </form>
            </div>
            <?php endif; ?>
        </header>

        <main class="app-main">
            <?= $content ?? '' ?>
        </main>
    </div>
</body>
</html>
