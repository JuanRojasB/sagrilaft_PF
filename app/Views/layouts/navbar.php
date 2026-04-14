<style>
    .navbar {
        background: var(--bg-card);
        box-shadow: var(--shadow-md);
        border-bottom: 1px solid var(--border-accent);
    }
    .navbar-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    .navbar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 4rem;
    }
    .navbar-left {
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    .navbar-brand {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        text-decoration: none;
        transition: color var(--transition-fast);
    }
    .navbar-brand:hover {
        color: var(--accent-light);
    }
    .navbar-link {
        color: var(--text-secondary);
        text-decoration: none;
        transition: color var(--transition-fast);
        font-size: 0.95rem;
    }
    .navbar-link:hover {
        color: var(--accent-lighter);
    }
    .navbar-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .navbar-user {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    .navbar-logout-btn {
        background: var(--error-bg);
        color: var(--error-light);
        border: 1px solid var(--error-border);
        padding: 0.5rem 1rem;
        border-radius: var(--radius-full);
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all var(--transition-fast);
    }
    .navbar-logout-btn:hover {
        background: rgba(239, 68, 68, 0.3);
        transform: translateY(-1px);
    }
</style>

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-content">
            <div class="navbar-left">
                <a href="index.php?route=/dashboard" class="navbar-brand">SAGRILAFT</a>
                <a href="index.php?route=/forms" class="navbar-link">Formularios</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="index.php?route=/admin" class="navbar-link">Admin</a>
                <?php endif; ?>
            </div>
            
            <div class="navbar-right">
                <span class="navbar-user"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></span>
                <form method="POST" action="index.php?route=/logout">
                    <button type="submit" class="navbar-logout-btn">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
