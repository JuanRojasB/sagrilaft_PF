<!--
    Vista: Dashboard de Administrador
    
    Descripción:
    Panel principal del administrador con acceso a las funcionalidades administrativas.
    
    Funcionalidades:
    - Acceso a gestión de usuarios
    - Acceso a todos los formularios del sistema
    - Navegación rápida a secciones principales
    
    Acceso:
    Solo usuarios con role='admin'
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
</head>
<body>
    <div class="app">
        <!-- Header con navegación -->
        <header class="app-header" style="padding: 0.75rem 1.25rem; background: var(--bg-card); border-bottom: 1px solid var(--border-primary);">
            <div class="brand" style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=4" alt="Logo" style="width: 32px; height: 32px;">
                <h1 style="font-size: 1.1rem; margin: 0; color: var(--text-primary);">SAGRILAFT - Admin</h1>
            </div>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 0.75rem;">
                <a href="/gestion-sagrilaft/public/forms" style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-primary); padding: 0.4rem 0.75rem; border-radius: var(--radius-sm); text-decoration: none; font-weight: 600; font-size: 0.8rem; transition: all var(--transition-fast); display: flex; align-items: center; gap: 0.3rem; white-space: nowrap;">
                    <span style="filter: grayscale(1);">←</span>
                    <span>Formularios</span>
                </a>
                <form method="POST" action="<?= $_ENV['APP_URL'] ?>/logout" style="margin: 0;">
                    <button type="submit" style="background: var(--error-bg); color: var(--error-light); border: 1px solid var(--error-border); padding: 0.4rem 0.75rem; border-radius: var(--radius-sm); cursor: pointer; font-weight: 600; font-size: 0.8rem; transition: all var(--transition-fast); white-space: nowrap;">Salir</button>
                </form>
            </div>
            
            <style>
                .header-actions a:hover {
                    background: var(--bg-card-hover) !important;
                    border-color: var(--border-focus) !important;
                }
                .header-actions button:hover {
                    background: rgba(239, 68, 68, 0.3) !important;
                    border-color: rgba(239, 68, 68, 0.6) !important;
                }
            </style>
        </header>

        <main class="app-main" style="padding: 0;">
            <div style="max-width: 100%; margin: 0; padding: 1rem;">
                <h2 style="margin: 0 0 1rem; font-size: 1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Panel de Administración</h2>

                <!-- Grid de opciones - 3 columnas -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <a href="/gestion-sagrilaft/public/admin/users" style="display: block; text-decoration: none; color: inherit;">
                        <div style="background: var(--bg-tertiary); border: 1px solid var(--border-secondary); border-radius: var(--radius-sm); padding: 1.5rem; transition: all var(--transition-fast); cursor: pointer; text-align: center;">
                            <div style="color: var(--text-primary); font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem;">Gestión de Usuarios</div>
                            <div style="color: var(--text-muted); font-size: 0.75rem;">Administrar usuarios del sistema</div>
                        </div>
                    </a>

                    <a href="/gestion-sagrilaft/public/admin/vendedores" style="display: block; text-decoration: none; color: inherit;">
                        <div style="background: var(--bg-tertiary); border: 1px solid var(--border-secondary); border-radius: var(--radius-sm); padding: 1.5rem; transition: all var(--transition-fast); cursor: pointer; text-align: center;">
                            <div style="color: var(--text-primary); font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem;">Gestión de Vendedores</div>
                            <div style="color: var(--text-muted); font-size: 0.75rem;">Administrar vendedores</div>
                        </div>
                    </a>

                    <a href="/gestion-sagrilaft/public/admin/logistics" style="display: block; text-decoration: none; color: inherit;">
                        <div style="background: var(--bg-tertiary); border: 1px solid var(--border-secondary); border-radius: var(--radius-sm); padding: 1.5rem; transition: all var(--transition-fast); cursor: pointer; text-align: center;">
                            <div style="color: var(--text-primary); font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem;">Evaluación Logística</div>
                            <div style="color: var(--text-muted); font-size: 0.75rem;">Evaluar proveedores logísticos</div>
                        </div>
                    </a>

                    <a href="/gestion-sagrilaft/public/forms" style="display: block; text-decoration: none; color: inherit;">
                        <div style="background: var(--bg-tertiary); border: 1px solid var(--border-secondary); border-radius: var(--radius-sm); padding: 1.5rem; transition: all var(--transition-fast); cursor: pointer; text-align: center;">
                            <div style="color: var(--text-primary); font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem;">Todos los Formularios</div>
                            <div style="color: var(--text-muted); font-size: 0.75rem;">Ver todos los formularios</div>
                        </div>
                    </a>

                    <a href="/gestion-sagrilaft/public/forms" style="display: block; text-decoration: none; color: inherit;">
                        <div style="background: var(--bg-tertiary); border: 1px solid var(--border-secondary); border-radius: var(--radius-sm); padding: 1.5rem; transition: all var(--transition-fast); cursor: pointer; text-align: center;">
                            <div style="color: var(--text-primary); font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem;">Exportar Excel</div>
                            <div style="color: var(--text-muted); font-size: 0.75rem;">Descargar datos desde formularios</div>
                        </div>
                    </a>
                </div>
                
                <style>
                    a > div:hover {
                        background: var(--bg-card-hover) !important;
                        border-color: var(--border-focus) !important;
                        transform: translateY(-2px);
                    }
                    a > div:active {
                        transform: translateY(0);
                    }
                </style>
            </div>
        </main>
    </div>
</body>
</html>
