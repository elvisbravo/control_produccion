<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Control de Producción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/custom.css">
    <style>
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-card p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .bg-pendiente {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        }

        .bg-proceso {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .bg-revision {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }

        .bg-completado {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        }

        .bg-entregado {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }

        .bg-atrasado {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge-prioridad {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block bg-dark sidebar vh-100">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">Control de Producción</h5>
                        <small class="text-muted">ES Consultores</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="/dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="/servicios">
                                <i class="fas fa-briefcase me-2"></i> Servicios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="/clientes">
                                <i class="fas fa-users me-2"></i> Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="/calendario">
                                <i class="fas fa-calendar-alt me-2"></i> Calendario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="/feriados">
                                <i class="fas fa-calendar-times me-2"></i> Feriados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="/usuarios">
                                <i class="fas fa-user-cog me-2"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item mt-auto">
                            <a class="nav-link text-danger" href="/auth/logout">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="text-muted">
                        <i class="fas fa-user me-2"></i>
                        <?= $usuario['nombre'] . ' ' . $usuario['apellidos'] ?>
                        <span class="badge bg-primary ms-2"><?= $usuario['perfil'] ?></span>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="row">
                    <div class="col-md-2">
                        <div class="stat-card bg-pendiente">
                            <h3 id="stat-pendientes"><?= $estadisticas['pendientes'] ?></h3>
                            <p>Pendientes</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card bg-proceso">
                            <h3 id="stat-proceso"><?= $estadisticas['en_proceso'] ?></h3>
                            <p>En Proceso</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card bg-revision">
                            <h3 id="stat-revision"><?= $estadisticas['en_revision'] ?></h3>
                            <p>En Revisión</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card bg-completado">
                            <h3 id="stat-completados"><?= $estadisticas['completados'] ?></h3>
                            <p>Completados</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card bg-entregado">
                            <h3 id="stat-entregados"><?= $estadisticas['entregados'] ?></h3>
                            <p>Entregados</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card bg-atrasado">
                            <h3 id="stat-atrasados"><?= $estadisticas['atrasados'] ?></h3>
                            <p>Atrasados</p>
                        </div>
                    </div>
                </div>

                <!-- Gráficos y Carga de Trabajo -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribución de Trabajos</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartEstados"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Carga por Auxiliar</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartAuxiliares"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Próximos Vencimientos -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Próximos Vencimientos (7 días)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Título</th>
                                                <th>Cliente</th>
                                                <th>Auxiliar</th>
                                                <th>Fecha Límite</th>
                                                <th>Estado</th>
                                                <th>Horas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($proximos_vencimientos)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay trabajos próximos a vencer</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($proximos_vencimientos as $servicio): ?>
                                                    <tr>
                                                        <td><strong><?= $servicio['codigo'] ?></strong></td>
                                                        <td><?= $servicio['titulo'] ?></td>
                                                        <td><?= $servicio['cliente_nombres'] . ' ' . $servicio['cliente_apellidos'] ?></td>
                                                        <td><?= $servicio['auxiliar_nombre'] ?? '<span class="text-muted">Sin asignar</span>' ?></td>
                                                        <td>
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            <?= date('d/m/Y', strtotime($servicio['fecha_limite'])) ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $badgeClass = match ($servicio['estado']) {
                                                                'Pendiente' => 'secondary',
                                                                'En Proceso' => 'primary',
                                                                'En Revisión' => 'warning',
                                                                'Completado' => 'success',
                                                                'Entregado' => 'info',
                                                                default => 'secondary'
                                                            };
                                                            ?>
                                                            <span class="badge bg-<?= $badgeClass ?>"><?= $servicio['estado'] ?></span>
                                                        </td>
                                                        <td><?= number_format($servicio['horas_estimadas'], 1) ?>h</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carga de Trabajo Detallada -->
                <div class="row mt-4 mb-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Carga de Trabajo por Auxiliar</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Auxiliar</th>
                                                <th class="text-center">Total Trabajos</th>
                                                <th class="text-center">Pendientes</th>
                                                <th class="text-center">En Proceso</th>
                                                <th class="text-center">En Revisión</th>
                                                <th class="text-center">Total Horas</th>
                                                <th class="text-center">Horas Pendientes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($carga_auxiliares)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay auxiliares con trabajos asignados</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($carga_auxiliares as $aux): ?>
                                                    <tr>
                                                        <td><strong><?= $aux['auxiliar'] ?></strong></td>
                                                        <td class="text-center"><span class="badge bg-secondary"><?= $aux['total_trabajos'] ?></span></td>
                                                        <td class="text-center"><span class="badge bg-secondary"><?= $aux['pendientes'] ?></span></td>
                                                        <td class="text-center"><span class="badge bg-primary"><?= $aux['en_proceso'] ?></span></td>
                                                        <td class="text-center"><span class="badge bg-warning"><?= $aux['en_revision'] ?></span></td>
                                                        <td class="text-center"><?= number_format($aux['total_horas_asignadas'], 1) ?>h</td>
                                                        <td class="text-center"><strong><?= number_format($aux['horas_pendientes'], 1) ?>h</strong></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Estados
        const ctxEstados = document.getElementById('chartEstados').getContext('2d');
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'En Proceso', 'En Revisión', 'Completados', 'Entregados'],
                datasets: [{
                    data: [
                        <?= $estadisticas['pendientes'] ?>,
                        <?= $estadisticas['en_proceso'] ?>,
                        <?= $estadisticas['en_revision'] ?>,
                        <?= $estadisticas['completados'] ?>,
                        <?= $estadisticas['entregados'] ?>
                    ],
                    backgroundColor: [
                        '#6c757d',
                        '#007bff',
                        '#ffc107',
                        '#28a745',
                        '#17a2b8'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Auxiliares
        const ctxAuxiliares = document.getElementById('chartAuxiliares').getContext('2d');
        new Chart(ctxAuxiliares, {
            type: 'bar',
            data: {
                labels: [<?php foreach ($carga_auxiliares as $aux): ?> '<?= $aux['auxiliar'] ?>', <?php endforeach; ?>],
                datasets: [{
                    label: 'Horas Pendientes',
                    data: [<?php foreach ($carga_auxiliares as $aux): ?><?= $aux['horas_pendientes'] ?>, <?php endforeach; ?>],
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Horas'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>

</html>