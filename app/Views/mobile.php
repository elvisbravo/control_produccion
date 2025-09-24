<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Producción ES Consultores</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome (adicional) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2a5298;
            --secondary-color: #1e3c72;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-header h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin: 0;
        }

        .nav-menu {
            padding: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            transition: all 0.3s ease;
            border: none;
            background: none;
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            border-left: 4px solid white;
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Submenu Styles */
        .submenu {
            background: rgba(0,0,0,0.2);
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .submenu.show {
            max-height: 300px;
        }

        .submenu .nav-link {
            padding-left: 3rem;
            font-size: 0.9rem;
        }

        .nav-link .arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .nav-link.collapsed .arrow {
            transform: rotate(-90deg);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .navbar {
            background: white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-bottom: 1px solid #e0e0e0;
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: 600;
            font-size: 1.5rem;
        }

        /* User Dropdown */
        .user-dropdown {
            position: relative;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease;
        }

        .user-info:hover {
            background-color: var(--light-bg);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Content Area */
        .content-wrapper {
            padding: 2rem;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card h3 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .stat-card p {
            color: #6c757d;
            margin: 0;
        }

        /* DateTime Display */
        .datetime-display {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .datetime-display .date {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .datetime-display .time {
            font-size: 2.5rem;
            font-weight: bold;
        }

        /* Status Colors for Tables */
        .table tbody tr.row-en-curso {
            background-color: #e3f2fd !important;
        }

        .table tbody tr.row-pausado {
            background-color: #fff9c4 !important;
        }

        .table tbody tr.row-corrigiendo {
            background-color: #fff9c4 !important;
        }

        .table tbody tr.row-completado {
            background-color: #e8f5e9 !important;
        }

        /* Calendar Styles */
        .calendar-container {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .calendar-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e0e0e0;
            border: 1px solid #e0e0e0;
        }

        .calendar-day {
            background: white;
            padding: 0.75rem;
            min-height: 100px;
            position: relative;
            cursor: pointer;
        }

        .calendar-day:hover {
            background: #f8f9fa;
        }

        .calendar-day-header {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem;
            text-align: center;
            font-weight: 500;
        }

        /* Sidebar Enhancements */
        .sidebar-header .btn-link {
            color: white !important;
            text-decoration: none;
            padding: 0.25rem !important;
        }

        .sidebar-header .btn-link:hover {
            color: rgba(255,255,255,0.8) !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 0.25rem;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 1rem;
            }

            .stat-card h3 {
                font-size: 1.5rem;
            }

            .datetime-display .time {
                font-size: 2rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
            }

            .content-wrapper {
                padding: 0.75rem;
            }

            .datetime-display {
                padding: 1.5rem;
            }

            .stat-card {
                padding: 1rem;
            }
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: var(--success-color);
            color: white;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
            z-index: 2000;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification.show {
            display: block;
        }

        /* Form Enhancements */
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25);
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25);
        }

        /* Button Enhancements */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Badge Enhancements */
        .badge-custom {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.25rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4>CONTROL DE PRODUCCIÓN</h4>
                    <p>ES Consultores</p>
                </div>
                <button class="btn btn-link text-white d-md-none p-1" id="closeSidebar">
                    <i class="bi bi-x-lg" style="font-size: 1.2rem;"></i>
                </button>
            </div>
        </div>
        
        <div class="nav-menu">
            <!-- Dashboard -->
            <div class="nav-item">
                <button class="nav-link active" data-tab="resumen">
                    <i class="bi bi-graph-up"></i>
                    <span>Dashboard</span>
                </button>
            </div>

            <!-- Gestión de Entregas -->
            <div class="nav-item">
                <button class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#entregasSubmenu">
                    <i class="bi bi-box-seam"></i>
                    <span>Gestión de Entregas</span>
                    <i class="bi bi-chevron-down arrow"></i>
                </button>
                <div class="collapse submenu" id="entregasSubmenu">
                    <button class="nav-link" data-tab="nueva-entrega">
                        <i class="bi bi-plus-circle"></i>
                        <span>Nueva Entrega</span>
                    </button>
                    <button class="nav-link" data-tab="control-entregas">
                        <i class="bi bi-list-check"></i>
                        <span>Control de Entregas</span>
                    </button>
                    <button class="nav-link" data-tab="calendario">
                        <i class="bi bi-calendar3"></i>
                        <span>Calendario</span>
                    </button>
                </div>
            </div>

            <!-- Reportes y Análisis -->
            <div class="nav-item">
                <button class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#reportesSubmenu">
                    <i class="bi bi-bar-chart"></i>
                    <span>Reportes y Análisis</span>
                    <i class="bi bi-chevron-down arrow"></i>
                </button>
                <div class="collapse submenu" id="reportesSubmenu">
                    <button class="nav-link" data-tab="reportes">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Reportes de Producción</span>
                    </button>
                    <button class="nav-link" data-tab="estadisticas">
                        <i class="bi bi-pie-chart"></i>
                        <span>Estadísticas</span>
                    </button>
                </div>
            </div>

            <!-- Configuración -->
            <div class="nav-item">
                <button class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#configSubmenu">
                    <i class="bi bi-gear"></i>
                    <span>Configuración</span>
                    <i class="bi bi-chevron-down arrow"></i>
                </button>
                <div class="collapse submenu" id="configSubmenu">
                    <button class="nav-link" data-tab="configuracion">
                        <i class="bi bi-sliders"></i>
                        <span>Configuración General</span>
                    </button>
                    <button class="nav-link" data-tab="horas-estandar">
                        <i class="bi bi-clock"></i>
                        <span>Horas Estándar</span>
                    </button>
                    <button class="nav-link" data-tab="papelera">
                        <i class="bi bi-trash"></i>
                        <span>Papelera</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary mobile-menu-btn me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>

                    <span class="navbar-brand mb-0 h2" id="pageTitle">Dashboard</span>
                </div>
                
                <div class="ms-auto">
                    <div class="dropdown user-dropdown">
                        <div class="user-info" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-semibold" id="supervisorDisplay">Hajid Celis Espinoza</div>
                                <small class="text-muted">Supervisor de Producción</small>
                            </div>
                            <i class="bi bi-chevron-down ms-2"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-tab="configuracion"><i class="bi bi-person-gear me-2"></i>Perfil</a></li>
                            <li><a class="dropdown-item" href="#" data-tab="configuracion"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-wrapper">
            <!-- Dashboard Tab -->
            <div id="resumen" class="tab-content active">
                <div class="datetime-display">
                    <div class="date" id="currentDate"></div>
                    <div class="time" id="currentTime"></div>
                </div>

                <!-- Toggle Buttons -->
                <div class="btn-group mb-4" role="group">
                    <input type="radio" class="btn-check" name="analysisType" id="btnDaily" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="btnDaily">Análisis Diario</label>
                    
                    <input type="radio" class="btn-check" name="analysisType" id="btnWeekly" autocomplete="off">
                    <label class="btn btn-outline-primary" for="btnWeekly">Análisis Semanal</label>
                </div>

                <!-- Stats Grid -->
                <div class="row mb-4" id="analysisDaily">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card">
                            <h3 id="dailyDelivered">0</h3>
                            <p>Entregados Hoy</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card">
                            <h3 id="dailyPending">0</h3>
                            <p>Pendientes Hoy</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card">
                            <h3 id="dailyInProgress">0</h3>
                            <p>En Progreso</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card">
                            <h3 id="dailyDelayed">0</h3>
                            <p>Retrasados</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4" id="analysisWeekly" style="display: none;">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card">
                            <h3 id="weeklyDelivered">0</h3>
                            <p>Entregados Semana</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card">
                            <h3 id="weeklyPending">0</h3>
                            <p>Pendientes Semana</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card">
                            <h3 id="weeklyInProgress">0</h3>
                            <p>En Progreso</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card">
                            <h3 id="weeklyDelayed">0</h3>
                            <p>Retrasados</p>
                        </div>
                    </div>
                </div>

                <!-- Delivery View Toggle -->
                <div class="btn-group mb-3" role="group">
                    <input type="radio" class="btn-check" name="deliveryView" id="btnToday" autocomplete="off" checked>
                    <label class="btn btn-outline-success" for="btnToday">Entregas de Hoy</label>
                    
                    <input type="radio" class="btn-check" name="deliveryView" id="btnWeek" autocomplete="off">
                    <label class="btn btn-outline-success" for="btnWeek">Entregas de la Semana</label>
                </div>

                <!-- Deliveries Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Entregas Programadas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="deliveriesTable">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Cliente</th>
                                        <th>Tarea</th>
                                        <th>Auxiliar</th>
                                        <th>Estado</th>
                                        <th class="text-center">Entregado</th>
                                    </tr>
                                </thead>
                                <tbody id="deliveriesTbody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No hay entregas programadas</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nueva Entrega Tab -->
            <div id="nueva-entrega" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Agregar Nueva Entrega</h5>
                    </div>
                    <div class="card-body">
                        <form id="newDeliveryForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha y Hora de Inicio</label>
                                    <input type="datetime-local" class="form-control" id="startDatetime" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Auxiliar</label>
                                    <select class="form-select" id="auxiliarSelect" required>
                                        <option value="">Seleccione un auxiliar</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Tarea</label>
                                    <select class="form-select" id="taskTypeSelect" required>
                                        <option value="">Seleccione una tarea</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Título de la Entrega</label>
                                    <input type="text" class="form-control" id="deliveryTitle" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" class="form-control" id="clientName" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tiempo Estimado (HH:MM)</label>
                                    <input type="text" class="form-control" id="estimatedTime" placeholder="05:00" pattern="[0-9]{2}:[0-9]{2}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" id="statusSelect" required>
                                        <option value="en-curso">En Curso</option>
                                        <option value="pausado">Pausado</option>
                                        <option value="completado">Completado</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha y Hora de Entrega Calculada</label>
                                    <input type="datetime-local" class="form-control" id="calculatedDelivery" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Link de Google Drive</label>
                                    <input type="url" class="form-control" id="driveLink">
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observations" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg me-1"></i>Guardar Entrega
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="clearForm">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Control de Entregas Tab -->
            <div id="control-entregas" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0"><i class="bi bi-list-check me-2"></i>Control de Entregas</h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-success btn-sm" id="exportControl">
                                    <i class="bi bi-download me-1"></i>Exportar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control" id="searchClient" placeholder="🔍 Buscar por cliente...">
                            </div>
                            <div class="col-md-3 mb-2">
                                <select class="form-select" id="filterPeriod">
                                    <option value="all">Todos los periodos</option>
                                    <option value="day">Día</option>
                                    <option value="week">Semana</option>
                                    <option value="month">Mes</option>
                                    <option value="year">Año</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="date" class="form-control" id="filterDate">
                            </div>
                            <div class="col-md-2 mb-2">
                                <button class="btn btn-primary w-100" id="applyFilters">Filtrar</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Tipo de Tarea</th>
                                        <th>Cliente</th>
                                        <th>Auxiliar</th>
                                        <th>Estado</th>
                                        <th>Link Drive</th>
                                        <th>Observaciones</th>
                                        <th>Entrega</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="controlTbody">
                                    <tr>
                                        <td colspan="9" class="text-center py-4">No hay entregas registradas</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination justify-content-center" id="controlPagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Calendario Tab -->
            <div id="calendario" class="tab-content">
                <div class="calendar-container">
                    <div class="calendar-header d-flex justify-content-between align-items-center">
                        <button class="btn btn-light" id="prevMonth">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </button>
                        <h2 id="calendarMonthYear" class="mb-0"></h2>
                        <button class="btn btn-light" id="nextMonth">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <div class="calendar-grid" id="calendarGrid"></div>
                </div>
            </div>

            <!-- Estadísticas Tab -->
            <div id="estadisticas" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-pie-chart me-2"></i>Estadísticas de Producción</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Productividad por Auxiliar</h6>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-success" style="width: 85%">85%</div>
                                        </div>
                                        <small class="text-muted">Promedio mensual</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Entregas a Tiempo</h6>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-info" style="width: 92%">92%</div>
                                        </div>
                                        <small class="text-muted">Último mes</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <h6>Distribución de Tareas por Tipo</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tipo de Tarea</th>
                                                <th>Cantidad</th>
                                                <th>Porcentaje</th>
                                                <th>Progreso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Proyectos</td>
                                                <td>45</td>
                                                <td>35%</td>
                                                <td>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-primary" style="width: 35%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Informes</td>
                                                <td>38</td>
                                                <td>30%</td>
                                                <td>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-success" style="width: 30%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Correcciones</td>
                                                <td>32</td>
                                                <td>25%</td>
                                                <td>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-warning" style="width: 25%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Otros</td>
                                                <td>13</td>
                                                <td>10%</td>
                                                <td>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-info" style="width: 10%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reportes Tab -->
            <div id="reportes" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reportes de Producción</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3 mb-2">
                                <select class="form-select" id="reportType">
                                    <option value="daily">Reporte Diario</option>
                                    <option value="weekly">Reporte Semanal</option>
                                    <option value="monthly">Reporte Mensual</option>
                                    <option value="individual">Reporte Individual</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="date" class="form-control" id="reportDate">
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-primary w-100" id="generateReport">
                                    <i class="bi bi-file-earmark-text me-1"></i>Generar
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-success w-100" id="exportReport">
                                    <i class="bi bi-download me-1"></i>Exportar
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Auxiliar</th>
                                        <th>Total Tareas</th>
                                        <th>Completadas</th>
                                        <th>Pendientes</th>
                                        <th>A Tiempo</th>
                                        <th>Antes de Tiempo</th>
                                        <th>Retrasadas</th>
                                        <th>Eficiencia</th>
                                    </tr>
                                </thead>
                                <tbody id="reportsTbody">
                                    <tr>
                                        <td>Juan Pérez</td>
                                        <td>25</td>
                                        <td>22</td>
                                        <td>3</td>
                                        <td>18</td>
                                        <td>3</td>
                                        <td>1</td>
                                        <td><span class="badge bg-success">88%</span></td>
                                    </tr>
                                    <tr>
                                        <td>María García</td>
                                        <td>20</td>
                                        <td>19</td>
                                        <td>1</td>
                                        <td>16</td>
                                        <td>2</td>
                                        <td>1</td>
                                        <td><span class="badge bg-success">95%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Carlos López</td>
                                        <td>18</td>
                                        <td>15</td>
                                        <td>3</td>
                                        <td>12</td>
                                        <td>2</td>
                                        <td>1</td>
                                        <td><span class="badge bg-warning">83%</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración Tab Mejorada -->
            <div id="configuracion" class="tab-content">
                <div class="row">
                    <!-- Supervisor Section -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-person-gear me-2"></i>Supervisor de Producción</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre del Supervisor</label>
                                        <input type="text" class="form-control" id="supervisorName" value="Hajid Celis Espinoza">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Cargo</label>
                                        <input type="text" class="form-control" id="supervisorPosition" value="Supervisor de Producción">
                                    </div>
                                </div>
                                <button class="btn btn-primary" id="saveSupervisor">
                                    <i class="bi bi-check-lg me-1"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Auxiliares Section -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="bi bi-people me-2"></i>Auxiliares de Producción</h5>
                                <button class="btn btn-success btn-sm" id="addAuxiliarBtn">
                                    <i class="bi bi-person-plus me-1"></i>Agregar Auxiliar
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Cargo</th>
                                                <th>Email</th>
                                                <th>Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="auxiliaresTbody">
                                            <tr>
                                                <td>Juan Pérez</td>
                                                <td>Auxiliar Senior</td>
                                                <td>juan.perez@email.com</td>
                                                <td><span class="badge bg-success">Activo</span></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Section -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-shield-check me-2"></i>Respaldo y Restauración</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> Realiza respaldos regulares para evitar pérdida de información.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6>Crear Respaldo</h6>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-primary" id="createBackup">
                                                <i class="bi bi-download me-1"></i>Respaldo JSON
                                            </button>
                                            <button class="btn btn-info" id="createTextBackup">
                                                <i class="bi bi-file-text me-1"></i>Respaldo Texto
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6>Restaurar Datos</h6>
                                        <div class="d-flex gap-2">
                                            <input type="file" class="form-control" id="restoreFile" accept=".json,.txt" style="display: none;">
                                            <button class="btn btn-warning" id="restoreBtn">
                                                <i class="bi bi-upload me-1"></i>Restaurar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horas Estándar Tab Mejorada -->
            <div id="horas-estandar" class="tab-content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-clock me-2"></i>Gestión de Horas Estándar</h5>
                        <button class="btn btn-success btn-sm" id="addTaskBtn">
                            <i class="bi bi-plus-circle me-1"></i>Nueva Tarea
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Proyectos -->
                            <div class="col-md-6 mb-4">
                                <div class="card bg-light">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="bi bi-folder me-2"></i>Proyectos (24 horas)</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Tarea</th>
                                                        <th class="text-center">Horas</th>
                                                        <th class="text-center">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Análisis Inicial</td>
                                                        <td class="text-center">4</td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Desarrollo</td>
                                                        <td class="text-center">16</td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Revisión Final</td>
                                                        <td class="text-center">4</td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informes -->
                            <div class="col-md-6 mb-4">
                                <div class="card bg-light">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Informes (12 horas)</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Tarea</th>
                                                        <th class="text-center">Horas</th>
                                                        <th class="text-center">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Investigación</td>
                                                        <td class="text-center">3</td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Redacción</td>
                                                        <td class="text-center">6</td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Revisión</td>
                                                        <td class="text-center">3</td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Papelera Tab Mejorada -->
            <div id="papelera" class="tab-content">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="bi bi-trash me-2"></i>Auxiliares Eliminados</h5>
                                <button class="btn btn-danger btn-sm" id="emptyTrashAuxiliares">
                                    <i class="bi bi-trash me-1"></i>Vaciar Papelera
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Cargo</th>
                                                <th>Fecha de Eliminación</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="deletedAuxiliaresTbody">
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox display-6"></i>
                                                    <br>No hay auxiliares eliminados
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="bi bi-box-seam me-2"></i>Entregas Eliminadas</h5>
                                <button class="btn btn-danger btn-sm" id="emptyTrashDeliveries">
                                    <i class="bi bi-trash me-1"></i>Vaciar Papelera
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Tarea</th>
                                                <th>Auxiliar</th>
                                                <th>Fecha de Eliminación</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="deletedDeliveriesTbody">
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox display-6"></i>
                                                    <br>No hay entregas eliminadas
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sistema de navegación
        document.addEventListener('DOMContentLoaded', function() {
            // Variables globales
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const pageTitle = document.getElementById('pageTitle');
            
            // Toggle sidebar en móvil
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });

            // Cerrar sidebar al hacer click en overlay
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Cerrar sidebar con botón X en móvil
            const closeSidebar = document.getElementById('closeSidebar');
            closeSidebar.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Navegación entre tabs
            document.addEventListener('click', function(e) {
                const button = e.target.closest('[data-tab]');
                if (button) {
                    e.preventDefault();
                    
                    const targetTab = button.getAttribute('data-tab');
                    
                    // Ocultar todos los contenidos
                    document.querySelectorAll('.tab-content').forEach(tab => {
                        tab.classList.remove('active');
                    });
                    
                    // Mostrar el contenido seleccionado
                    const activeTab = document.getElementById(targetTab);
                    if (activeTab) {
                        activeTab.classList.add('active');
                    }
                    
                    // Actualizar navegación activa - solo para botones principales del sidebar
                    if (button.closest('.sidebar')) {
                        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                            link.classList.remove('active');
                        });
                        button.classList.add('active');
                    }
                    
                    // Actualizar título de página
                    const titles = {
                        'resumen': 'Dashboard',
                        'nueva-entrega': 'Nueva Entrega',
                        'control-entregas': 'Control de Entregas',
                        'calendario': 'Calendario',
                        'reportes': 'Reportes de Producción',
                        'estadisticas': 'Estadísticas',
                        'configuracion': 'Configuración General',
                        'horas-estandar': 'Horas Estándar',
                        'papelera': 'Papelera'
                    };
                    pageTitle.textContent = titles[targetTab] || 'Sistema';
                    
                    // Cerrar sidebar en móvil después de seleccionar
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                }
            });

            // Reloj en tiempo real
            function updateDateTime() {
                const now = new Date();
                const dateElement = document.getElementById('currentDate');
                const timeElement = document.getElementById('currentTime');
                
                if (dateElement && timeElement) {
                    const options = { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    };
                    
                    dateElement.textContent = now.toLocaleDateString('es-ES', options);
                    timeElement.textContent = now.toLocaleTimeString('es-ES');
                }
            }

            // Actualizar cada segundo
            updateDateTime();
            setInterval(updateDateTime, 1000);

            // Toggle entre análisis diario y semanal
            document.getElementById('btnDaily')?.addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById('analysisDaily').style.display = 'flex';
                    document.getElementById('analysisWeekly').style.display = 'none';
                }
            });

            document.getElementById('btnWeekly')?.addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById('analysisDaily').style.display = 'none';
                    document.getElementById('analysisWeekly').style.display = 'flex';
                }
            });

            // Gestión responsiva del sidebar
            function handleResize() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            }

            window.addEventListener('resize', handleResize);
        });

        // Función de cerrar sesión
        function logout() {
            if (confirm('¿Está seguro que desea cerrar sesión?')) {
                // Aquí implementarías la lógica de cerrar sesión
                alert('Sesión cerrada exitosamente');
                // window.location.href = '/login';
            }
        }

        // Función para mostrar notificaciones
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Inicializar tooltips de Bootstrap (si se necesitan)
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>