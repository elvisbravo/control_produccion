<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Producción ES Consultores</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            height: 100vh;
            overflow: hidden;
            font-size: 14px;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 200px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .logo-section {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo-section h2 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .logo-section p {
            font-size: 12px;
            opacity: 0.8;
        }

        .nav-menu {
            flex: 1;
            padding: 20px 0;
        }

        .nav-item {
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 14px;
            transition: background 0.3s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
        }

        .nav-item.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid white;
        }

        .nav-item i {
            margin-right: 10px;
            font-size: 16px;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
            overflow: hidden;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
            color: #2a5298;
        }

        .supervisor-info {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .content {
            flex: 1;
            padding: 20px 30px;
            overflow-y: auto;
            background: #fafafa;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 4px solid #2a5298;
        }

        .stat-card h3 {
            font-size: 32px;
            color: #2a5298;
            margin-bottom: 10px;
        }

        .stat-card p {
            color: #666;
            font-size: 14px;
        }

        table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        thead {
            background: #2a5298;
            color: white;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
            vertical-align: middle;
        }

        th:last-child {
            text-align: center;
            width: 220px;
        }

        td:last-child {
            text-align: center;
            padding: 8px;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:hover {
            opacity: 0.9;
        }

        /* COLORES PARA FILAS EN CONTROL DE ENTREGAS */
        tr.row-en-curso {
            background-color: #e3f2fd !important;
        }

        tr.row-pausado {
            background-color: #fff9c4 !important;
        }

        tr.row-corrigiendo {
            background-color: #fff9c4 !important;
        }

        tr.row-completado {
            background-color: #e8f5e9 !important;
        }

        td:first-child {
            font-weight: 500;
        }

        .task-section table th:nth-child(2),
        .task-section table td:nth-child(2) {
            text-align: center;
            width: 120px;
            padding: 12px 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            min-width: 75px;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            margin: 0 3px;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: #2a5298;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .action-buttons {
            display: inline-flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }

        td select {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            min-width: 120px;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        td select:hover {
            border-color: #2a5298;
        }

        td select:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 2px rgba(42, 82, 152, 0.1);
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .calendar-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: #2a5298;
            color: white;
            padding: 15px;
            border-radius: 4px;
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
            padding: 10px;
            min-height: 100px;
            position: relative;
            cursor: pointer;
        }

        .calendar-day:hover {
            background: #f0f0f0;
        }

        .calendar-day-header {
            background: #2a5298;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: 500;
        }

        .calendar-day-number {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .calendar-task {
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 11px;
            margin-bottom: 2px;
            cursor: pointer;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .datetime-display {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .datetime-display .date {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .datetime-display .time {
            font-size: 28px;
            font-weight: bold;
        }

        .toggle-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .toggle-btn {
            padding: 10px 20px;
            background: white;
            border: 2px solid #2a5298;
            color: #2a5298;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .toggle-btn.active {
            background: #2a5298;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #2a5298;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: #28a745;
            color: white;
            border-radius: 4px;
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

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 5px 10px;
            border: 1px solid #2a5298;
            background: white;
            color: #2a5298;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination button.active {
            background: #2a5298;
            color: white;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alarm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }

        .alarm-modal.active {
            display: flex;
        }

        .alarm-content {
            background: linear-gradient(135deg, #ff6b6b, #ff4444);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 0 50px rgba(255, 68, 68, 0.5);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .alarm-content h1 {
            color: white;
            font-size: 36px;
            margin-bottom: 20px;
        }

        .alarm-content .delivery-info {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            color: white;
        }

        .alarm-content .delivery-info p {
            margin: 10px 0;
            font-size: 18px;
        }

        .alarm-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .alarm-buttons button {
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-accept-alarm {
            background: #28a745;
            color: white;
        }

        .btn-postpone-alarm {
            background: #ffc107;
            color: #212529;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }

        .search-bar input {
            flex: 1;
            max-width: 300px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-bar input:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
        }

        .config-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .config-section h3 {
            color: #2a5298;
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }

        .task-section {
            margin-bottom: 30px;
        }

        .task-section h3 {
            color: #2a5298;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo-section">
                <h2>CONTROL DE PRODUCCIÓN</h2>
                <p>ES Consultores</p>
            </div>
            <nav class="nav-menu">
                <div class="nav-item active" data-tab="resumen">
                    <i>📊</i> <span>Resumen</span>
                </div>
                <div class="nav-item" data-tab="calendario">
                    <i>📅</i> <span>Calendario</span>
                </div>
                <div class="nav-item" data-tab="nueva-entrega">
                    <i>➕</i> <span>Nueva Entrega</span>
                </div>
                <div class="nav-item" data-tab="control-entregas">
                    <i>📋</i> <span>Control de Entregas</span>
                </div>
                <div class="nav-item" data-tab="reportes">
                    <i>📈</i> <span>Reportes</span>
                </div>
                <div class="nav-item" data-tab="papelera">
                    <i>🗑️</i> <span>Papelera</span>
                </div>
                <div class="nav-item" data-tab="horas-estandar">
                    <i>⏰</i> <span>Horas Estándar</span>
                </div>
                <div class="nav-item" data-tab="configuracion">
                    <i>⚙️</i> <span>Configuración</span>
                </div>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <h1 id="page-title">Resumen</h1>
                <div class="supervisor-info" id="supervisor-display">Supervisor de Producción: Hajid Celis Espinoza</div>
            </div>

            <div class="content">
                <div id="resumen" class="tab-content active">
                    <div class="datetime-display">
                        <div class="date" id="current-date"></div>
                        <div class="time" id="current-time"></div>
                    </div>

                    <div class="toggle-buttons">
                        <button class="toggle-btn active" id="btn-daily">Análisis Diario</button>
                        <button class="toggle-btn" id="btn-weekly">Análisis Semanal</button>
                    </div>

                    <div id="analysis-daily" class="stats-grid">
                        <div class="stat-card">
                            <h3 id="daily-delivered">0</h3>
                            <p>Entregados Hoy</p>
                        </div>
                        <div class="stat-card">
                            <h3 id="daily-pending">0</h3>
                            <p>Pendientes Hoy</p>
                        </div>
                    </div>

                    <div id="analysis-weekly" class="stats-grid" style="display: none;">
                        <div class="stat-card">
                            <h3 id="weekly-delivered">0</h3>
                            <p>Entregados Semana</p>
                        </div>
                        <div class="stat-card">
                            <h3 id="weekly-pending">0</h3>
                            <p>Pendientes Semana</p>
                        </div>
                    </div>

                    <div class="toggle-buttons">
                        <button class="toggle-btn active" id="btn-today">Entregas de Hoy</button>
                        <button class="toggle-btn" id="btn-week">Entregas de la Semana</button>
                    </div>

                    <div id="deliveries-today">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Cliente</th>
                                    <th>Tarea</th>
                                    <th>Auxiliar</th>
                                    <th>Estado</th>
                                    <th>Entregado</th>
                                </tr>
                            </thead>
                            <tbody id="today-deliveries-tbody">
                                <tr>
                                    <td colspan="6" style="text-align: center;">No hay entregas pendientes para hoy</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="deliveries-week" style="display: none;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Cliente</th>
                                    <th>Tarea</th>
                                    <th>Auxiliar</th>
                                    <th>Estado</th>
                                    <th>Entregado</th>
                                </tr>
                            </thead>
                            <tbody id="week-deliveries-tbody">
                                <tr>
                                    <td colspan="6" style="text-align: center;">No hay entregas pendientes esta semana</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="calendario" class="tab-content">
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <button class="btn btn-primary" id="prev-month">← Anterior</button>
                            <h2 id="calendar-month-year"></h2>
                            <button class="btn btn-primary" id="next-month">Siguiente →</button>
                        </div>
                        <div class="calendar-grid" id="calendar-grid"></div>
                    </div>
                </div>

                <div id="nueva-entrega" class="tab-content">
                    <div class="config-section">
                        <h3>Agregar Nueva Entrega</h3>
                        <form id="new-delivery-form">
                            <div class="form-group">
                                <label>Fecha y Hora de Inicio</label>
                                <input type="datetime-local" id="start-datetime" required autocomplete="off">
                            </div>
                            
                            <div class="form-group">
                                <label>Auxiliar</label>
                                <select id="auxiliar-select" required>
                                    <option value="">Seleccione un auxiliar</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tipo de Tarea</label>
                                <select id="task-type-select" required>
                                    <option value="">Seleccione una tarea</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Título de la Entrega</label>
                                <input type="text" id="delivery-title" required autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>Cliente</label>
                                <input type="text" id="client-name" required autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>Tiempo Estimado (HH:MM)</label>
                                <input type="text" id="estimated-time" placeholder="05:00" pattern="[0-9]{2}:[0-9]{2}" required autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>Estado</label>
                                <select id="status-select" required>
                                    <option value="en-curso">En Curso</option>
                                    <option value="pausado">Pausado</option>
                                    <option value="completado">Completado</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Link de Google Drive</label>
                                <input type="url" id="drive-link" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>Fecha y Hora de Entrega Calculada</label>
                                <input type="datetime-local" id="calculated-delivery" required autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea id="observations" rows="3" autocomplete="off"></textarea>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-success">Guardar Entrega</button>
                                <button type="button" class="btn btn-danger" id="clear-form">Limpiar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="control-entregas" class="tab-content">
                    <div class="search-bar">
                        <input type="text" id="search-client" placeholder="🔍 Buscar por cliente (nombre o apellido)...">
                        <select id="filter-period">
                            <option value="all">Todos los periodos</option>
                            <option value="day">Día</option>
                            <option value="week">Semana</option>
                            <option value="month">Mes</option>
                            <option value="year">Año</option>
                        </select>
                        <input type="date" id="filter-date">
                        <button class="btn btn-primary" id="apply-filters">Filtrar</button>
                        <button class="btn btn-success" id="export-control">Exportar Control</button>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Tipo de Tarea</th>
                                <th>Cliente</th>
                                <th>Auxiliar</th>
                                <th>Estado</th>
                                <th>Link Drive</th>
                                <th>Observaciones</th>
                                <th>Entrega</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="control-tbody">
                            <tr>
                                <td colspan="9" style="text-align: center;">No hay entregas registradas</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="pagination" id="control-pagination"></div>
                </div>

                <div id="reportes" class="tab-content">
                    <div class="config-section">
                        <h3>Reportes de Producción</h3>
                        
                        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                            <select id="report-type">
                                <option value="daily">Reporte Diario</option>
                                <option value="weekly">Reporte Semanal</option>
                                <option value="individual">Reporte Individual</option>
                            </select>
                            <input type="date" id="report-date">
                            <button class="btn btn-primary" id="generate-report">Generar Reporte</button>
                            <button class="btn btn-success" id="export-report">Exportar Reporte</button>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Auxiliar</th>
                                    <th>Total</th>
                                    <th>Completadas</th>
                                    <th>Pendientes</th>
                                    <th>A Tiempo</th>
                                    <th>Antes</th>
                                    <th>Después</th>
                                </tr>
                            </thead>
                            <tbody id="reports-tbody">
                                <tr>
                                    <td colspan="7" style="text-align: center;">Seleccione un tipo de reporte</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="papelera" class="tab-content">
                    <div class="config-section">
                        <h3>Auxiliares Eliminados</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                    <th>Fecha de Eliminación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="deleted-auxiliares-tbody">
                                <tr>
                                    <td colspan="4" style="text-align: center;">No hay auxiliares eliminados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="config-section">
                        <h3>Entregas Eliminadas</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Tarea</th>
                                    <th>Fecha de Eliminación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="deleted-deliveries-tbody">
                                <tr>
                                    <td colspan="4" style="text-align: center;">No hay entregas eliminadas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="horas-estandar" class="tab-content">
                    <div class="config-section">
                        <h3>Gestión de Horas Estándar por Tarea</h3>
                        <button class="btn btn-success" id="add-task-btn" style="margin-bottom: 20px;">➕ Agregar Nueva Tarea</button>
                    </div>
                    
                    <div class="task-section">
                        <h3>📋 Proyecto (24 horas)</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Horas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="project-tasks-tbody"></tbody>
                        </table>
                    </div>

                    <div class="task-section">
                        <h3>📝 Informe (12 horas)</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Horas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="report-tasks-tbody"></tbody>
                        </table>
                    </div>

                    <div class="task-section">
                        <h3>✏️ Tareas de Corrección</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Horas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="correction-tasks-tbody"></tbody>
                        </table>
                    </div>

                    <div class="task-section">
                        <h3>📚 Elaboración Completa</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Horas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="complete-tasks-tbody"></tbody>
                        </table>
                    </div>

                    <div class="task-section">
                        <h3>🎨 Temas de Forma</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Horas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="form-tasks-tbody"></tbody>
                        </table>
                    </div>

                    <div class="task-section">
                        <h3>✅ Culminar y Avances</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Horas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="finish-tasks-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <div id="configuracion" class="tab-content">
                    <div class="config-section">
                        <h3>👤 Supervisor de Producción</h3>
                        <div class="form-group">
                            <label>Nombre del Supervisor</label>
                            <input type="text" id="supervisor-name" value="Hajid Celis Espinoza">
                        </div>
                        <div class="form-group">
                            <label>Cargo</label>
                            <input type="text" id="supervisor-position" value="Supervisor de Producción">
                        </div>
                        <button class="btn btn-primary" id="save-supervisor">Guardar Cambios de Supervisor</button>
                    </div>
                    
                    <div class="config-section">
                        <h3>👥 Gestión de Auxiliares de Producción</h3>
                        <button class="btn btn-success" id="add-auxiliar-btn" style="margin-bottom: 20px;">➕ Agregar Auxiliar</button>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="auxiliares-tbody"></tbody>
                        </table>
                    </div>

                    <div class="config-section">
                        <h3>💾 Respaldo y Restauración de Datos</h3>
                        <p style="margin-bottom: 15px;">⚠️ <strong>Importante:</strong> Guarda regularmente un respaldo de tus datos para evitar pérdida de información.</p>
                        
                        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                            <button class="btn btn-primary" id="create-backup">💾 Guardar Respaldo JSON</button>
                            <button class="btn btn-info" id="create-text-backup">📄 Respaldo en Texto</button>
                        </div>
                        
                        <div>
                            <input type="file" id="restore-file" accept=".json,.txt" style="display: none;">
                            <button class="btn btn-warning" id="restore-btn">🔄 Restaurar Datos</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="notification" class="notification"></div>

    <div id="alarm-modal" class="alarm-modal">
        <div class="alarm-content">
            <h1>⚠️ ALERTA DE ENTREGA ⚠️</h1>
            <div class="delivery-info">
                <p><strong>Cliente:</strong> <span id="alarm-client"></span></p>
                <p><strong>Tarea:</strong> <span id="alarm-task"></span></p>
                <p><strong>Auxiliar:</strong> <span id="alarm-auxiliar"></span></p>
                <p><strong>Hora de Entrega:</strong> <span id="alarm-time"></span></p>
                <p><strong>Tiempo Restante:</strong> <span id="alarm-remaining"></span></p>
            </div>
            <div class="alarm-buttons">
                <button class="btn-accept-alarm" onclick="acceptAlarm()">✓ ACEPTAR</button>
                <button class="btn-postpone-alarm" onclick="postponeAlarm()">⏰ POSPONER 1 MIN</button>
            </div>
        </div>
    </div>

    <script>
        let deliveries = [];
        let auxiliares = [];
        let deletedDeliveries = [];
        let deletedAuxiliares = [];
        let standardTasks = {};
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let currentPage = 1;
        const itemsPerPage = 10;
        let supervisorInfo = {
            name: 'Hajid Celis Espinoza',
            position: 'Supervisor de Producción'
        };
        let activeAlarms = {};
        let clientSearchQuery = '';

        const peruHolidays = [
            '2025-01-01', '2025-04-17', '2025-04-18', '2025-05-01',
            '2025-06-29', '2025-07-28', '2025-07-29', '2025-08-30',
            '2025-10-08', '2025-11-01', '2025-12-08', '2025-12-25'
        ];

        document.addEventListener('DOMContentLoaded', function() {
            loadFromLocalStorage();
            initializeAuxiliares();
            initializeStandardTasks();
            initializeEventListeners();
            updateDateTime();
            setInterval(updateDateTime, 1000);
            setInterval(checkDeliveryAlarms, 10000);
            updateAllTabs();
            
            const now = new Date();
            const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
            document.getElementById('start-datetime').value = localDateTime;
        });

        function initializeAuxiliares() {
            if (auxiliares.length === 0) {
                auxiliares = [
                    { id: 1, name: 'Alejandra Torres', position: 'Auxiliar de Producción' },
                    { id: 2, name: 'Diana Sotelo', position: 'Auxiliar de Producción' },
                    { id: 3, name: 'Grecia Salazar', position: 'Auxiliar de Producción' },
                    { id: 4, name: 'Hajid Celis', position: 'Supervisor de Producción' },
                    { id: 5, name: 'Jessica Martínez', position: 'Auxiliar de Producción' },
                    { id: 6, name: 'José Cipriano', position: 'Auxiliar de Producción' },
                    { id: 7, name: 'Karliany Rodríguez', position: 'Auxiliar de Producción' },
                    { id: 8, name: 'Kateryn Pruizaca', position: 'Auxiliar de Producción' },
                    { id: 9, name: 'Luz Coa', position: 'Auxiliar de Producción' },
                    { id: 10, name: 'Miccy Bonilla', position: 'Auxiliar de Producción' },
                    { id: 11, name: 'Nicol Roldán', position: 'Auxiliar de Producción' },
                    { id: 12, name: 'Rafael Díaz', position: 'Ventas y Producción' }
                ];
                saveToLocalStorage();
            }
        }

        function initializeStandardTasks() {
            if (Object.keys(standardTasks).length === 0) {
                standardTasks = {
                    proyecto: [
                        { name: 'Matriz de consistencia', hours: '02:00' },
                        { name: 'Realidad problemática', hours: '03:00' },
                        { name: 'Antecedentes', hours: '05:00' },
                        { name: 'Bases teóricas', hours: '09:00' },
                        { name: 'Metodología', hours: '02:00' },
                        { name: 'Justificación, problemas, objetivos, hipótesis, limitaciones + aspectos administrativos', hours: '02:00' },
                        { name: 'Instrumentos', hours: '01:00' }
                    ],
                    informe: [
                        { name: 'Resultados (estándar)', hours: '05:00' },
                        { name: 'Discusión', hours: '03:00' },
                        { name: 'Conclusiones y recomendaciones', hours: '02:00' },
                        { name: 'Cambio de formato + redacción en pasado, índices + resumen y abstract, agregar anexos', hours: '02:00' }
                    ],
                    correccion: [
                        { name: 'Corregir obs', hours: '01:00' },
                        { name: 'Agregar requerimientos', hours: '01:00' }
                    ],
                    elaboracion: [
                        { name: 'Elaboración de proyecto completo', hours: '24:00' },
                        { name: 'Elaboración de informe completo', hours: '12:00' },
                        { name: 'Elaboración del artículo revisión sistemática', hours: '36:00' },
                        { name: 'Elaboración del artículo investigación original', hours: '26:00' },
                        { name: 'Elaboración de articulo tesis en formato artículo', hours: '12:00' }
                    ],
                    forma: [
                        { name: 'Citar de nuevo', hours: '01:00' },
                        { name: 'Bajar plagio', hours: '01:00' },
                        { name: 'Reducir IA', hours: '01:00' }
                    ],
                    culminar: [
                        { name: 'Proyecto', hours: '01:00' },
                        { name: 'Informe', hours: '01:00' },
                        { name: 'Artículo', hours: '01:00' },
                        { name: 'Avance', hours: '01:00' }
                    ]
                };
                saveToLocalStorage();
            }
        }

        function initializeEventListeners() {
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', function() {
                    switchTab(this.getAttribute('data-tab'));
                });
            });

            document.getElementById('btn-daily').addEventListener('click', () => toggleAnalysis('daily'));
            document.getElementById('btn-weekly').addEventListener('click', () => toggleAnalysis('weekly'));
            document.getElementById('btn-today').addEventListener('click', () => toggleDeliveries('today'));
            document.getElementById('btn-week').addEventListener('click', () => toggleDeliveries('week'));

            document.getElementById('prev-month').addEventListener('click', () => changeMonth(-1));
            document.getElementById('next-month').addEventListener('click', () => changeMonth(1));

            document.getElementById('new-delivery-form').addEventListener('submit', handleNewDelivery);
            document.getElementById('clear-form').addEventListener('click', clearNewDeliveryForm);
            document.getElementById('task-type-select').addEventListener('change', updateEstimatedTime);
            document.getElementById('start-datetime').addEventListener('change', calculateDeliveryTime);
            document.getElementById('estimated-time').addEventListener('input', calculateDeliveryTime);

            document.getElementById('search-client').addEventListener('input', function(e) {
                clientSearchQuery = e.target.value.toLowerCase();
                currentPage = 1;
                updateControlTable();
            });
            document.getElementById('export-control').addEventListener('click', exportControl);
            document.getElementById('apply-filters').addEventListener('click', applyFilters);
            
            document.getElementById('filter-period').addEventListener('change', function() {
                if (this.value === 'all') {
                    document.getElementById('filter-date').value = '';
                    updateControlTable();
                } else {
                    if (document.getElementById('filter-date').value) {
                        applyFilters();
                    }
                }
            });
            
            document.getElementById('filter-date').addEventListener('change', function() {
                if (this.value && document.getElementById('filter-period').value !== 'all') {
                    applyFilters();
                }
            });

            document.getElementById('generate-report').addEventListener('click', generateReport);
            document.getElementById('export-report').addEventListener('click', exportReport);

            document.getElementById('add-auxiliar-btn').addEventListener('click', showAddAuxiliarModal);
            document.getElementById('add-task-btn').addEventListener('click', showAddTaskModal);
            document.getElementById('create-backup').addEventListener('click', createBackup);
            document.getElementById('create-text-backup').addEventListener('click', createTextBackup);
            document.getElementById('restore-btn').addEventListener('click', () => document.getElementById('restore-file').click());
            document.getElementById('restore-file').addEventListener('change', restoreBackup);
            document.getElementById('save-supervisor').addEventListener('click', saveSupervisor);
        }

        function checkDeliveryAlarms() {
            const now = new Date();
            
            deliveries.forEach(delivery => {
                if (delivery.delivered === 'si' || delivery.status === 'completado') return;
                
                const deliveryTime = new Date(delivery.calculatedDelivery);
                const diffMinutes = Math.floor((deliveryTime - now) / (1000 * 60));
                
                const alarmKey5 = `${delivery.id}_5min`;
                const alarmKey1 = `${delivery.id}_1min`;
                
                if (diffMinutes === 5 && !activeAlarms[alarmKey5]) {
                    activeAlarms[alarmKey5] = true;
                    showAlarmModal(delivery, '5 minutos');
                }
                
                if (diffMinutes === 1 && !activeAlarms[alarmKey1]) {
                    activeAlarms[alarmKey1] = true;
                    showAlarmModal(delivery, '1 minuto');
                }
            });
        }

        function showAlarmModal(delivery, timeRemaining) {
            const modal = document.getElementById('alarm-modal');
            
            document.getElementById('alarm-client').textContent = delivery.client;
            document.getElementById('alarm-task').textContent = delivery.taskType;
            document.getElementById('alarm-auxiliar').textContent = delivery.auxiliar;
            document.getElementById('alarm-time').textContent = formatDateTime(delivery.calculatedDelivery);
            document.getElementById('alarm-remaining').textContent = timeRemaining;
            
            modal.dataset.deliveryId = delivery.id;
            modal.dataset.timeType = timeRemaining === '5 minutos' ? '5min' : '1min';
            
            modal.classList.add('active');
            playAlarmSound();
        }

        function playAlarmSound() {
            let playCount = 0;
            const maxPlays = 3;
            
            function playOnce() {
                if (playCount < maxPlays) {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';
                    
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 1);
                    
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 1);
                    
                    playCount++;
                    setTimeout(playOnce, 1200);
                }
            }
            
            playOnce();
        }

        function acceptAlarm() {
            const modal = document.getElementById('alarm-modal');
            modal.classList.remove('active');
            showNotification('Alarma aceptada - Recuerda completar la entrega');
        }

        function postponeAlarm() {
            const modal = document.getElementById('alarm-modal');
            const deliveryId = parseInt(modal.dataset.deliveryId);
            
            modal.classList.remove('active');
            
            setTimeout(() => {
                const delivery = deliveries.find(d => d.id === deliveryId);
                if (delivery && delivery.delivered !== 'si') {
                    showAlarmModal(delivery, 'URGENTE - Pospuesto');
                }
            }, 60000);
            
            showNotification('Alarma pospuesta por 1 minuto');
        }

        function saveSupervisor() {
            supervisorInfo.name = document.getElementById('supervisor-name').value;
            supervisorInfo.position = document.getElementById('supervisor-position').value;
            
            document.getElementById('supervisor-display').textContent = 
                `${supervisorInfo.position}: ${supervisorInfo.name}`;
            
            saveToLocalStorage();
            showNotification('Información del supervisor actualizada');
        }

        function switchTab(tabId) {
            document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            
            document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            const tabNames = {
                'resumen': 'Resumen',
                'calendario': 'Calendario',
                'nueva-entrega': 'Nueva Entrega',
                'control-entregas': 'Control de Entregas',
                'reportes': 'Reportes',
                'papelera': 'Papelera',
                'horas-estandar': 'Horas Estándar',
                'configuracion': 'Configuración'
            };
            
            document.getElementById('page-title').textContent = tabNames[tabId];
            updateAllTabs();
        }

        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                timeZone: 'America/Lima'
            };
            
            const dateStr = now.toLocaleDateString('es-PE', options);
            const timeStr = now.toLocaleTimeString('es-PE', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false,
                timeZone: 'America/Lima'
            });
            
            document.getElementById('current-date').textContent = dateStr;
            document.getElementById('current-time').textContent = timeStr;
        }

        function toggleAnalysis(type) {
            const dailyDiv = document.getElementById('analysis-daily');
            const weeklyDiv = document.getElementById('analysis-weekly');
            const dailyBtn = document.getElementById('btn-daily');
            const weeklyBtn = document.getElementById('btn-weekly');
            
            if (type === 'daily') {
                dailyDiv.style.display = 'grid';
                weeklyDiv.style.display = 'none';
                dailyBtn.classList.add('active');
                weeklyBtn.classList.remove('active');
            } else {
                dailyDiv.style.display = 'none';
                weeklyDiv.style.display = 'grid';
                dailyBtn.classList.remove('active');
                weeklyBtn.classList.add('active');
            }
            
            updateAnalysisStats();
        }

        function toggleDeliveries(type) {
            const todayDiv = document.getElementById('deliveries-today');
            const weekDiv = document.getElementById('deliveries-week');
            const todayBtn = document.getElementById('btn-today');
            const weekBtn = document.getElementById('btn-week');
            
            if (type === 'today') {
                todayDiv.style.display = 'block';
                weekDiv.style.display = 'none';
                todayBtn.classList.add('active');
                weekBtn.classList.remove('active');
            } else {
                todayDiv.style.display = 'none';
                weekDiv.style.display = 'block';
                todayBtn.classList.remove('active');
                weekBtn.classList.add('active');
            }
            
            updateDeliveriesTables();
        }

        function updateAnalysisStats() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay() + 1);
            
            let dailyDelivered = 0;
            let dailyPending = 0;
            let weeklyDelivered = 0;
            let weeklyPending = 0;
            
            deliveries.forEach(delivery => {
                const deliveryDate = new Date(delivery.calculatedDelivery);
                deliveryDate.setHours(0, 0, 0, 0);
                
                if (deliveryDate.getTime() === today.getTime()) {
                    if (delivery.delivered === 'si') {
                        dailyDelivered++;
                    } else {
                        dailyPending++;
                    }
                }
                
                if (deliveryDate >= weekStart && deliveryDate <= new Date(weekStart.getTime() + 6 * 24 * 60 * 60 * 1000)) {
                    if (delivery.delivered === 'si') {
                        weeklyDelivered++;
                    } else {
                        weeklyPending++;
                    }
                }
            });
            
            document.getElementById('daily-delivered').textContent = dailyDelivered;
            document.getElementById('daily-pending').textContent = dailyPending;
            document.getElementById('weekly-delivered').textContent = weeklyDelivered;
            document.getElementById('weekly-pending').textContent = weeklyPending;
        }

        function updateDeliveriesTables() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay() + 1);
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            
            const todayDeliveries = deliveries.filter(d => {
                const deliveryDate = new Date(d.calculatedDelivery);
                deliveryDate.setHours(0, 0, 0, 0);
                return deliveryDate.getTime() === today.getTime() && 
                       d.delivered !== 'si' && 
                       d.status !== 'completado';
            });
            
            const weekDeliveries = deliveries.filter(d => {
                const deliveryDate = new Date(d.calculatedDelivery);
                deliveryDate.setHours(0, 0, 0, 0);
                return deliveryDate >= weekStart && 
                       deliveryDate <= weekEnd && 
                       d.delivered !== 'si' && 
                       d.status !== 'completado';
            });
            
            const todayTbody = document.getElementById('today-deliveries-tbody');
            if (todayDeliveries.length > 0) {
                todayTbody.innerHTML = todayDeliveries.map(d => `
                    <tr>
                        <td>${formatDateTime(d.calculatedDelivery)}</td>
                        <td>${d.client}</td>
                        <td>${d.taskType}</td>
                        <td>${d.auxiliar}</td>
                        <td><span class="badge badge-${getStatusBadgeClass(d.status)}">${d.status}</span></td>
                        <td>
                            <select onchange="updateDeliveryField(${d.id}, 'delivered', this.value)" style="background: ${d.delivered === 'si' ? '#d4edda' : '#f8d7da'};">
                                <option value="no" ${d.delivered === 'no' ? 'selected' : ''}>No</option>
                                <option value="si" ${d.delivered === 'si' ? 'selected' : ''}>Sí</option>
                            </select>
                        </td>
                    </tr>
                `).join('');
            } else {
                todayTbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No hay entregas pendientes para hoy</td></tr>';
            }
            
            const weekTbody = document.getElementById('week-deliveries-tbody');
            if (weekDeliveries.length > 0) {
                weekTbody.innerHTML = weekDeliveries.map(d => `
                    <tr>
                        <td>${formatDateTime(d.calculatedDelivery)}</td>
                        <td>${d.client}</td>
                        <td>${d.taskType}</td>
                        <td>${d.auxiliar}</td>
                        <td><span class="badge badge-${getStatusBadgeClass(d.status)}">${d.status}</span></td>
                        <td>
                            <select onchange="updateDeliveryField(${d.id}, 'delivered', this.value)" style="background: ${d.delivered === 'si' ? '#d4edda' : '#f8d7da'};">
                                <option value="no" ${d.delivered === 'no' ? 'selected' : ''}>No</option>
                                <option value="si" ${d.delivered === 'si' ? 'selected' : ''}>Sí</option>
                            </select>
                        </td>
                    </tr>
                `).join('');
            } else {
                weekTbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No hay entregas pendientes esta semana</td></tr>';
            }
        }

        function generateCalendar() {
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const prevLastDay = new Date(currentYear, currentMonth, 0);
            
            const firstDayIndex = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
            const nextDays = 6 - (lastDay.getDay() === 0 ? 6 : lastDay.getDay() - 1);
            
            const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            
            document.getElementById('calendar-month-year').textContent = `${months[currentMonth]} ${currentYear}`;
            
            let html = '';
            
            const dayHeaders = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
            dayHeaders.forEach(day => {
                html += `<div class="calendar-day-header">${day}</div>`;
            });
            
            for (let x = firstDayIndex; x > 0; x--) {
                html += `<div class="calendar-day" style="opacity: 0.3;">${prevLastDay.getDate() - x + 1}</div>`;
            }
            
            for (let i = 1; i <= lastDay.getDate(); i++) {
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                const dayDeliveries = getDeliveriesForDate(dateStr);
                
                html += `<div class="calendar-day" onclick="showDayDetails('${dateStr}')">
                    <div class="calendar-day-number">${i}</div>`;
                
                dayDeliveries.forEach(delivery => {
                    const time = new Date(delivery.calculatedDelivery).toLocaleTimeString('es-PE', { 
                        hour: '2-digit', 
                        minute: '2-digit',
                        hour12: false
                    });
                    html += `<div class="calendar-task">${delivery.auxiliar.split(' ')[0]} - ${time}</div>`;
                });
                
                html += '</div>';
            }
            
            for (let j = 1; j <= nextDays; j++) {
                html += `<div class="calendar-day" style="opacity: 0.3;">${j}</div>`;
            }
            
            document.getElementById('calendar-grid').innerHTML = html;
        }

        function changeMonth(direction) {
            currentMonth += direction;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar();
        }

        function getDeliveriesForDate(dateStr) {
            return deliveries.filter(d => {
                const deliveryDate = new Date(d.calculatedDelivery).toISOString().split('T')[0];
                return deliveryDate === dateStr;
            });
        }

        function showDayDetails(dateStr) {
            const dayDeliveries = getDeliveriesForDate(dateStr);
            
            if (dayDeliveries.length === 0) {
                showNotification('No hay entregas para esta fecha');
                return;
            }
            
            let modalHTML = `
                <div class="modal active" id="day-details-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Entregas del ${new Date(dateStr + 'T12:00:00').toLocaleDateString('es-PE', { 
                                weekday: 'long', 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric' 
                            })}</h2>
                        </div>
                        <div style="max-height: 400px; overflow-y: auto;">
            `;
            
            dayDeliveries.forEach(delivery => {
                modalHTML += `
                    <div style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9f9f9;">
                        <p style="margin: 5px 0;"><strong>Hora de inicio:</strong> ${formatDateTime(delivery.startDateTime)}</p>
                        <p style="margin: 5px 0;"><strong>Auxiliar:</strong> ${delivery.auxiliar}</p>
                        <p style="margin: 5px 0;"><strong>Hora de entrega:</strong> ${formatDateTime(delivery.calculatedDelivery)}</p>
                        <p style="margin: 5px 0;"><strong>Título de entrega:</strong> ${delivery.title}</p>
                        <p style="margin: 5px 0;"><strong>Cliente:</strong> ${delivery.client}</p>
                    </div>
                `;
            });
            
            modalHTML += `
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" onclick="closeDayDetailsModal()">Cerrar</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
        
        function closeDayDetailsModal() {
            const modal = document.getElementById('day-details-modal');
            if (modal) modal.remove();
        }

        function handleNewDelivery(e) {
            e.preventDefault();
            
            const delivery = {
                id: Date.now(),
                startDateTime: document.getElementById('start-datetime').value,
                auxiliar: document.getElementById('auxiliar-select').value,
                taskType: document.getElementById('task-type-select').value,
                title: document.getElementById('delivery-title').value,
                client: document.getElementById('client-name').value,
                estimatedTime: document.getElementById('estimated-time').value,
                status: document.getElementById('status-select').value,
                driveLink: document.getElementById('drive-link').value,
                calculatedDelivery: document.getElementById('calculated-delivery').value,
                observations: document.getElementById('observations').value,
                delivered: 'no',
                deliveryStatus: 'no-entregado',
                observationStatus: 'en-desarrollo',
                createdAt: new Date().toISOString()
            };
            
            deliveries.push(delivery);
            saveToLocalStorage();
            
            showNotification('Entrega agregada exitosamente');
            clearNewDeliveryForm();
            switchTab('control-entregas');
        }

        function clearNewDeliveryForm() {
            document.getElementById('new-delivery-form').reset();
            const now = new Date();
            const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
            document.getElementById('start-datetime').value = localDateTime;
        }

        function updateEstimatedTime() {
            const taskSelect = document.getElementById('task-type-select');
            const estimatedTimeInput = document.getElementById('estimated-time');
            
            if (taskSelect.value) {
                let hours = '01:00';
                Object.values(standardTasks).forEach(category => {
                    const task = category.find(t => t.name === taskSelect.value);
                    if (task) {
                        hours = task.hours.includes('-') ? task.hours.split('-')[0] : task.hours;
                    }
                });
                
                estimatedTimeInput.value = hours;
                calculateDeliveryTime();
            }
        }

        function calculateDeliveryTime() {
            const startDateTime = document.getElementById('start-datetime').value;
            const estimatedTime = document.getElementById('estimated-time').value;
            
            if (startDateTime && estimatedTime) {
                const calculatedDateTime = calculateWorkingHours(startDateTime, estimatedTime);
                document.getElementById('calculated-delivery').value = calculatedDateTime;
            }
        }

        function calculateWorkingHours(startDateTime, duration) {
            const start = new Date(startDateTime);
            const [durationHours, durationMinutes] = duration.split(':').map(Number);
            let remainingMinutes = (durationHours * 60) + durationMinutes;
            
            let current = new Date(start);
            
            function getMinutesSinceMidnight(date) {
                return date.getHours() * 60 + date.getMinutes();
            }
            
            function setTimeFromMinutes(date, minutes) {
                date.setHours(Math.floor(minutes / 60));
                date.setMinutes(minutes % 60);
                date.setSeconds(0);
                date.setMilliseconds(0);
            }
            
            while (remainingMinutes > 0) {
                const dayOfWeek = current.getDay();
                const currentMinutes = getMinutesSinceMidnight(current);
                
                const dateStr = current.toISOString().split('T')[0];
                const isHoliday = peruHolidays.includes(dateStr);
                
                if (dayOfWeek === 0 || isHoliday) {
                    current.setDate(current.getDate() + 1);
                    current.setHours(8, 0, 0, 0);
                    continue;
                }
                
                if (dayOfWeek >= 1 && dayOfWeek <= 5) {
                    const morningStart = 8 * 60;
                    const morningEnd = 13 * 60;
                    const afternoonStart = 15 * 60;
                    const afternoonEnd = 19 * 60;
                    
                    if (currentMinutes < morningStart) {
                        setTimeFromMinutes(current, morningStart);
                        continue;
                    }
                    
                    if (currentMinutes >= morningStart && currentMinutes < morningEnd) {
                        const availableInMorning = morningEnd - currentMinutes;
                        
                        if (remainingMinutes <= availableInMorning) {
                            setTimeFromMinutes(current, currentMinutes + remainingMinutes);
                            remainingMinutes = 0;
                        } else {
                            remainingMinutes -= availableInMorning;
                            setTimeFromMinutes(current, afternoonStart);
                        }
                    }
                    else if (currentMinutes >= morningEnd && currentMinutes < afternoonStart) {
                        setTimeFromMinutes(current, afternoonStart);
                        continue;
                    }
                    else if (currentMinutes >= afternoonStart && currentMinutes < afternoonEnd) {
                        const availableInAfternoon = afternoonEnd - currentMinutes;
                        
                        if (remainingMinutes <= availableInAfternoon) {
                            setTimeFromMinutes(current, currentMinutes + remainingMinutes);
                            remainingMinutes = 0;
                        } else {
                            remainingMinutes -= availableInAfternoon;
                            current.setDate(current.getDate() + 1);
                            current.setHours(8, 0, 0, 0);
                        }
                    }
                    else {
                        current.setDate(current.getDate() + 1);
                        current.setHours(8, 0, 0, 0);
                    }
                }
                else if (dayOfWeek === 6) {
                    const morningStart = 8 * 60;
                    const morningEnd = 13 * 60;
                    
                    if (currentMinutes < morningStart) {
                        setTimeFromMinutes(current, morningStart);
                        continue;
                    }
                    
                    if (currentMinutes >= morningStart && currentMinutes < morningEnd) {
                        const availableInMorning = morningEnd - currentMinutes;
                        
                        if (remainingMinutes <= availableInMorning) {
                            setTimeFromMinutes(current, currentMinutes + remainingMinutes);
                            remainingMinutes = 0;
                        } else {
                            remainingMinutes -= availableInMorning;
                            current.setDate(current.getDate() + 2);
                            current.setHours(8, 0, 0, 0);
                        }
                    }
                    else {
                        current.setDate(current.getDate() + 2);
                        current.setHours(8, 0, 0, 0);
                    }
                }
            }
            
            const year = current.getFullYear();
            const month = String(current.getMonth() + 1).padStart(2, '0');
            const day = String(current.getDate()).padStart(2, '0');
            const hours = String(current.getHours()).padStart(2, '0');
            const minutes = String(current.getMinutes()).padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // FUNCIÓN MEJORADA PARA CONTROL DE ENTREGAS CON ORDEN Y COLORES
        function updateControlTable() {
            const tbody = document.getElementById('control-tbody');
            
            if (deliveries.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align: center;">No hay entregas registradas</td></tr>';
                return;
            }
            
            deliveries.forEach(d => {
                if (!d.deliveryStatus || d.deliveryStatus === 'pendiente') {
                    d.deliveryStatus = 'no-entregado';
                }
            });
            
            let filteredDeliveries = deliveries;
            if (clientSearchQuery) {
                filteredDeliveries = deliveries.filter(d => 
                    d.client.toLowerCase().includes(clientSearchQuery)
                );
            }
            
            // NUEVO ORDENAMIENTO
            const sortedDeliveries = [...filteredDeliveries].sort((a, b) => {
                const statusPriority = {
                    'en-curso': 1,
                    'pausado': 2,
                    'corrigiendo': 2,
                    'completado': 3
                };
                
                const priorityA = statusPriority[a.status] || 4;
                const priorityB = statusPriority[b.status] || 4;
                
                if (priorityA !== priorityB) {
                    return priorityA - priorityB;
                }
                
                if (a.status === 'completado' && b.status === 'completado') {
                    return new Date(b.calculatedDelivery) - new Date(a.calculatedDelivery);
                } else {
                    return new Date(a.calculatedDelivery) - new Date(b.calculatedDelivery);
                }
            });
            
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageDeliveries = sortedDeliveries.slice(start, end);
            
            if (pageDeliveries.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align: center;">No se encontraron entregas con ese cliente</td></tr>';
                updatePagination(0);
                return;
            }
            
            tbody.innerHTML = pageDeliveries.map(d => {
                let deliveryBgColor = '#f8d7da';
                if (d.deliveryStatus === 'entregado') {
                    deliveryBgColor = '#d4edda';
                } else if (d.deliveryStatus === 'corrigiendo') {
                    deliveryBgColor = '#fff3cd';
                }
                
                let rowClass = '';
                if (d.status === 'en-curso') {
                    rowClass = 'row-en-curso';
                } else if (d.status === 'pausado') {
                    rowClass = 'row-pausado';
                } else if (d.status === 'corrigiendo') {
                    rowClass = 'row-corrigiendo';
                } else if (d.status === 'completado') {
                    rowClass = 'row-completado';
                }
                
                return `
                    <tr class="${rowClass}">
                        <td>${formatDateTime(d.calculatedDelivery)}</td>
                        <td>${d.taskType}</td>
                        <td>${d.client}</td>
                        <td>${d.auxiliar}</td>
                        <td>
                            <select onchange="updateDeliveryField(${d.id}, 'status', this.value)">
                                <option value="en-curso" ${d.status === 'en-curso' ? 'selected' : ''}>En curso</option>
                                <option value="completado" ${d.status === 'completado' ? 'selected' : ''}>Completado</option>
                                <option value="pausado" ${d.status === 'pausado' ? 'selected' : ''}>Pausado</option>
                                <option value="corrigiendo" ${d.status === 'corrigiendo' ? 'selected' : ''}>Corrigiendo</option>
                            </select>
                        </td>
                        <td>${d.driveLink ? `<a href="${d.driveLink}" target="_blank" class="btn btn-info">Ver</a>` : 'N/A'}</td>
                        <td>
                            <select onchange="updateDeliveryField(${d.id}, 'observationStatus', this.value)">
                                <option value="en-desarrollo" ${d.observationStatus === 'en-desarrollo' ? 'selected' : ''}>En desarrollo</option>
                                <option value="entrego-antes" ${d.observationStatus === 'entrego-antes' ? 'selected' : ''}>Entregó antes</option>
                                <option value="entrego-tiempo" ${d.observationStatus === 'entrego-tiempo' ? 'selected' : ''}>Entregó a tiempo</option>
                                <option value="entrego-despues" ${d.observationStatus === 'entrego-despues' ? 'selected' : ''}>Entregó después</option>
                            </select>
                        </td>
                        <td>
                            <select onchange="updateDeliveryField(${d.id}, 'deliveryStatus', this.value)" style="background: ${deliveryBgColor}; font-weight: bold;">
                                <option value="no-entregado" ${d.deliveryStatus === 'no-entregado' ? 'selected' : ''}>No Entregado</option>
                                <option value="entregado" ${d.deliveryStatus === 'entregado' ? 'selected' : ''}>Entregado</option>
                                <option value="corrigiendo" ${d.deliveryStatus === 'corrigiendo' ? 'selected' : ''}>Corrigiendo</option>
                            </select>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-warning" onclick="editDelivery(${d.id})">Editar</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            
            updatePagination(sortedDeliveries.length);
        }

        function applyFilters() {
            const period = document.getElementById('filter-period').value;
            const filterDate = document.getElementById('filter-date').value;
            
            let filteredDeliveries = [...deliveries];
            
            if (clientSearchQuery) {
                filteredDeliveries = filteredDeliveries.filter(d => 
                    d.client.toLowerCase().includes(clientSearchQuery)
                );
            }
            
            if (period !== 'all' && filterDate) {
                filteredDeliveries = filteredDeliveries.filter(delivery => {
                    const deliveryDate = new Date(delivery.calculatedDelivery);
                    
                    switch(period) {
                        case 'day':
                            const selectedDateStr = filterDate;
                            const deliveryDateStr = deliveryDate.toISOString().split('T')[0];
                            return deliveryDateStr === selectedDateStr;
                            
                        case 'week':
                            const selectedDate = new Date(filterDate + 'T12:00:00');
                            const weekStart = new Date(selectedDate);
                            weekStart.setDate(selectedDate.getDate() - selectedDate.getDay());
                            const weekEnd = new Date(weekStart);
                            weekEnd.setDate(weekStart.getDate() + 6);
                            weekEnd.setHours(23, 59, 59, 999);
                            
                            return deliveryDate >= weekStart && deliveryDate <= weekEnd;
                            
                        case 'month':
                            const selectedMonth = new Date(filterDate + 'T12:00:00');
                            return deliveryDate.getMonth() === selectedMonth.getMonth() && 
                                   deliveryDate.getFullYear() === selectedMonth.getFullYear();
                            
                        case 'year':
                            const selectedYear = new Date(filterDate + 'T12:00:00');
                            return deliveryDate.getFullYear() === selectedYear.getFullYear();
                            
                        default:
                            return true;
                    }
                });
            }
            
            updateFilteredControlTable(filteredDeliveries);
        }
        
        function updateFilteredControlTable(filteredDeliveries) {
            const tbody = document.getElementById('control-tbody');
            
            if (filteredDeliveries.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align: center;">No se encontraron entregas con los filtros aplicados</td></tr>';
                updatePagination(0);
                return;
            }
            
            const sortedDeliveries = filteredDeliveries.sort((a, b) => {
                const statusPriority = {
                    'en-curso': 1,
                    'pausado': 2,
                    'corrigiendo': 2,
                    'completado': 3
                };
                
                const priorityA = statusPriority[a.status] || 4;
                const priorityB = statusPriority[b.status] || 4;
                
                if (priorityA !== priorityB) {
                    return priorityA - priorityB;
                }
                
                if (a.status === 'completado' && b.status === 'completado') {
                    return new Date(b.calculatedDelivery) - new Date(a.calculatedDelivery);
                } else {
                    return new Date(a.calculatedDelivery) - new Date(b.calculatedDelivery);
                }
            });
            
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageDeliveries = sortedDeliveries.slice(start, end);
            
            tbody.innerHTML = pageDeliveries.map(d => {
                let deliveryBgColor = '#f8d7da';
                if (d.deliveryStatus === 'entregado') {
                    deliveryBgColor = '#d4edda';
                } else if (d.deliveryStatus === 'corrigiendo') {
                    deliveryBgColor = '#fff3cd';
                }
                
                let rowClass = '';
                if (d.status === 'en-curso') {
                    rowClass = 'row-en-curso';
                } else if (d.status === 'pausado') {
                    rowClass = 'row-pausado';
                } else if (d.status === 'corrigiendo') {
                    rowClass = 'row-corrigiendo';
                } else if (d.status === 'completado') {
                    rowClass = 'row-completado';
                }
                
                return `
                    <tr class="${rowClass}">
                        <td>${formatDateTime(d.calculatedDelivery)}</td>
                        <td>${d.taskType}</td>
                        <td>${d.client}</td>
                        <td>${d.auxiliar}</td>
                        <td>
                            <select onchange="updateDeliveryField(${d.id}, 'status', this.value)">
                                <option value="en-curso" ${d.status === 'en-curso' ? 'selected' : ''}>En curso</option>
                                <option value="completado" ${d.status === 'completado' ? 'selected' : ''}>Completado</option>
                                <option value="pausado" ${d.status === 'pausado' ? 'selected' : ''}>Pausado</option>
                                <option value="corrigiendo" ${d.status === 'corrigiendo' ? 'selected' : ''}>Corrigiendo</option>
                            </select>
                        </td>
                        <td>${d.driveLink ? `<a href="${d.driveLink}" target="_blank" class="btn btn-info">Ver</a>` : 'N/A'}</td>
                        <td>
                            <select onchange="updateDeliveryField(${d.id}, 'observationStatus', this.value)">
                                <option value="en-desarrollo" ${d.observationStatus === 'en-desarrollo' ? 'selected' : ''}>En desarrollo</option>
                                <option value="entrego-antes" ${d.observationStatus === 'entrego-antes' ? 'selected' : ''}>Entregó antes</option>
                                <option value="entrego-tiempo" ${d.observationStatus === 'entrego-tiempo' ? 'selected' : ''}>Entregó a tiempo</option>
                                <option value="entrego-despues" ${d.observationStatus === 'entrego-despues' ? 'selected' : ''}>Entregó después</option>
                            </select>
                        </td>
                        <td>
                            <select onchange="updateDeliveryField(${d.id}, 'deliveryStatus', this.value)" style="background: ${deliveryBgColor}; font-weight: bold;">
                                <option value="no-entregado" ${d.deliveryStatus === 'no-entregado' ? 'selected' : ''}>No Entregado</option>
                                <option value="entregado" ${d.deliveryStatus === 'entregado' ? 'selected' : ''}>Entregado</option>
                                <option value="corrigiendo" ${d.deliveryStatus === 'corrigiendo' ? 'selected' : ''}>Corrigiendo</option>
                            </select>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-warning" onclick="editDelivery(${d.id})">Editar</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            
            updatePagination(sortedDeliveries.length);
        }

        function updatePagination(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            const paginationDiv = document.getElementById('control-pagination');
            
            if (totalPages <= 1) {
                paginationDiv.innerHTML = '';
                return;
            }
            
            let html = '';
            for (let i = 1; i <= totalPages; i++) {
                html += `<button onclick="changePage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
            }
            paginationDiv.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            updateControlTable();
        }

        function updateDeliveryField(id, field, value) {
            const delivery = deliveries.find(d => d.id === id);
            if (!delivery) return;
            
            const now = new Date();
            const deliveryTime = new Date(delivery.calculatedDelivery);
            const diffMinutes = (deliveryTime - now) / (1000 * 60);
            
            if (field === 'delivered' && value === 'si') {
                delivery.delivered = 'si';
                delivery.status = 'completado';
                delivery.deliveryStatus = 'entregado';
                
                if (now < deliveryTime && diffMinutes > 10) {
                    delivery.observationStatus = 'entrego-antes';
                } else if (diffMinutes >= -10 && diffMinutes <= 10) {
                    delivery.observationStatus = 'entrego-tiempo';
                } else {
                    delivery.observationStatus = 'entrego-despues';
                }
            }
            else if (field === 'delivered' && value === 'no') {
                delivery.delivered = 'no';
                delivery.status = 'en-curso';
                delivery.deliveryStatus = 'no-entregado';
                delivery.observationStatus = 'en-desarrollo';
            }
            else if (field === 'deliveryStatus' && value === 'entregado') {
                delivery.deliveryStatus = 'entregado';
                delivery.delivered = 'si';
                delivery.status = 'completado';
                
                if (now < deliveryTime && diffMinutes > 10) {
                    delivery.observationStatus = 'entrego-antes';
                } else if (diffMinutes >= -10 && diffMinutes <= 10) {
                    delivery.observationStatus = 'entrego-tiempo';
                } else {
                    delivery.observationStatus = 'entrego-despues';
                }
            }
            else if (field === 'deliveryStatus' && value === 'corrigiendo') {
                delivery.deliveryStatus = 'corrigiendo';
                delivery.status = 'corrigiendo';
                delivery.delivered = 'no';
            }
            else if (field === 'deliveryStatus' && value === 'no-entregado') {
                delivery.deliveryStatus = 'no-entregado';
                delivery.delivered = 'no';
                delivery.status = 'en-curso';
                delivery.observationStatus = 'en-desarrollo';
            }
            else if (field === 'status') {
                delivery.status = value;
                if (value === 'completado') {
                    delivery.delivered = 'si';
                    delivery.deliveryStatus = 'entregado';
                    if (now < deliveryTime && diffMinutes > 10) {
                        delivery.observationStatus = 'entrego-antes';
                    } else if (diffMinutes >= -10 && diffMinutes <= 10) {
                        delivery.observationStatus = 'entrego-tiempo';
                    } else {
                        delivery.observationStatus = 'entrego-despues';
                    }
                } else if (value === 'en-curso') {
                    delivery.delivered = 'no';
                    delivery.deliveryStatus = 'no-entregado';
                    delivery.observationStatus = 'en-desarrollo';
                } else if (value === 'corrigiendo') {
                    delivery.delivered = 'no';
                    delivery.deliveryStatus = 'corrigiendo';
                }
            }
            else if (field === 'observationStatus') {
                delivery.observationStatus = value;
                if (value === 'entrego-antes' || value === 'entrego-tiempo' || value === 'entrego-despues') {
                    delivery.delivered = 'si';
                    delivery.status = 'completado';
                    delivery.deliveryStatus = 'entregado';
                } else if (value === 'en-desarrollo') {
                    delivery.delivered = 'no';
                    delivery.status = 'en-curso';
                    delivery.deliveryStatus = 'no-entregado';
                }
            }
            else {
                delivery[field] = value;
            }
            
            saveToLocalStorage();
            updateAllTabs();
            showNotification('Estado actualizado correctamente');
        }

        function editDelivery(id) {
            const delivery = deliveries.find(d => d.id === id);
            if (!delivery) return;
            
            const modalHTML = `
                <div class="modal active" id="edit-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Editar Entrega</h2>
                        </div>
                        <form id="edit-delivery-form">
                            <div class="form-group">
                                <label>Fecha y Hora de Inicio</label>
                                <input type="datetime-local" id="edit-start-datetime" value="${delivery.startDateTime}" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Auxiliar</label>
                                <select id="edit-auxiliar-select" required>
                                    ${auxiliares.map(a => `<option value="${a.name}" ${a.name === delivery.auxiliar ? 'selected' : ''}>${a.name}</option>`).join('')}
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tipo de Tarea</label>
                                <select id="edit-task-type-select" required>
                                    ${getTaskOptions(delivery.taskType)}
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Título de la Entrega</label>
                                <input type="text" id="edit-delivery-title" value="${delivery.title}" required>
                            </div>

                            <div class="form-group">
                                <label>Cliente</label>
                                <input type="text" id="edit-client-name" value="${delivery.client}" required>
                            </div>

                            <div class="form-group">
                                <label>Tiempo Estimado (HH:MM)</label>
                                <input type="text" id="edit-estimated-time" value="${delivery.estimatedTime}" pattern="[0-9]{2}:[0-9]{2}" required>
                            </div>

                            <div class="form-group">
                                <label>Estado</label>
                                <select id="edit-status-select" required>
                                    <option value="en-curso" ${delivery.status === 'en-curso' ? 'selected' : ''}>En Curso</option>
                                    <option value="pausado" ${delivery.status === 'pausado' ? 'selected' : ''}>Pausado</option>
                                    <option value="completado" ${delivery.status === 'completado' ? 'selected' : ''}>Completado</option>
                                    <option value="corrigiendo" ${delivery.status === 'corrigiendo' ? 'selected' : ''}>Corrigiendo</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Link de Google Drive</label>
                                <input type="url" id="edit-drive-link" value="${delivery.driveLink || ''}">
                            </div>

                            <div class="form-group">
                                <label>Fecha y Hora de Entrega Calculada</label>
                                <input type="datetime-local" id="edit-calculated-delivery" value="${delivery.calculatedDelivery}" required>
                            </div>

                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea id="edit-observations" rows="3">${delivery.observations || ''}</textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                <button type="button" class="btn btn-danger" onclick="deleteDelivery(${id})">Eliminar</button>
                                <button type="button" class="btn btn-primary" onclick="closeModal()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            document.getElementById('edit-estimated-time').addEventListener('input', function() {
                const startDateTime = document.getElementById('edit-start-datetime').value;
                const estimatedTime = this.value;
                
                if (startDateTime && estimatedTime && estimatedTime.match(/[0-9]{2}:[0-9]{2}/)) {
                    const calculatedDateTime = calculateWorkingHours(startDateTime, estimatedTime);
                    document.getElementById('edit-calculated-delivery').value = calculatedDateTime;
                }
            });
            
            document.getElementById('edit-start-datetime').addEventListener('change', function() {
                const startDateTime = this.value;
                const estimatedTime = document.getElementById('edit-estimated-time').value;
                
                if (startDateTime && estimatedTime) {
                    const calculatedDateTime = calculateWorkingHours(startDateTime, estimatedTime);
                    document.getElementById('edit-calculated-delivery').value = calculatedDateTime;
                }
            });
            
            document.getElementById('edit-delivery-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                delivery.startDateTime = document.getElementById('edit-start-datetime').value;
                delivery.auxiliar = document.getElementById('edit-auxiliar-select').value;
                delivery.taskType = document.getElementById('edit-task-type-select').value;
                delivery.title = document.getElementById('edit-delivery-title').value;
                delivery.client = document.getElementById('edit-client-name').value;
                delivery.estimatedTime = document.getElementById('edit-estimated-time').value;
                delivery.status = document.getElementById('edit-status-select').value;
                delivery.driveLink = document.getElementById('edit-drive-link').value;
                delivery.calculatedDelivery = document.getElementById('edit-calculated-delivery').value;
                delivery.observations = document.getElementById('edit-observations').value;
                
                saveToLocalStorage();
                closeModal();
                showNotification('Entrega actualizada exitosamente');
                updateAllTabs();
            });
        }

        function deleteDelivery(id) {
            const deliveryIndex = deliveries.findIndex(d => d.id === id);
            
            if (deliveryIndex !== -1) {
                const delivery = deliveries[deliveryIndex];
                delivery.deletedAt = new Date().toISOString();
                deletedDeliveries.push(delivery);
                deliveries.splice(deliveryIndex, 1);
                
                saveToLocalStorage();
                closeModal();
                showNotification('Entrega movida a papelera');
                switchTab('papelera');
            }
        }

        function exportControl() {
            const date = new Date().toLocaleDateString('es-PE');
            let content = `CONTROL DE ENTREGAS - ES CONSULTORES\n`;
            content += `Fecha de exportación: ${date}\n\n`;
            
            deliveries.forEach(delivery => {
                content += `${'='.repeat(50)}\n`;
                content += `Fecha y Hora: ${formatDateTime(delivery.calculatedDelivery)}\n`;
                content += `Cliente: ${delivery.client}\n`;
                content += `Tarea: ${delivery.taskType}\n`;
                content += `Auxiliar: ${delivery.auxiliar}\n`;
                content += `Estado: ${delivery.status}\n`;
                content += `Entregado: ${delivery.delivered === 'si' ? 'Sí' : 'No'}\n`;
                content += `Observaciones: ${delivery.observations || 'N/A'}\n`;
            });
            
            downloadFile(content, `control_entregas_${date.replace(/\//g, '-')}.txt`);
        }

        function generateReport() {
            const reportType = document.getElementById('report-type').value;
            const tbody = document.getElementById('reports-tbody');
            
            let reportData = {};
            
            auxiliares.forEach(aux => {
                reportData[aux.name] = {
                    total: 0,
                    completed: 0,
                    pending: 0,
                    onTime: 0,
                    before: 0,
                    after: 0
                };
            });
            
            deliveries.forEach(delivery => {
                if (reportData[delivery.auxiliar]) {
                    reportData[delivery.auxiliar].total++;
                    
                    if (delivery.status === 'completado') {
                        reportData[delivery.auxiliar].completed++;
                    } else {
                        reportData[delivery.auxiliar].pending++;
                    }
                    
                    if (delivery.observationStatus === 'entrego-tiempo') {
                        reportData[delivery.auxiliar].onTime++;
                    } else if (delivery.observationStatus === 'entrego-antes') {
                        reportData[delivery.auxiliar].before++;
                    } else if (delivery.observationStatus === 'entrego-despues') {
                        reportData[delivery.auxiliar].after++;
                    }
                }
            });
            
            let html = '';
            Object.entries(reportData).forEach(([name, data]) => {
                if (data.total > 0) {
                    html += `
                        <tr>
                            <td>${name}</td>
                            <td>${data.total}</td>
                            <td>${data.completed}</td>
                            <td>${data.pending}</td>
                            <td>${data.onTime}</td>
                            <td>${data.before}</td>
                            <td>${data.after}</td>
                        </tr>
                    `;
                }
            });
            
            tbody.innerHTML = html || '<tr><td colspan="7" style="text-align: center;">No hay datos para mostrar</td></tr>';
        }

        function exportReport() {
            const date = new Date().toLocaleDateString('es-PE');
            const tbody = document.getElementById('reports-tbody');
            
            let content = `REPORTE DE PRODUCCIÓN - ES CONSULTORES\n`;
            content += `Fecha de generación: ${date}\n\n`;
            content += `AUXILIAR | TOTAL | COMPLETADAS | PENDIENTES | A TIEMPO | ANTES | DESPUÉS\n`;
            content += `${'='.repeat(80)}\n`;
            
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 1) {
                    content += `${cells[0].textContent} | ${cells[1].textContent} | ${cells[2].textContent} | ${cells[3].textContent} | ${cells[4].textContent} | ${cells[5].textContent} | ${cells[6].textContent}\n`;
                }
            });
            
            downloadFile(content, `reporte_${date.replace(/\//g, '-')}.txt`);
        }

        function updatePapeleraTab() {
            const auxTbody = document.getElementById('deleted-auxiliares-tbody');
            if (deletedAuxiliares.length > 0) {
                auxTbody.innerHTML = deletedAuxiliares.map(aux => `
                    <tr>
                        <td>${aux.name}</td>
                        <td>${aux.position}</td>
                        <td>${formatDateTime(aux.deletedAt)}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-success" onclick="restoreAuxiliar(${aux.id})">Restaurar</button>
                                <button class="btn btn-danger" onclick="permanentDeleteAuxiliar(${aux.id})">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } else {
                auxTbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">No hay auxiliares eliminados</td></tr>';
            }
            
            const delTbody = document.getElementById('deleted-deliveries-tbody');
            if (deletedDeliveries.length > 0) {
                delTbody.innerHTML = deletedDeliveries.map(del => `
                    <tr>
                        <td>${del.client}</td>
                        <td>${del.taskType}</td>
                        <td>${formatDateTime(del.deletedAt)}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-success" onclick="restoreDelivery(${del.id})">Restaurar</button>
                                <button class="btn btn-danger" onclick="permanentDeleteDelivery(${del.id})">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } else {
                delTbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">No hay entregas eliminadas</td></tr>';
            }
        }

        function restoreAuxiliar(id) {
            const index = deletedAuxiliares.findIndex(a => a.id === id);
            if (index !== -1) {
                const aux = deletedAuxiliares[index];
                delete aux.deletedAt;
                auxiliares.push(aux);
                deletedAuxiliares.splice(index, 1);
                saveToLocalStorage();
                showNotification('Auxiliar restaurado');
                updateAllTabs();
            }
        }

        function permanentDeleteAuxiliar(id) {
            if (confirm('¿Está seguro de eliminar permanentemente este auxiliar?')) {
                const index = deletedAuxiliares.findIndex(a => a.id === id);
                if (index !== -1) {
                    deletedAuxiliares.splice(index, 1);
                    saveToLocalStorage();
                    showNotification('Auxiliar eliminado permanentemente');
                    updateAllTabs();
                }
            }
        }

        function restoreDelivery(id) {
            const index = deletedDeliveries.findIndex(d => d.id === id);
            if (index !== -1) {
                const delivery = deletedDeliveries[index];
                delete delivery.deletedAt;
                deliveries.push(delivery);
                deletedDeliveries.splice(index, 1);
                saveToLocalStorage();
                showNotification('Entrega restaurada');
                updateAllTabs();
            }
        }

        function permanentDeleteDelivery(id) {
            if (confirm('¿Está seguro de eliminar permanentemente esta entrega?')) {
                const index = deletedDeliveries.findIndex(d => d.id === id);
                if (index !== -1) {
                    deletedDeliveries.splice(index, 1);
                    saveToLocalStorage();
                    showNotification('Entrega eliminada permanentemente');
                    updateAllTabs();
                }
            }
        }

        function updateHorasEstandarTab() {
            const categories = {
                'proyecto': 'project-tasks-tbody',
                'informe': 'report-tasks-tbody',
                'correccion': 'correction-tasks-tbody',
                'elaboracion': 'complete-tasks-tbody',
                'forma': 'form-tasks-tbody',
                'culminar': 'finish-tasks-tbody'
            };
            
            Object.entries(categories).forEach(([category, tbodyId]) => {
                const tbody = document.getElementById(tbodyId);
                const tasks = standardTasks[category] || [];
                
                tbody.innerHTML = tasks.map((task, index) => `
                    <tr>
                        <td>${task.name}</td>
                        <td style="text-align: center; font-weight: 600;">${task.hours}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-warning" onclick="editTask('${category}', ${index})">Editar</button>
                                <button class="btn btn-danger" onclick="deleteTask('${category}', ${index})">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            });
        }

        function showAddTaskModal() {
            const categories = ['proyecto', 'informe', 'correccion', 'elaboracion', 'forma', 'culminar'];
            const categoryNames = {
                'proyecto': 'Proyecto',
                'informe': 'Informe',
                'correccion': 'Tareas de Corrección',
                'elaboracion': 'Elaboración Completa',
                'forma': 'Temas de Forma',
                'culminar': 'Culminar y Avances'
            };
            
            const modalHTML = `
                <div class="modal active" id="add-task-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Agregar Nueva Tarea</h2>
                        </div>
                        <form id="add-task-form">
                            <div class="form-group">
                                <label>Categoría</label>
                                <select id="task-category" required>
                                    ${categories.map(cat => `<option value="${cat}">${categoryNames[cat]}</option>`).join('')}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nombre de la Tarea</label>
                                <input type="text" id="task-name" required>
                            </div>
                            <div class="form-group">
                                <label>Horas Estimadas (HH:MM)</label>
                                <input type="text" id="task-hours" pattern="[0-9]{2}:[0-9]{2}" placeholder="05:00" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Agregar Tarea</button>
                                <button type="button" class="btn btn-danger" onclick="closeAddTaskModal()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            document.getElementById('add-task-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const category = document.getElementById('task-category').value;
                const name = document.getElementById('task-name').value;
                const hours = document.getElementById('task-hours').value;
                
                if (!standardTasks[category]) {
                    standardTasks[category] = [];
                }
                
                standardTasks[category].push({ name, hours });
                saveToLocalStorage();
                closeAddTaskModal();
                showNotification('Tarea agregada exitosamente');
                updateAllTabs();
            });
        }
        
        function closeAddTaskModal() {
            const modal = document.getElementById('add-task-modal');
            if (modal) modal.remove();
        }

        function editTask(category, index) {
            const task = standardTasks[category][index];
            const newName = prompt('Nombre de la tarea:', task.name);
            const newHours = prompt('Horas estimadas (HH:MM):', task.hours);
            
            if (newName && newHours) {
                standardTasks[category][index] = { name: newName, hours: newHours };
                saveToLocalStorage();
                showNotification('Tarea actualizada');
                updateAllTabs();
            }
        }

        function deleteTask(category, index) {
            if (confirm('¿Está seguro de eliminar esta tarea?')) {
                standardTasks[category].splice(index, 1);
                saveToLocalStorage();
                showNotification('Tarea eliminada');
                updateAllTabs();
            }
        }

        function updateAuxiliaresTable() {
            const tbody = document.getElementById('auxiliares-tbody');
            const sortedAuxiliares = [...auxiliares].sort((a, b) => a.name.localeCompare(b.name));
            
            tbody.innerHTML = sortedAuxiliares.map(aux => `
                <tr>
                    <td>${aux.name}</td>
                    <td>${aux.position}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-warning" onclick="editAuxiliar(${aux.id})">Editar</button>
                            <button class="btn btn-danger" onclick="deleteAuxiliar(${aux.id})">Eliminar</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function showAddAuxiliarModal() {
            const name = prompt('Nombre completo del auxiliar:');
            const position = prompt('Cargo:');
            
            if (name && position) {
                const newAuxiliar = {
                    id: Date.now(),
                    name: name,
                    position: position
                };
                
                auxiliares.push(newAuxiliar);
                saveToLocalStorage();
                showNotification('Auxiliar agregado exitosamente');
                updateAllTabs();
            }
        }

        function editAuxiliar(id) {
            const aux = auxiliares.find(a => a.id === id);
            if (aux) {
                const newName = prompt('Nombre completo:', aux.name);
                const newPosition = prompt('Cargo:', aux.position);
                
                if (newName && newPosition) {
                    aux.name = newName;
                    aux.position = newPosition;
                    saveToLocalStorage();
                    showNotification('Auxiliar actualizado');
                    updateAllTabs();
                }
            }
        }

        function deleteAuxiliar(id) {
            const index = auxiliares.findIndex(a => a.id === id);
            if (index !== -1) {
                const aux = auxiliares[index];
                aux.deletedAt = new Date().toISOString();
                deletedAuxiliares.push(aux);
                auxiliares.splice(index, 1);
                saveToLocalStorage();
                showNotification('Auxiliar movido a papelera');
                updateAllTabs();
            }
        }

        function createBackup() {
            const backup = {
                version: '1.0',
                date: new Date().toISOString(),
                deliveries,
                auxiliares,
                deletedDeliveries,
                deletedAuxiliares,
                standardTasks,
                supervisorInfo
            };
            
            const json = JSON.stringify(backup, null, 2);
            downloadFile(json, `backup_es_consultores_${new Date().toLocaleDateString('es-PE').replace(/\//g, '-')}.json`);
        }

        function createTextBackup() {
            let content = `RESPALDO DEL SISTEMA - ES CONSULTORES\n`;
            content += `Fecha: ${new Date().toLocaleDateString('es-PE')}\n`;
            content += `${supervisorInfo.position}: ${supervisorInfo.name}\n\n`;
            
            content += `AUXILIARES (${auxiliares.length}):\n`;
            auxiliares.forEach(aux => {
                content += `- ${aux.name} (${aux.position})\n`;
            });
            
            content += `\nENTREGAS (${deliveries.length}):\n`;
            deliveries.forEach(d => {
                content += `\n${'-'.repeat(40)}\n`;
                content += `Cliente: ${d.client}\n`;
                content += `Tarea: ${d.taskType}\n`;
                content += `Auxiliar: ${d.auxiliar}\n`;
                content += `Estado: ${d.status}\n`;
                content += `Fecha de entrega: ${formatDateTime(d.calculatedDelivery)}\n`;
            });
            
            downloadFile(content, `respaldo_texto_${new Date().toLocaleDateString('es-PE').replace(/\//g, '-')}.txt`);
        }

        function restoreBackup(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(event) {
                try {
                    const backup = JSON.parse(event.target.result);
                    
                    if (confirm('¿Está seguro de restaurar este respaldo? Se reemplazarán todos los datos actuales.')) {
                        deliveries = backup.deliveries || [];
                        auxiliares = backup.auxiliares || [];
                        deletedDeliveries = backup.deletedDeliveries || [];
                        deletedAuxiliares = backup.deletedAuxiliares || [];
                        standardTasks = backup.standardTasks || {};
                        supervisorInfo = backup.supervisorInfo || { name: 'Hajid Celis Espinoza', position: 'Supervisor de Producción' };
                        
                        document.getElementById('supervisor-name').value = supervisorInfo.name;
                        document.getElementById('supervisor-position').value = supervisorInfo.position;
                        document.getElementById('supervisor-display').textContent = 
                            `${supervisorInfo.position}: ${supervisorInfo.name}`;
                        
                        saveToLocalStorage();
                        showNotification('Respaldo restaurado exitosamente');
                        updateAllTabs();
                    }
                } catch (error) {
                    alert('Error al leer el archivo de respaldo');
                }
            };
            reader.readAsText(file);
        }

        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return 'N/A';
            const date = new Date(dateTimeStr);
            return date.toLocaleString('es-PE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        }

        function getStatusBadgeClass(status) {
            const classes = {
                'en-curso': 'info',
                'completado': 'success',
                'pausado': 'warning',
                'corrigiendo': 'danger'
            };
            return classes[status] || 'info';
        }

        function getTaskOptions(selectedTask) {
            let options = '';
            Object.entries(standardTasks).forEach(([category, tasks]) => {
                const categoryNames = {
                    'proyecto': 'Proyecto',
                    'informe': 'Informe',
                    'correccion': 'Tareas de Corrección',
                    'elaboracion': 'Elaboración Completa',
                    'forma': 'Temas de Forma',
                    'culminar': 'Culminar y Avances'
                };
                
                options += `<optgroup label="${categoryNames[category] || category}">`;
                tasks.forEach(task => {
                    options += `<option value="${task.name}" ${task.name === selectedTask ? 'selected' : ''}>${task.name}</option>`;
                });
                options += '</optgroup>';
            });
            return options;
        }

        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        function downloadFile(content, filename) {
            const blob = new Blob([content], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function closeModal() {
            const modal = document.getElementById('edit-modal');
            if (modal) modal.remove();
        }

        function populateSelects() {
            const auxSelect = document.getElementById('auxiliar-select');
            if (auxSelect) {
                auxSelect.innerHTML = '<option value="">Seleccione un auxiliar</option>';
                const sortedAuxiliares = [...auxiliares].sort((a, b) => a.name.localeCompare(b.name));
                sortedAuxiliares.forEach(aux => {
                    auxSelect.innerHTML += `<option value="${aux.name}">${aux.name}</option>`;
                });
            }
            
            const taskSelect = document.getElementById('task-type-select');
            if (taskSelect) {
                taskSelect.innerHTML = '<option value="">Seleccione una tarea</option>' + getTaskOptions('');
            }
        }

        function updateAllTabs() {
            updateAnalysisStats();
            updateDeliveriesTables();
            updateControlTable();
            updatePapeleraTab();
            updateHorasEstandarTab();
            updateAuxiliaresTable();
            generateCalendar();
            populateSelects();
        }

        function saveToLocalStorage() {
            deliveries.forEach(d => {
                if (!d.deliveryStatus || d.deliveryStatus === 'pendiente') {
                    d.deliveryStatus = 'no-entregado';
                }
            });
            
            localStorage.setItem('es_consultores_deliveries', JSON.stringify(deliveries));
            localStorage.setItem('es_consultores_auxiliares', JSON.stringify(auxiliares));
            localStorage.setItem('es_consultores_deleted_deliveries', JSON.stringify(deletedDeliveries));
            localStorage.setItem('es_consultores_deleted_auxiliares', JSON.stringify(deletedAuxiliares));
            localStorage.setItem('es_consultores_standard_tasks', JSON.stringify(standardTasks));
            localStorage.setItem('es_consultores_supervisor', JSON.stringify(supervisorInfo));
        }

        function loadFromLocalStorage() {
            const savedDeliveries = localStorage.getItem('es_consultores_deliveries');
            const savedAuxiliares = localStorage.getItem('es_consultores_auxiliares');
            const savedDeletedDeliveries = localStorage.getItem('es_consultores_deleted_deliveries');
            const savedDeletedAuxiliares = localStorage.getItem('es_consultores_deleted_auxiliares');
            const savedStandardTasks = localStorage.getItem('es_consultores_standard_tasks');
            const savedSupervisor = localStorage.getItem('es_consultores_supervisor');
            
            if (savedDeliveries) {
                deliveries = JSON.parse(savedDeliveries);
                deliveries.forEach(d => {
                    if (!d.deliveryStatus || d.deliveryStatus === 'pendiente') {
                        d.deliveryStatus = 'no-entregado';
                    }
                    if (!d.observationStatus) {
                        d.observationStatus = 'en-desarrollo';
                    }
                });
            }
            if (savedAuxiliares) auxiliares = JSON.parse(savedAuxiliares);
            if (savedDeletedDeliveries) deletedDeliveries = JSON.parse(savedDeletedDeliveries);
            if (savedDeletedAuxiliares) deletedAuxiliares = JSON.parse(savedDeletedAuxiliares);
            if (savedStandardTasks) standardTasks = JSON.parse(savedStandardTasks);
            if (savedSupervisor) {
                supervisorInfo = JSON.parse(savedSupervisor);
                document.getElementById('supervisor-name').value = supervisorInfo.name;
                document.getElementById('supervisor-position').value = supervisorInfo.position;
                document.getElementById('supervisor-display').textContent = 
                    `${supervisorInfo.position}: ${supervisorInfo.name}`;
            }
        }
    </script>
</body>
</html>