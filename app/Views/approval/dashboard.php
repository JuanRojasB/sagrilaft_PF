<!--
    Vista: Dashboard de Revisor
    
    Descripción:
    - Panel de control para revisores de formularios
    - Muestra todos los formularios del sistema con filtros
    - Permite filtrar por estado: Todos, Pendientes, Aprobados, Rechazados
    - Cada formulario muestra: ID, título, creador, tipo y estado
    
    Filtros:
    - Todos: Muestra todos los formularios
    - Pendientes: Solo formularios esperando revisión
    - Aprobados: Formularios ya aprobados
    - Rechazados: Formularios rechazados
    
    Acceso:
    - Solo usuarios con rol 'revisor' pueden acceder
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Revisor - SAGRILAFT</title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <link rel="stylesheet" href="/gestion-sagrilaft/public/assets/css/global-theme.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        /* ===== TEMA CLARO - DASHBOARD ===== */
        body { background: #f8fafc !important; color: #0f172a !important; }

        /* Header */
        .app-header { background: #ffffff !important; border-bottom: 1px solid #e2e8f0 !important; }

        /* Botones del header */
        .btn-export { background: rgba(16, 185, 129, 0.1) !important; color: #059669 !important; border: 1px solid rgba(16, 185, 129, 0.4) !important; }
        .btn-export:hover { background: rgba(16, 185, 129, 0.2) !important; }
        .btn-firma { background: #f1f5f9 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important; }
        .btn-firma:hover { background: #e2e8f0 !important; border-color: #94a3b8 !important; }
        .btn-logout { background: rgba(239, 68, 68, 0.08) !important; color: #dc2626 !important; border: 1px solid rgba(239, 68, 68, 0.3) !important; }
        .btn-logout:hover { background: rgba(239, 68, 68, 0.15) !important; }

        /* Contenedor de filtros */
        .filters-container { background: #ffffff !important; border: 1px solid #e2e8f0 !important; border-radius: 0.5rem !important; }

        /* Botones de filtro */
        .filter-btn { background: #f1f5f9 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important; }
        .filter-btn:hover { background: #e2e8f0 !important; border-color: #94a3b8 !important; }
        .filter-btn.active {
            background: #1d4ed8 !important;
            color: #ffffff !important;
            border: 2px solid #1d4ed8 !important;
            transform: scale(1.02);
        }
        .filter-btn.active:hover {
            background: #1e40af !important;
            border-color: #1e40af !important;
        }

        /* Labels de filtros */
        [style*="color: #94a3b8"] { color: #64748b !important; }
        [style*="color: #9ca3af"] { color: #64748b !important; }

        /* Badges de conteo en filtros */
        .filter-btn span[style*="background: rgba(71, 85, 105, 0.4)"] { background: #e2e8f0 !important; color: #475569 !important; }
        .filter-btn span[style*="background: rgba(59, 130, 246, 0.4)"] { background: #dbeafe !important; color: #1d4ed8 !important; }

        /* Separador */
        [style*="height: 1px; background: rgba(71, 85, 105"] { background: #e2e8f0 !important; }
        [style*="width: 1px; height: 24px; background: rgba(71, 85, 105"] { background: #e2e8f0 !important; }

        /* Inputs */
        #searchInput, input[type="date"] {
            background: #ffffff !important; color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
        }
        #searchInput::placeholder { color: #94a3b8 !important; }
        #searchInput:focus, input[type="date"]:focus {
            border-color: #3b82f6 !important; background: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
        }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: none !important; cursor: pointer; }
        select { background: #ffffff !important; color: #0f172a !important; border: 1px solid #cbd5e1 !important; }
        select option { background: #ffffff; color: #0f172a; }

        /* Botón limpiar filtros */
        [onclick="clearFilters()"] { background: rgba(239, 68, 68, 0.08) !important; color: #dc2626 !important; border: 1px solid rgba(239, 68, 68, 0.3) !important; }

        /* Barra de paginación */
        .pagination-bar { background: #ffffff !important; border: 1px solid #e2e8f0 !important; border-radius: 0.5rem !important; }
        .sort-btn, .pagesize-btn, .pagination-btn {
            background: #f1f5f9 !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;
        }
        .sort-btn:hover, .pagesize-btn:hover, .pagination-btn:hover {
            background: #e2e8f0 !important; border-color: #94a3b8 !important;
        }
        .sort-btn.active, .pagesize-btn.active {
            background: #1d4ed8 !important; color: #ffffff !important;
            border: 2px solid #1d4ed8 !important;
        }
        .sort-btn.active:hover, .pagesize-btn.active:hover {
            background: #1e40af !important; border-color: #1e40af !important;
        }
        #resultInfo { color: #64748b !important; }
        #pageInfo { color: #0f172a !important; }

        /* Cards de formularios - override forzado del fondo inline */
        a.result-link > div,
        .results-grid a > div {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
            color: #0f172a !important;
        }
        a.result-link > div:hover,
        .results-grid a:hover > div {
            background: #f8fafc !important;
            border-color: #93c5fd !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12) !important;
            transform: translateY(-2px) !important;
        }

        /* Todos los textos dentro de las cards */
        .results-grid a > div * { color: inherit !important; }
        .results-grid a > div [style*="color: #e2e8f0"],
        .results-grid a > div [style*="color: #e5e7eb"] { color: #0f172a !important; }
        .results-grid a > div [style*="color: #cbd5e1"] { color: #334155 !important; }
        .results-grid a > div [style*="color: #94a3b8"] { color: #64748b !important; }
        .results-grid a > div [style*="color: #64748b"] { color: #64748b !important; }

        /* Separador footer card */
        .results-grid a > div [style*="border-top"] { border-top-color: #e2e8f0 !important; }

        /* Badge rol */
        .results-grid a span[style*="background: rgba(71, 85, 105"] { background: #f1f5f9 !important; color: #475569 !important; }
        .results-grid a span[style*="background: rgba(251, 191, 36"] { background: #fef9c3 !important; color: #a16207 !important; }

        /* Gráficas */
        .charts-grid > div { background: #ffffff !important; border: 1px solid #e2e8f0 !important; border-radius: 0.5rem !important; }
        .charts-grid > div[style*="background: rgba(15"] { background: #ffffff !important; }
        .charts-grid h3,
        .charts-grid h3[style] { color: #0f172a !important; }
        /* Leyenda de estado bajo la dona */
        #chartStatusLegend span[style*="color:"],
        #chartStatusLegend [style*="color: #"] { color: #1e293b !important; }

        /* Modal firma */
        #modalFirma > div { background: #ffffff !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important; }
        #modalFirma h3 { color: #0f172a !important; }

        /* Empty state */
        [style*="grid-column: 1 / -1"] { background: #ffffff !important; border: 1px solid #e2e8f0 !important; color: #64748b !important; }
    </style>
</head>
<body>
    <div class="app">
        <header class="app-header" style="padding: 0.75rem 1.25rem; background: rgba(255, 255, 255, 0.98); border-bottom: 1px solid rgba(71, 85, 105, 0.2); display: flex; justify-content: space-between; align-items: center;">
            <div class="brand" style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="/gestion-sagrilaft/public/assets/img/orb-logo.png?v=4" alt="Logo" style="width: 32px; height: 32px;">
                <h1 style="font-size: 1.1rem; margin: 0; color: #0f172a;">SAGRILAFT - Revisor</h1>
            </div>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 0.75rem;">
                <button onclick="exportarFiltrados()" class="btn-export" style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.4rem 0.75rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.8rem; transition: all 0.15s; display: flex; align-items: center; gap: 0.3rem; white-space: nowrap;">
                    <span>Exportar a Excel</span>
                </button>
                <button onclick="mostrarModalFirma()" class="btn-firma" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.8rem; transition: all 0.15s; display: flex; align-items: center; gap: 0.3rem; white-space: nowrap;">
                    <span>Firma Digital</span>
                </button>
                <form method="POST" action="/gestion-sagrilaft/public/reviewer/logout" style="margin: 0;">
                    <button type="submit" class="btn-logout" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 0.4rem 0.75rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.8rem; transition: all 0.15s; white-space: nowrap;">Salir</button>
                </form>
            </div>
            
            <style>
                .header-actions a:hover,
                .header-actions button:hover {
                    background: rgba(51, 65, 85, 0.8) !important;
                    border-color: rgba(59, 130, 246, 0.7) !important;
                }
                .header-actions button[type="submit"]:hover {
                    background: rgba(239, 68, 68, 0.3) !important;
                    border-color: rgba(239, 68, 68, 0.6) !important;
                }
                .header-actions button[onclick="exportarFiltrados()"]:hover {
                    background: rgba(16, 185, 129, 0.3) !important;
                    border-color: rgba(16, 185, 129, 0.6) !important;
                }
                
                /* Responsive header - todo en una columna */
                @media (max-width: 768px) {
                    .app-header {
                        flex-direction: column !important;
                        align-items: stretch !important;
                        gap: 0.75rem !important;
                        padding: 1rem !important;
                    }
                    
                    .brand {
                        justify-content: center !important;
                        padding-bottom: 0.5rem;
                        border-bottom: 1px solid rgba(71, 85, 105, 0.3);
                    }
                    
                    .header-actions {
                        flex-direction: column !important;
                        width: 100% !important;
                        gap: 0.5rem !important;
                    }
                    
                    .header-actions button,
                    .header-actions form {
                        width: 100% !important;
                    }
                    
                    .header-actions button {
                        justify-content: center !important;
                    }
                }
            </style>
        </header>

        <main class="app-main" style="padding: 0; overflow-x: hidden;">
            <div style="max-width: 100%; margin: 0; padding: 1rem; box-sizing: border-box; overflow-x: hidden;">
                <!-- Filtros compactos -->
                <div class="filters-container" style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(71, 85, 105, 0.6); border-radius: 0.25rem; padding: 0.6rem 0.9rem; margin-bottom: 0.75rem;">
                    <!-- Fila 1: Estado y Tipo -->
                    <div class="filters-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.6rem;">
                        <!-- Estado -->
                        <div>
                            <span style="color: #94a3b8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.35rem;">Estado</span>
                            <div class="status-filters" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.35rem;">
                                <?php 
                                $pending = count(array_filter($forms, fn($f) => $f['approval_status'] === 'pending'));
                                $approved = count(array_filter($forms, fn($f) => $f['approval_status'] === 'approved'));
                                $approvedPending = count(array_filter($forms, fn($f) => $f['approval_status'] === 'approved_pending'));
                                $rejected = count(array_filter($forms, fn($f) => $f['approval_status'] === 'rejected'));
                                $corrected = count(array_filter($forms, fn($f) => $f['approval_status'] === 'corrected'));
                                $otros = count($forms) - ($pending + $approved + $approvedPending + $rejected + $corrected);
                                ?>
                                <button onclick="filterByStatus('all')" id="status-all" class="filter-btn active" style="padding: 0.3rem 0.4rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                    <span>Todos</span>
                                    <span style="background: rgba(59, 130, 246, 0.4); padding: 0.05rem 0.25rem; border-radius: 0.15rem; font-size: 0.65rem;"><?= count($forms) ?></span>
                                </button>
                                <button onclick="filterByStatus('pending')" id="status-pending" class="filter-btn" style="padding: 0.3rem 0.4rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                    <span>Pend.</span>
                                    <span style="background: rgba(71, 85, 105, 0.4); padding: 0.05rem 0.25rem; border-radius: 0.15rem; font-size: 0.65rem;"><?= $pending ?></span>
                                </button>
                                <button onclick="filterByStatus('approved')" id="status-approved" class="filter-btn" style="padding: 0.3rem 0.4rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                    <span>Aprob.</span>
                                    <span style="background: rgba(71, 85, 105, 0.4); padding: 0.05rem 0.25rem; border-radius: 0.15rem; font-size: 0.65rem;"><?= $approved ?></span>
                                </button>
                                <button onclick="filterByStatus('rejected')" id="status-rejected" class="filter-btn" style="padding: 0.3rem 0.4rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                    <span>Rech.</span>
                                    <span style="background: rgba(71, 85, 105, 0.4); padding: 0.05rem 0.25rem; border-radius: 0.15rem; font-size: 0.65rem;"><?= $rejected ?></span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Tipo -->
                        <div>
                            <span style="color: #94a3b8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.35rem;">Tipo de Usuario</span>
                            <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                <?php 
                                $clientes = count(array_filter($forms, fn($f) => ($f['role'] ?? 'cliente') === 'cliente'));
                                $proveedores = count(array_filter($forms, fn($f) => ($f['role'] ?? 'cliente') === 'proveedor'));
                                $transportistas = count(array_filter($forms, fn($f) => ($f['role'] ?? 'cliente') === 'transportista'));
                                $otros = count(array_filter($forms, fn($f) => !in_array($f['role'] ?? 'cliente', ['cliente', 'proveedor', 'transportista'])));
                                ?>
                                <button onclick="filterByRole('all')" id="role-all" class="filter-btn active" style="padding: 0.4rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.75rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.3rem; white-space: nowrap;">
                                    <span>Todos</span>
                                </button>
                                <button onclick="filterByRole('cliente')" id="role-cliente" class="filter-btn" style="padding: 0.4rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.75rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.3rem; white-space: nowrap;">
                                    <span>Cliente</span>
                                    <span style="background: rgba(71, 85, 105, 0.4); padding: 0.05rem 0.3rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 700;"><?= $clientes ?></span>
                                </button>
                                <button onclick="filterByRole('proveedor')" id="role-proveedor" class="filter-btn" style="padding: 0.4rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.75rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.3rem; white-space: nowrap;">
                                    <span>Proveedor</span>
                                    <span style="background: rgba(71, 85, 105, 0.4); padding: 0.05rem 0.3rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 700;"><?= $proveedores ?></span>
                                </button>
                                <button onclick="filterByRole('transportista')" id="role-transportista" class="filter-btn" style="padding: 0.4rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.75rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.3rem; white-space: nowrap;">
                                    <span>Transportista</span>
                                    <span style="background: rgba(71, 85, 105, 0.4); padding: 0.05rem 0.3rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 700;"><?= $transportistas ?></span>
                                </button>
                                <?php if ($otros > 0): ?>
                                <button onclick="filterByRole('otros')" id="role-otros" class="filter-btn" style="padding: 0.4rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.75rem; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 0.3rem; white-space: nowrap;">
                                    <span>Otros</span>
                                    <span style="background: rgba(71, 85, 105, 0.4); padding: 0.05rem 0.3rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 700;"><?= $otros ?></span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Separador horizontal -->
                    <div style="height: 1px; background: rgba(71, 85, 105, 0.4); margin-bottom: 1rem;"></div>
                    
                    <!-- Búsqueda abajo -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="color: #94a3b8; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Buscar</span>
                            <button onclick="clearFilters()" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 0.3rem 0.6rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s; display: flex; align-items: center; gap: 0.3rem;">
                                <span>Limpiar Filtros</span>
                            </button>
                        </div>
                        <div class="search-grid" style="display: grid; grid-template-columns: 1fr auto auto; gap: 0.75rem; align-items: end;">
                            <!-- Campo de búsqueda -->
                            <div style="position: relative;">
                                <input type="text" id="searchInput" placeholder="ID, título, empresa, NIT, creador..." 
                                       oninput="searchForms()"
                                       style="width: 100%; padding: 0.6rem 0.6rem 0.6rem 2.2rem; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(71, 85, 105, 0.5); border-radius: 0.25rem; color: #e2e8f0; font-size: 0.85rem; transition: all 0.15s;">
                                <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; pointer-events: none;">&#128269;</span>
                            </div>
                            
                            <!-- Fecha desde -->
                            <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                                <label for="dateFrom" style="color: #94a3b8; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Desde</label>
                                <input type="date" id="dateFrom" onchange="searchForms()"
                                       style="padding: 0.6rem; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(71, 85, 105, 0.5); border-radius: 0.25rem; color: #e2e8f0; font-size: 0.85rem; transition: all 0.15s; min-width: 150px;">
                            </div>
                            
                            <!-- Fecha hasta -->
                            <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                                <label for="dateTo" style="color: #94a3b8; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Hasta</label>
                                <input type="date" id="dateTo" onchange="searchForms()"
                                       style="padding: 0.6rem; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(71, 85, 105, 0.5); border-radius: 0.25rem; color: #e2e8f0; font-size: 0.85rem; transition: all 0.15s; min-width: 150px;">
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <style>
                    .filter-btn:hover {
                        background: rgba(51, 65, 85, 0.8) !important;
                        border-color: rgba(59, 130, 246, 0.7) !important;
                    }
                    .filter-btn:active {
                        transform: scale(0.98) !important;
                    }
                    #searchInput:focus, input[type="date"]:focus {
                        outline: none;
                        border-color: rgba(59, 130, 246, 0.6);
                        background: rgba(15, 23, 42, 0.8);
                    }
                    #searchInput::placeholder {
                        color: #64748b;
                    }
                    input[type="date"]::-webkit-calendar-picker-indicator {
                        filter: invert(0.6);
                        cursor: pointer;
                    }
                </style>
                
                <!-- Paginación y Ordenamiento compacto -->
                <div class="pagination-bar" style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(71, 85, 105, 0.6); border-radius: 0.25rem; padding: 0.5rem 0.9rem; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                    <!-- Izquierda: Ordenamiento + Resultados por página -->
                    <div class="pagination-left" style="display: flex; align-items: center; gap: 1rem;">
                        <!-- Ordenamiento -->
                        <div class="sort-controls" style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: #94a3b8; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">Ordenar:</span>
                            <div style="display: flex; gap: 0.3rem;">
                                <button onclick="changeSortBy('date')" id="sort-date" class="sort-btn active" style="padding: 0.25rem 0.45rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s; display: flex; align-items: center; gap: 0.2rem;">
                                    <span>Fecha</span>
                                    <span class="sort-arrow" style="font-size: 0.65rem;">&#8595;</span>
                                </button>
                                <button onclick="changeSortBy('status')" id="sort-status" class="sort-btn" style="padding: 0.25rem 0.45rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s; display: flex; align-items: center; gap: 0.2rem;">
                                    <span>Estado</span>
                                    <span class="sort-arrow" style="font-size: 0.65rem;">&#8597;</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Separador -->
                        <div class="separator-vertical" style="width: 1px; height: 24px; background: rgba(71, 85, 105, 0.4);"></div>
                        
                        <!-- Resultados por página -->
                        <div class="pagesize-controls" style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: #94a3b8; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">Por pág:</span>
                            <div style="display: flex; gap: 0.3rem;">
                                <button onclick="changePageSize(10)" id="pagesize-10" class="pagesize-btn" style="padding: 0.25rem 0.45rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s;">10</button>
                                <button onclick="changePageSize(25)" id="pagesize-25" class="pagesize-btn active" style="padding: 0.25rem 0.45rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s;">25</button>
                                <button onclick="changePageSize(50)" id="pagesize-50" class="pagesize-btn" style="padding: 0.25rem 0.45rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s;">50</button>
                                <button onclick="changePageSize(100)" id="pagesize-100" class="pagesize-btn" style="padding: 0.25rem 0.45rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s;">100</button>
                            </div>
                        </div>
                        
                        <!-- Separador -->
                        <div class="separator-vertical" style="width: 1px; height: 24px; background: rgba(71, 85, 105, 0.4);"></div>
                        
                        <!-- Contador de resultados -->
                        <span id="resultInfo" style="color: #64748b; font-size: 0.7rem; white-space: nowrap;">Mostrando 0 de 0</span>
                    </div>
                    
                    <!-- Derecha: Controles de paginación -->
                    <div class="pagination-controls" style="display: flex; align-items: center; gap: 0.4rem;">
                        <button onclick="goToPage('first')" class="pagination-btn" style="background: rgba(30, 41, 59, 0.6); color: #cbd5e1; border: 1px solid rgba(71, 85, 105, 0.6); padding: 0.25rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s;">&#171;</button>
                        <button onclick="goToPage('prev')" class="pagination-btn" style="background: rgba(30, 41, 59, 0.6); color: #cbd5e1; border: 1px solid rgba(71, 85, 105, 0.6); padding: 0.25rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s;">&#8249;</button>
                        <span id="pageInfo" style="color: #e2e8f0; font-size: 0.7rem; font-weight: 600; min-width: 60px; text-align: center;">Pág. 1/1</span>
                        <button onclick="goToPage('next')" class="pagination-btn" style="background: rgba(30, 41, 59, 0.6); color: #cbd5e1; border: 1px solid rgba(71, 85, 105, 0.6); padding: 0.25rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s;">&#8250;</button>
                        <button onclick="goToPage('last')" class="pagination-btn" style="background: rgba(30, 41, 59, 0.6); color: #cbd5e1; border: 1px solid rgba(71, 85, 105, 0.6); padding: 0.25rem 0.5rem; border-radius: 0.25rem; cursor: pointer; font-weight: 600; font-size: 0.7rem; transition: all 0.15s;">&#187;</button>
                    </div>
                </div>
                
                <style>
                    .pagesize-btn:hover, .pagination-btn:hover, .sort-btn:hover {
                        background: rgba(51, 65, 85, 0.8) !important;
                        border-color: rgba(59, 130, 246, 0.7) !important;
                    }
                    .pagination-btn:disabled {
                        opacity: 0.4;
                        cursor: not-allowed;
                    }
                </style>
                
                <!-- Grid de resultados optimizado - 3 columnas -->
                <div class="results-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; opacity: 1; transition: opacity 0.2s;" id="resultsGrid">
                    <?php if (empty($forms)): ?>
                    <div style="grid-column: 1 / -1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 2rem; text-align: center; color: #64748b;">No hay formularios</div>
                    <?php else: ?>
                    <?php
                    $formTypeLabels = [
                        'cliente_natural' => 'FGF-08 - Cliente Persona Natural',
                        'cliente_juridica' => 'FGF-16 - Cliente Persona Jurídica',
                        'declaracion_fondos_clientes' => 'FGF-17 - Declaración Origen de Fondos (Cliente)',
                        'declaracion_cliente' => 'FGF-17 - Declaración Origen de Fondos (Cliente)',
                        'proveedor_natural' => 'FCO-05 - Proveedor Persona Natural',
                        'proveedor_juridica' => 'FCO-02 - Proveedor Persona Jurídica',
                        'proveedor_internacional' => 'FCO-04 - Proveedor Internacional',
                        'declaracion_fondos_proveedores' => 'FCO-03 - Declaración Origen de Fondos (Proveedor)',
                        'declaracion_proveedor' => 'FCO-03 - Declaración Origen de Fondos (Proveedor)',
                    ];
                    ?>
                    <?php foreach ($forms as $form): ?>
                    <?php
                        $formType = (string)($form['form_type'] ?? '');
                        $formTypeLabel = $formTypeLabels[$formType] ?? ucfirst(str_replace('_', ' ', $formType ?: 'formulario'));
                    ?>
                    <a href="/gestion-sagrilaft/public/approval/<?= $form['approval_token'] ?>" 
                       class="result-link" 
                       data-status="<?= $form['approval_status'] ?>"
                       data-role="<?= $form['role'] ?? 'cliente' ?>"
                       data-date="<?= date('Y-m-d', strtotime($form['created_at'])) ?>"
                       data-id="<?= $form['id'] ?>"
                       data-title="<?= strtolower(htmlspecialchars($form['title'])) ?>"
                       data-search="<?= strtolower(htmlspecialchars($form['id'] . ' ' . $form['title'] . ' ' . $form['creator_name'] . ' ' . ($form['company_name'] ?? '') . ' ' . ($form['nit'] ?? ''))) ?>"
                       style="display: block; text-decoration: none; color: inherit;">
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.85rem; transition: all 0.15s; cursor: pointer; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                            <!-- Header con ID y estado -->
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.75rem; margin-bottom: 0.5rem;">
                                <div style="color: #64748b; font-size: 0.75rem; font-weight: 700;">#<?= $form['id'] ?></div>
                                <?php if ($form['approval_status'] === 'approved'): ?>
                                <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 0.2rem 0.5rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">Aprobado</span>
                                <?php elseif ($form['approval_status'] === 'approved_pending'): ?>
                                <span style="background: #fef9c3; color: #a16207; border: 1px solid #fde047; padding: 0.2rem 0.5rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">Con Observaciones</span>
                                <?php elseif ($form['approval_status'] === 'corrected'): ?>
                                <span style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 0.2rem 0.5rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">Corregido</span>
                                <?php elseif ($form['approval_status'] === 'rejected'): ?>
                                <span style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 0.2rem 0.5rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">Rechazado</span>
                                <?php else: ?>
                                <span style="background: #fef9c3; color: #a16207; border: 1px solid #fde047; padding: 0.2rem 0.5rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap;">Pendiente</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Título -->
                            <div style="color: #0f172a; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.3;">
                                <?= htmlspecialchars($form['title']) ?>
                            </div>
                            
                            <div style="color: #64748b; font-size: 0.72rem; margin-bottom: 0.5rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                Formulario: <?= htmlspecialchars($formTypeLabel) ?>
                            </div>
                            
                            <!-- Información adicional -->
                            <?php if (!empty($form['company_name'])): ?>
                            <div style="color: #334155; font-size: 0.75rem; margin-bottom: 0.3rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($form['company_name']) ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($form['nit'])): ?>
                            <div style="color: #64748b; font-size: 0.7rem; margin-bottom: 0.5rem;">
                                NIT: <?= htmlspecialchars($form['nit']) ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Footer con creador y fecha -->
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; padding-top: 0.5rem; border-top: 1px solid #f1f5f9;">
                                <div style="display: flex; align-items: center; gap: 0.3rem; flex: 1; min-width: 0;">
                                    <span style="background: #f1f5f9; padding: 0.1rem 0.35rem; border-radius: 0.2rem; font-size: 0.7rem; font-weight: 600; white-space: nowrap; <?= ($form['role'] ?? 'cliente') === 'proveedor' ? 'background: #fef9c3; color: #a16207;' : 'color: #475569;' ?>">
                                        <?= ucfirst($form['role'] ?? 'cliente') ?>
                                    </span>
                                    <span style="color: #64748b; font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($form['creator_name']) ?></span>
                                </div>
                                <div style="color: #64748b; font-size: 0.7rem; white-space: nowrap;">
                                    <?= date('d/m/Y', strtotime($form['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <style>
                    .result-link {
                        min-width: 0; /* Permite que flex/grid comprima el elemento */
                    }
                    
                    .result-link > div {
                        min-width: 0;
                        max-width: 100%;
                        overflow: hidden;
                        box-sizing: border-box;
                    }
                    
                    .result-link > div > * {
                        max-width: 100%;
                        box-sizing: border-box;
                    }
                    
                    .result-link > div:hover {
                        background: #f0f9ff !important;
                        border-color: #93c5fd !important;
                        transform: translateY(-1px);
                        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12) !important;
                    }
                    .result-link > div:active {
                        transform: translateY(0);
                    }
                    
                    /* Responsive para grid de resultados */
                    .results-grid {
                        width: 100%;
                        max-width: 100%;
                        overflow: hidden;
                    }
                    
                    @media (max-width: 1400px) {
                        .results-grid {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }
                    
                    @media (max-width: 768px) {
                        .results-grid {
                            grid-template-columns: 1fr !important;
                        }
                        
                        /* Filtros en móvil */
                        .filters-row {
                            grid-template-columns: 1fr !important;
                        }
                        
                        .status-filters {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                        
                        .search-grid {
                            grid-template-columns: 1fr !important;
                        }
                        
                        .search-grid > div {
                            width: 100%;
                        }
                        
                        /* Paginación responsive */
                        .pagination-bar {
                            flex-direction: column !important;
                            gap: 0.75rem !important;
                        }
                        
                        .pagination-left {
                            flex-direction: column !important;
                            width: 100%;
                            gap: 0.75rem !important;
                        }
                        
                        .separator-vertical {
                            display: none !important;
                        }
                        
                        .sort-controls,
                        .pagesize-controls {
                            width: 100%;
                            justify-content: space-between;
                        }
                        
                        .pagination-controls {
                            width: 100%;
                            justify-content: center;
                        }
                        
                        #resultInfo {
                            text-align: center;
                            width: 100%;
                        }
                        
                        /* Header responsive */
                        .app-header {
                            flex-direction: column !important;
                            gap: 0.75rem !important;
                        }
                        
                        .header-actions {
                            width: 100%;
                            justify-content: space-between !important;
                        }
                        
                        .header-actions button,
                        .header-actions a {
                            font-size: 0.7rem !important;
                            padding: 0.35rem 0.5rem !important;
                        }
                    }
                    
                    @media (max-width: 480px) {
                        .status-filters {
                            grid-template-columns: 1fr !important;
                        }
                        
                        .header-actions {
                            flex-direction: column !important;
                        }
                        
                        .header-actions button,
                        .header-actions a {
                            width: 100%;
                        }
                        
                        /* Botones de paginación más compactos */
                        .pagesize-btn,
                        .sort-btn {
                            padding: 0.2rem 0.35rem !important;
                            font-size: 0.65rem !important;
                        }
                        
                        .pagination-btn {
                            padding: 0.2rem 0.4rem !important;
                        }
                    }
                </style>
                
                <!-- Gráficas de estadísticas -->
                <div class="charts-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem;">
                    <!-- Gráfica 1: Por Estado -->
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                        <h3 style="color: #0f172a; font-size: 0.85rem; margin: 0 0 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            Por Estado
                        </h3>
                        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                            <div style="position: relative; width: 160px; height: 160px;">
                                <canvas id="chartStatus"></canvas>
                            </div>
                        </div>
                        <div id="chartStatusLegend" style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.75rem;"></div>
                    </div>
                    
                    <!-- Gráfica 2: Por Tipo -->
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                        <h3 style="color: #0f172a; font-size: 0.85rem; margin: 0 0 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            Por Tipo
                        </h3>
                        <div style="position: relative; height: 200px;">
                            <canvas id="chartRole"></canvas>
                        </div>
                    </div>
                    
                    <!-- Gráfica 3: Tendencia -->
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                        <h3 style="color: #0f172a; font-size: 0.85rem; margin: 0 0 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            Últimos 30 Días
                        </h3>
                        <div style="position: relative; height: 200px;">
                            <canvas id="chartTrend"></canvas>
                        </div>
                    </div>
                </div>
                
                <style>
                    /* Responsive para gráficas */
                    @media (max-width: 1200px) {
                        .charts-grid {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                        .charts-grid > div:last-child {
                            grid-column: 1 / -1;
                        }
                    }
                    
                    @media (max-width: 768px) {
                        .charts-grid {
                            grid-template-columns: 1fr !important;
                        }
                        .charts-grid > div:last-child {
                            grid-column: auto;
                        }
                    }
                </style>
                        <canvas id="chartTrend" style="max-height: 180px;"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Version 2.0 - Fix ordenamiento
        let currentStatusFilter = 'all';
        let currentRoleFilter = 'all';
        let currentSearchTerm = '';
        let currentPage = 1;
        let pageSize = 25;
        let totalFilteredResults = 0;
        let sortBy = 'date'; // id, date, status, title
        let sortOrder = 'desc'; // asc, desc

        // Definir funciones globalmente para que estén disponibles en onclick
        window.filterByStatus = function(status) {
            currentStatusFilter = status;
            currentPage = 1;
            saveFilters();
            applyFilters();
            updateFilterButtons('status', status);
        };
        
        window.filterByRole = function(role) {
            currentRoleFilter = role;
            currentPage = 1;
            saveFilters();
            applyFilters();
            updateFilterButtons('role', role);
        };
        
        window.clearFilters = function() {
            currentStatusFilter = 'all';
            currentRoleFilter = 'all';
            currentSearchTerm = '';
            currentPage = 1;
            sortBy = 'date';
            sortOrder = 'desc';
            
            document.getElementById('searchInput').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            
            updateFilterButtons('status', 'all');
            updateFilterButtons('role', 'all');
            updateSortButtons();
            
            localStorage.removeItem('sagrilaft_filters');
            applyFilters();
        };

        window.searchForms = function() {
            currentSearchTerm = document.getElementById('searchInput').value.toLowerCase();
            currentPage = 1;
            saveFilters();
            applyFilters();
        };

        window.changeSortBy = function(field) {
            if (sortBy === field) {
                sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                sortBy = field;
                sortOrder = field === 'date' ? 'desc' : 'asc';
            }
            saveFilters();
            applyFilters();
            updateSortButtons();
        };

        window.changePageSize = function(size) {
            pageSize = size;
            currentPage = 1;
            saveFilters();
            updatePageSizeButtons();
            applyFilters();
        };

        window.changePage = function(page) {
            currentPage = page;
            applyFilters();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        window.goToPage = function(action) {
            const totalPages = Math.ceil(totalFilteredResults / pageSize);
            
            switch(action) {
                case 'first':
                    currentPage = 1;
                    break;
                case 'prev':
                    if (currentPage > 1) currentPage--;
                    break;
                case 'next':
                    if (currentPage < totalPages) currentPage++;
                    break;
                case 'last':
                    currentPage = totalPages;
                    break;
            }
            
            applyFilters();
        };

        window.mostrarModalFirma = function() {
            document.getElementById('modalFirma').style.display = 'flex';
        };

        window.cerrarModalFirma = function() {
            document.getElementById('modalFirma').style.display = 'none';
        };

        window.exportarFiltrados = function() {
            const visibleLinks = Array.from(document.querySelectorAll('.result-link'))
                .filter(link => link.style.display !== 'none')
                .map(link => link.getAttribute('data-id'));
            
            if (visibleLinks.length === 0) {
                alert('No hay formularios para exportar con los filtros actuales');
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/gestion-sagrilaft/public/excel/download-filtered';
            form.style.display = 'none';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'form_ids';
            input.value = visibleLinks.join(',');
            form.appendChild(input);
            
            const filterInfo = document.createElement('input');
            filterInfo.type = 'hidden';
            filterInfo.name = 'filter_info';
            filterInfo.value = JSON.stringify({
                status: currentStatusFilter,
                role: currentRoleFilter,
                search: currentSearchTerm
            });
            form.appendChild(filterInfo);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        };

        // Cargar filtros guardados al iniciar
        function loadSavedFilters() {
            const saved = localStorage.getItem('sagrilaft_filters');
            if (saved) {
                try {
                    const filters = JSON.parse(saved);
                    currentStatusFilter = filters.status || 'all';
                    currentRoleFilter = filters.role || 'all';
                    currentSearchTerm = filters.search || '';
                    sortBy = filters.sortBy || 'date';
                    sortOrder = filters.sortOrder || 'desc';
                    pageSize = filters.pageSize || 25;
                    
                    // Restaurar valores en los campos
                    document.getElementById('searchInput').value = currentSearchTerm;
                    if (filters.dateFrom) document.getElementById('dateFrom').value = filters.dateFrom;
                    if (filters.dateTo) document.getElementById('dateTo').value = filters.dateTo;
                    
                    // Actualizar botones
                    updateFilterButtons('status', currentStatusFilter);
                    updateFilterButtons('role', currentRoleFilter);
                    updateSortButtons();
                    
                    // Actualizar botón de tamaño de página
                    updatePageSizeButtons();
                } catch (e) {
                    console.error('Error al cargar filtros guardados:', e);
                }
            }
        }

        function updateSortButtons() {
            document.querySelectorAll('.sort-btn').forEach(btn => {
                btn.classList.remove('active');
                const arrow = btn.querySelector('.sort-arrow');
                if (arrow) arrow.textContent = '↕';
            });
            const activeBtn = document.getElementById('sort-' + sortBy);
            if (activeBtn) {
                activeBtn.classList.add('active');
                const arrow = activeBtn.querySelector('.sort-arrow');
                if (arrow) arrow.textContent = sortOrder === 'asc' ? '↑' : '↓';
            }
        }

        function updatePageSizeButtons() {
            document.querySelectorAll('.pagesize-btn').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById('pagesize-' + pageSize);
            if (activeBtn) activeBtn.classList.add('active');
        }

        // Guardar filtros en localStorage
        function saveFilters() {
            const filters = {
                status: currentStatusFilter,
                role: currentRoleFilter,
                search: currentSearchTerm,
                sortBy: sortBy,
                sortOrder: sortOrder,
                pageSize: pageSize,
                dateFrom: document.getElementById('dateFrom').value,
                dateTo: document.getElementById('dateTo').value
            };
            localStorage.setItem('sagrilaft_filters', JSON.stringify(filters));
        }

        function searchForms() {
            currentSearchTerm = document.getElementById('searchInput').value.toLowerCase();
            currentPage = 1; // Reset a página 1 cuando se busca
            saveFilters();
            applyFilters();
        }

        function filterByStatus(status) {
            currentStatusFilter = status;
            currentPage = 1; // Reset a página 1 cuando se filtra
            saveFilters();
            applyFilters();
            updateFilterButtons('status', status);
        }

        function filterByRole(role) {
            currentRoleFilter = role;
            currentPage = 1; // Reset a página 1 cuando se filtra
            saveFilters();
            applyFilters();
            updateFilterButtons('role', role);
        }
        
        function clearFilters() {
            // Resetear todos los filtros
            currentStatusFilter = 'all';
            currentRoleFilter = 'all';
            currentSearchTerm = '';
            currentPage = 1;
            sortBy = 'date';
            sortOrder = 'desc';
            
            // Limpiar campos de formulario
            document.getElementById('searchInput').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            
            // Resetear botones de filtro
            updateFilterButtons('status', 'all');
            updateFilterButtons('role', 'all');
            updateSortButtons();
            
            // Limpiar localStorage
            localStorage.removeItem('sagrilaft_filters');
            
            // Aplicar filtros
            applyFilters();
        }
        
        function changeSortBy(field) {
            if (sortBy === field) {
                // Si es el mismo campo, cambiar orden
                sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                // Si es campo diferente, establecer orden por defecto
                sortBy = field;
                sortOrder = field === 'date' ? 'desc' : 'asc';
            }
            
            console.log('Ordenando por:', sortBy, 'Orden:', sortOrder);
            saveFilters();
            updateSortButtons();
            applyFilters();
        }
        
        function updateSortButtons() {
            document.querySelectorAll('.sort-btn').forEach(btn => {
                btn.classList.remove('active');
                const arrow = btn.querySelector('.sort-arrow');
                if (arrow) arrow.textContent = '↕';
            });
            const activeBtn = document.getElementById('sort-' + sortBy);
            if (activeBtn) {
                activeBtn.classList.add('active');
                const arrow = activeBtn.querySelector('.sort-arrow');
                if (arrow) arrow.textContent = sortOrder === 'asc' ? '↑' : '↓';
            }
        }

        function changePageSize(size) {
            pageSize = size;
            currentPage = 1;
            saveFilters();
            applyFilters();
            updatePageSizeButtons();
        }

        function goToPage(action) {
            const totalPages = Math.ceil(totalFilteredResults / pageSize);
            
            switch(action) {
                case 'first':
                    currentPage = 1;
                    break;
                case 'prev':
                    if (currentPage > 1) currentPage--;
                    break;
                case 'next':
                    if (currentPage < totalPages) currentPage++;
                    break;
                case 'last':
                    currentPage = totalPages;
                    break;
            }
            
            applyFilters();
        }

        function applyFilters() {
            const links = document.querySelectorAll('.result-link');
            let visibleCount = 0;
            let filteredResults = [];
            
            // Contadores por estado y rol (basados en búsqueda y fecha solamente)
            let counts = {
                status: { all: 0, pending: 0, approved: 0, rejected: 0 },
                role: { all: 0, cliente: 0, proveedor: 0, transportista: 0, otros: 0 }
            };
            
            // Obtener fechas de filtro
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            
            // Primera pasada: contar según búsqueda y fecha (para actualizar contadores)
            links.forEach(link => {
                const formStatus = link.getAttribute('data-status');
                const formRole = link.getAttribute('data-role') || 'cliente';
                const searchData = link.getAttribute('data-search') || '';
                const formDate = link.getAttribute('data-date') || '';
                
                // Verificar búsqueda
                const searchMatch = currentSearchTerm === '' || searchData.includes(currentSearchTerm);
                
                // Verificar filtro de fechas
                let dateMatch = true;
                if (dateFrom && formDate < dateFrom) {
                    dateMatch = false;
                }
                if (dateTo && formDate > dateTo) {
                    dateMatch = false;
                }
                
                // Contar solo los que pasan búsqueda y fecha
                if (searchMatch && dateMatch) {
                    // Aplicar filtro de rol actual para contar estados
                    let roleMatchForCount = false;
                    if (currentRoleFilter === 'all') {
                        roleMatchForCount = true;
                    } else if (currentRoleFilter === 'otros') {
                        roleMatchForCount = !['cliente', 'proveedor', 'transportista'].includes(formRole);
                    } else {
                        roleMatchForCount = formRole === currentRoleFilter;
                    }
                    
                    if (roleMatchForCount) {
                        counts.status.all++;
                        if (formStatus === 'pending') counts.status.pending++;
                        if (formStatus === 'approved') counts.status.approved++;
                        if (formStatus === 'rejected') counts.status.rejected++;
                    }
                    
                    // Aplicar filtro de estado actual para contar roles
                    const statusMatchForCount =
                        currentStatusFilter === 'all'
                        || formStatus === currentStatusFilter
                        || (currentStatusFilter === 'approved' && formStatus === 'approved_pending');
                    
                    if (statusMatchForCount) {
                        counts.role.all++;
                        if (formRole === 'cliente') counts.role.cliente++;
                        if (formRole === 'proveedor') counts.role.proveedor++;
                        if (formRole === 'transportista') counts.role.transportista++;
                        if (!['cliente', 'proveedor', 'transportista'].includes(formRole)) counts.role.otros++;
                    }
                }
            });

            // Segunda pasada: filtrar para mostrar
            links.forEach(link => {
                const formStatus = link.getAttribute('data-status');
                const formRole = link.getAttribute('data-role') || 'cliente';
                const searchData = link.getAttribute('data-search') || '';
                const formDate = link.getAttribute('data-date') || '';
                
                // Verificar filtro de estado
                const statusMatch =
                    currentStatusFilter === 'all'
                    || formStatus === currentStatusFilter
                    || (currentStatusFilter === 'approved' && formStatus === 'approved_pending');
                
                // Verificar filtro de rol
                let roleMatch = false;
                if (currentRoleFilter === 'all') {
                    roleMatch = true;
                } else if (currentRoleFilter === 'otros') {
                    roleMatch = !['cliente', 'proveedor', 'transportista'].includes(formRole);
                } else {
                    roleMatch = formRole === currentRoleFilter;
                }
                
                // Verificar búsqueda
                const searchMatch = currentSearchTerm === '' || searchData.includes(currentSearchTerm);
                
                // Verificar filtro de fechas
                let dateMatch = true;
                if (dateFrom && formDate < dateFrom) {
                    dateMatch = false;
                }
                if (dateTo && formDate > dateTo) {
                    dateMatch = false;
                }
                
                // Agregar a resultados filtrados si pasa todos los filtros
                if (statusMatch && roleMatch && searchMatch && dateMatch) {
                    filteredResults.push(link);
                }
            });
            
            // Ordenar resultados
            console.log('Aplicando ordenamiento:', sortBy, sortOrder, 'Total items:', filteredResults.length);
            
            filteredResults.sort((a, b) => {
                let valA, valB;
                
                switch(sortBy) {
                    case 'id':
                        valA = parseInt(a.getAttribute('data-id'));
                        valB = parseInt(b.getAttribute('data-id'));
                        break;
                    case 'date':
                        valA = a.getAttribute('data-date') || '';
                        valB = b.getAttribute('data-date') || '';
                        break;
                    case 'status':
                        // Orden alfabético simple: approved, pending, rejected
                        valA = a.getAttribute('data-status') || '';
                        valB = b.getAttribute('data-status') || '';
                        break;
                    case 'title':
                        valA = (a.getAttribute('data-title') || '').toLowerCase();
                        valB = (b.getAttribute('data-title') || '').toLowerCase();
                        break;
                }
                
                // Comparación
                if (sortOrder === 'asc') {
                    if (valA < valB) return -1;
                    if (valA > valB) return 1;
                    return 0;
                } else {
                    if (valA > valB) return -1;
                    if (valA < valB) return 1;
                    return 0;
                }
            });

            console.log('Primeros 5 después de ordenar:', 
                filteredResults.slice(0, 5).map(el => ({
                    id: el.getAttribute('data-id'),
                    date: el.getAttribute('data-date'),
                    status: el.getAttribute('data-status')
                }))
            );
            
            // Guardar total de resultados filtrados
            totalFilteredResults = filteredResults.length;
            
            // Calcular paginación
            const totalPages = Math.ceil(totalFilteredResults / pageSize);
            if (currentPage > totalPages && totalPages > 0) {
                currentPage = totalPages;
            }
            if (currentPage < 1) currentPage = 1;
            
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            
            // Ocultar todos primero
            links.forEach(link => link.style.display = 'none');
            
            // Obtener el contenedor del grid
            const grid = document.getElementById('resultsGrid');
            
            // Reordenar físicamente los elementos en el DOM según el orden filtrado
            filteredResults.forEach((link, index) => {
                // Mover el elemento al final del contenedor para reordenarlo
                grid.appendChild(link);
                
                // Mostrar solo los de la página actual
                if (index >= startIndex && index < endIndex) {
                    link.style.display = 'block';
                    visibleCount++;
                }
            });
            
            // Actualizar contadores en los botones
            updateCounters(counts);
            
            // Actualizar información de paginación
            updatePaginationInfo(startIndex + 1, Math.min(endIndex, totalFilteredResults), totalFilteredResults, currentPage, totalPages);
            
            // Mostrar mensaje si no hay resultados
            let tempNoResults = document.querySelector('.temp-no-results');
            
            if (totalFilteredResults === 0) {
                if (!tempNoResults) {
                    tempNoResults = document.createElement('div');
                    tempNoResults.className = 'temp-no-results';
                    tempNoResults.style.cssText = 'grid-column: 1 / -1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 2rem; text-align: center; color: #64748b;';
                    tempNoResults.textContent = 'No hay formularios con estos filtros';
                    grid.appendChild(tempNoResults);
                }
            } else {
                if (tempNoResults) {
                    tempNoResults.remove();
                }
            }
            
            // Mostrar el grid con fade-in suave
            grid.style.opacity = '1';
            
            // Actualizar gráficas con los datos filtrados
            updateCharts();
        }
        
        function updatePaginationInfo(start, end, total, page, totalPages) {
            // Actualizar texto de resultados
            document.getElementById('resultInfo').textContent = `Mostrando ${start}-${end} de ${total}`;
            
            // Actualizar texto de página (formato compacto)
            document.getElementById('pageInfo').textContent = `Pág. ${page}/${totalPages || 1}`;
            
            // Habilitar/deshabilitar botones de paginación
            const buttons = document.querySelectorAll('.pagination-btn');
            buttons[0].disabled = page === 1; // First
            buttons[1].disabled = page === 1; // Prev
            buttons[2].disabled = page === totalPages || totalPages === 0; // Next
            buttons[3].disabled = page === totalPages || totalPages === 0; // Last
        }
        
        function updateCounters(counts) {
            // Actualizar contadores de estado
            const statusAllBtn = document.querySelector('#status-all span:last-child');
            const statusPendingBtn = document.querySelector('#status-pending span:last-child');
            const statusApprovedBtn = document.querySelector('#status-approved span:last-child');
            const statusRejectedBtn = document.querySelector('#status-rejected span:last-child');
            
            if (statusAllBtn) statusAllBtn.textContent = counts.status.all;
            if (statusPendingBtn) statusPendingBtn.textContent = counts.status.pending;
            if (statusApprovedBtn) statusApprovedBtn.textContent = counts.status.approved;
            if (statusRejectedBtn) statusRejectedBtn.textContent = counts.status.rejected;
            
            // Actualizar contadores de rol
            const roleClienteBtn = document.querySelector('#role-cliente span:last-child');
            const roleProveedorBtn = document.querySelector('#role-proveedor span:last-child');
            const roleTransportistaBtn = document.querySelector('#role-transportista span:last-child');
            const roleOtrosBtn = document.querySelector('#role-otros span:last-child');
            
            if (roleClienteBtn) roleClienteBtn.textContent = counts.role.cliente;
            if (roleProveedorBtn) roleProveedorBtn.textContent = counts.role.proveedor;
            if (roleTransportistaBtn) roleTransportistaBtn.textContent = counts.role.transportista;
            if (roleOtrosBtn) roleOtrosBtn.textContent = counts.role.otros;
        }

        function updateFilterButtons(type, value) {
            const prefix = type === 'status' ? 'status-' : 'role-';
            
            document.querySelectorAll(`[id^="${prefix}"]`).forEach(btn => {
                btn.classList.remove('active');
                btn.style.transform = 'none';
            });
            
            const activeBtn = document.getElementById(prefix + value);
            if (activeBtn) {
                activeBtn.classList.add('active');
            }
        }

        // Variables globales para las gráficas
        let chartStatus, chartRole, chartTrend;
        <?php
        // Limpiar datos antes de convertir a JSON
        $formsClean = array_map(function($form) {
            return [
                'id' => $form['id'],
                'title' => $form['title'] ?? '',
                'approval_status' => $form['approval_status'] ?? 'pending',
                'role' => $form['role'] ?? 'cliente',
                'creator_name' => $form['creator_name'] ?? '',
                'company_name' => $form['company_name'] ?? '',
                'nit' => $form['nit'] ?? '',
                'created_at' => $form['created_at'] ?? ''
            ];
        }, $forms);
        ?>
        const allForms = <?= json_encode($formsClean, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;

        // Set initial filter state
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedFilters();
            initCharts();
            applyFilters();
        });

        // Inicializar gráficas
        function initCharts() {
            // Configuración común
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#1e293b',
                            font: { size: 11 }
                        }
                    }
                }
            };
            
            // Gráfica 1: Por Estado (Dona)
            chartStatus = new Chart(document.getElementById('chartStatus'), {
                type: 'doughnut',
                data: {
                    labels: ['Pendientes', 'Aprobados', 'Con Observaciones', 'Rechazados'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: [
                            'rgba(148, 163, 184, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgba(148, 163, 184, 1)',
                            'rgba(34, 197, 94, 1)',
                            'rgba(251, 191, 36, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '65%',
                    layout: {
                        padding: {
                            top: 5,
                            bottom: 5
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
            
            // Gráfica 2: Por Tipo (Barras horizontales)
            chartRole = new Chart(document.getElementById('chartRole'), {
                type: 'bar',
                data: {
                    labels: ['Cliente', 'Proveedor', 'Transportista'],
                    datasets: [{
                        label: 'Formularios',
                        data: [0, 0, 0],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(168, 85, 247, 0.8)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(251, 191, 36, 1)',
                            'rgba(168, 85, 247, 1)'
                        ],
                        borderWidth: 2,
                        barThickness: 35
                    }]
                },
                options: {
                    ...commonOptions,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { 
                                color: '#475569', 
                                font: { size: 10 },
                                stepSize: 1,
                                precision: 0
                            },
                            grid: { color: 'rgba(203, 213, 225, 0.5)' }
                        },
                        y: {
                            ticks: { color: '#1e293b', font: { size: 11 } },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.x || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return 'Total: ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
            
            // Gráfica 3: Tendencia (Línea)
            const today = new Date();
            const last30Days = [];
            for (let i = 29; i >= 0; i--) {
                const date = new Date(today);
                date.setDate(date.getDate() - i);
                last30Days.push(date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' }));
            }
            
            chartTrend = new Chart(document.getElementById('chartTrend'), {
                type: 'line',
                data: {
                    labels: last30Days,
                    datasets: [{
                        label: 'Formularios',
                        data: new Array(30).fill(0),
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)'
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        x: {
                            ticks: { 
                                color: '#475569', 
                                font: { size: 8 },
                                maxRotation: 90,
                                minRotation: 90,
                                autoSkip: true,
                                maxTicksLimit: 10
                            },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { 
                                color: '#475569', 
                                font: { size: 10 },
                                stepSize: 1,
                                precision: 0
                            },
                            grid: { color: 'rgba(203, 213, 225, 0.5)' }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
            
            // Actualizar con datos iniciales
            updateCharts();
        }
        
        // Actualizar gráficas basadas en filtros actuales
        function updateCharts() {
            // Verificar que las gráficas están inicializadas
            if (!chartStatus || !chartRole || !chartTrend) {
                console.log('Gráficas no inicializadas aún');
                return;
            }
            
            // Obtener fechas de filtro
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            
            // Filtrar formularios según los filtros actuales
            const filteredForms = allForms.filter(form => {
                const formRole = form.role || 'cliente';
                const searchData = (form.id + ' ' + form.title + ' ' + form.creator_name + ' ' + (form.company_name || '') + ' ' + (form.nit || '')).toLowerCase();
                const formDate = form.created_at.split(' ')[0];
                
                // Verificar filtro de estado
                const statusMatch = currentStatusFilter === 'all' || form.approval_status === currentStatusFilter;
                
                // Verificar filtro de rol
                let roleMatch = false;
                if (currentRoleFilter === 'all') {
                    roleMatch = true;
                } else if (currentRoleFilter === 'otros') {
                    roleMatch = !['cliente', 'proveedor', 'transportista'].includes(formRole);
                } else {
                    roleMatch = formRole === currentRoleFilter;
                }
                
                // Verificar búsqueda
                const searchMatch = currentSearchTerm === '' || searchData.includes(currentSearchTerm);
                
                // Verificar filtro de fechas
                let dateMatch = true;
                if (dateFrom && formDate < dateFrom) dateMatch = false;
                if (dateTo && formDate > dateTo) dateMatch = false;
                
                return statusMatch && roleMatch && searchMatch && dateMatch;
            });
            
            // Actualizar datos de estado
            const statusData = {
                pending: filteredForms.filter(f => f.approval_status === 'pending').length,
                approved: filteredForms.filter(f => f.approval_status === 'approved').length,
                approved_pending: filteredForms.filter(f => f.approval_status === 'approved_pending').length,
                rejected: filteredForms.filter(f => f.approval_status === 'rejected').length
            };
            
            chartStatus.data.datasets[0].data = [
                statusData.pending,
                statusData.approved,
                statusData.approved_pending,
                statusData.rejected
            ];
            chartStatus.update();
            
            // Actualizar leyenda personalizada - solo mostrar items con valor > 0
            const total = statusData.pending + statusData.approved + statusData.approved_pending + statusData.rejected;
            const legendData = [
                { label: 'Pendientes', value: statusData.pending, color: 'rgba(148, 163, 184, 1)' },
                { label: 'Aprobados', value: statusData.approved, color: 'rgba(34, 197, 94, 1)' },
                { label: 'Con Observaciones', value: statusData.approved_pending, color: 'rgba(251, 191, 36, 1)' },
                { label: 'Rechazados', value: statusData.rejected, color: 'rgba(239, 68, 68, 1)' }
            ].filter(item => item.value > 0); // Solo mostrar items con valor mayor a 0
            
            const legendHTML = legendData.map(item => {
                const percentage = total > 0 ? ((item.value / total) * 100).toFixed(1) : 0;
                return `
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background: ${item.color}; flex-shrink: 0;"></div>
                        <span style="color: #1e293b;">${item.label}: ${item.value} (${percentage}%)</span>
                    </div>
                `;
            }).join('');
            
            document.getElementById('chartStatusLegend').innerHTML = legendHTML || '<span style="color: #64748b; font-style: italic;">No hay datos</span>';
            
            // Actualizar datos de rol
            const roleData = {
                cliente: filteredForms.filter(f => (f.role || 'cliente') === 'cliente').length,
                proveedor: filteredForms.filter(f => (f.role || 'cliente') === 'proveedor').length,
                transportista: filteredForms.filter(f => (f.role || 'cliente') === 'transportista').length
            };
            
            chartRole.data.datasets[0].data = [
                roleData.cliente,
                roleData.proveedor,
                roleData.transportista
            ];
            chartRole.update();
            
            // Actualizar tendencia últimos 30 días
            const today = new Date();
            const trendData = [];
            
            for (let i = 29; i >= 0; i--) {
                const date = new Date(today);
                date.setDate(date.getDate() - i);
                const dateStr = date.toISOString().split('T')[0];
                
                const count = filteredForms.filter(f => {
                    const formDate = f.created_at.split(' ')[0];
                    return formDate === dateStr;
                }).length;
                
                trendData.push(count);
            }
            
            chartTrend.data.datasets[0].data = trendData;
            chartTrend.update();
        }

    </script>

    <!-- Modal de Firma Digital -->
    <div id="modalFirma" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(59, 130, 246, 0.5); border-radius: 1rem; padding: 2rem; max-width: 500px; width: 90%;">
            <h3 style="margin: 0 0 1.5rem; color: #e5e7eb;">Mi Firma Digital</h3>
            
            <?php
            // Verificar si tiene firma actual
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM firmas_digitales WHERE user_id = ? AND activa = 1 LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $firmaActual = $stmt->fetch();
            ?>
            
            <?php if ($firmaActual): ?>
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 0.5rem;">
                <p style="color: #86efac; margin: 0 0 0.5rem; font-weight: 600;">Firma actual:</p>
                <img src="/gestion-sagrilaft/public/ver_firma.php?user_id=<?= $_SESSION['user_id'] ?>&t=<?= time() ?>" 
                     alt="Firma actual" 
                     style="max-width: 200px; max-height: 100px; background: white; padding: 0.5rem; border-radius: 0.25rem;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <p style="color: #ef4444; display: none; margin: 0.5rem 0 0;">No se pudo cargar la imagen</p>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.5rem 0 0;">
                    Subida el <?= date('d/m/Y H:i', strtotime($firmaActual['created_at'])) ?>
                </p>
            </div>
            <?php else: ?>
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 0.5rem;">
                <p style="color: #fbbf24; margin: 0;">No tienes firma registrada</p>
            </div>
            <?php endif; ?>
            
            <form id="formFirma" method="POST" action="/gestion-sagrilaft/public/upload_firma_ajax.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="form-field">
                    <label for="firma"><?= $firmaActual ? 'Actualizar Firma' : 'Subir Firma' ?></label>
                    <input type="file" id="firma" name="firma" accept="image/png,image/jpeg,image/jpg" required>
                    <small style="color: #9ca3af;">PNG o JPG, máximo 2MB. Fondo transparente recomendado.</small>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="primary-button" style="flex: 1;">
                        <?= $firmaActual ? 'Actualizar Firma' : 'Guardar Firma' ?>
                    </button>
                    <button type="button" onclick="cerrarModalFirma()" class="secondary-button" style="flex: 1;">Cancelar</button>
                </div>
                
                <div id="firmaMessage" style="margin-top: 1rem; text-align: center;"></div>
            </form>
        </div>
    </div>
    
    <script>
        // Manejar envío del formulario de firma
        document.getElementById('formFirma').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageDiv = document.getElementById('firmaMessage');
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Deshabilitar botón
            submitBtn.disabled = true;
            submitBtn.textContent = 'Subiendo...';
            
            fetch('/gestion-sagrilaft/public/upload_firma_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = '<p style="color: #10b981; margin: 0;">' + data.message + '</p>';
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    messageDiv.innerHTML = '<p style="color: #ef4444; margin: 0;">' + data.message + '</p>';
                    submitBtn.disabled = false;
                    submitBtn.textContent = <?= json_encode($firmaActual ? 'Actualizar Firma' : 'Guardar Firma') ?>;
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<p style="color: #ef4444; margin: 0;">Error al subir la firma</p>';
                submitBtn.disabled = false;
                submitBtn.textContent = <?= json_encode($firmaActual ? 'Actualizar Firma' : 'Guardar Firma') ?>;
            });
        });
    </script>
</body>
</html>
