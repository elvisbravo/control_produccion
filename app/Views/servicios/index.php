<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - Control de Producción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<body>
    <?php include(APPPATH . 'Views/layouts/sidebar.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestión de Servicios</h1>
                    <div>
                        <a href="/servicios/nuevo" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nuevo Servicio
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="filtro-estado">
                                    <option value="">Todos</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="En Proceso">En Proceso</option>
                                    <option value="En Revisión">En Revisión</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Entregado">Entregado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jefe de Producción</label>
                                <select class="form-select" id="filtro-jefe">
                                    <option value="">Todos</option>
                                    <?php foreach ($jefes as $jefe): ?>
                                        <option value="<?= $jefe['id'] ?>"><?= $jefe['nombres'] . ' ' . $jefe['apellidos'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Auxiliar de Producción</label>
                                <select class="form-select" id="filtro-auxiliar">
                                    <option value="">Todos</option>
                                    <?php foreach ($auxiliares as $aux): ?>
                                        <option value="<?= $aux['id'] ?>"><?= $aux['nombres'] . ' ' . $aux['apellidos'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-primary w-100" onclick="cargarServicios()">
                                    <i class="fas fa-filter me-2"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Servicios -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla-servicios" class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Título</th>
                                        <th>Cliente</th>
                                        <th>Auxiliar</th>
                                        <th>Fecha Límite</th>
                                        <th>Estado</th>
                                        <th>Horas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Cambiar Estado -->
    <div class="modal fade" id="modalEstado" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="servicio-id-estado">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado</label>
                        <select class="form-select" id="nuevo-estado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="En Proceso">En Proceso</option>
                            <option value="En Revisión">En Revisión</option>
                            <option value="Completado">Completado</option>
                            <option value="Entregado">Entregado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comentario (opcional)</label>
                        <textarea class="form-control" id="comentario-estado" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCambioEstado()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let tablaServicios;

        $(document).ready(function() {
            cargarServicios();
        });

        function cargarServicios() {
            const filtros = {
                estado: $('#filtro-estado').val(),
                jefe_id: $('#filtro-jefe').val(),
                auxiliar_id: $('#filtro-auxiliar').val()
            };

            if (tablaServicios) {
                tablaServicios.destroy();
            }

            tablaServicios = $('#tabla-servicios').DataTable({
                ajax: {
                    url: '/servicios/all',
                    data: filtros,
                    dataSrc: ''
                },
                columns: [{
                        data: 'codigo'
                    },
                    {
                        data: 'titulo'
                    },
                    {
                        data: 'cliente'
                    },
                    {
                        data: 'auxiliar_produccion',
                        defaultContent: '<span class="text-muted">Sin asignar</span>'
                    },
                    {
                        data: 'fecha_limite',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">Sin fecha</span>';
                            const fecha = new Date(data);
                            return fecha.toLocaleDateString('es-PE');
                        }
                    },
                    {
                        data: 'estado',
                        render: function(data) {
                            const badges = {
                                'Pendiente': 'secondary',
                                'En Proceso': 'primary',
                                'En Revisión': 'warning',
                                'Completado': 'success',
                                'Entregado': 'info'
                            };
                            return `<span class="badge bg-${badges[data] || 'secondary'}">${data}</span>`;
                        }
                    },
                    {
                        data: 'horas_estimadas',
                        render: function(data) {
                            return parseFloat(data).toFixed(1) + 'h';
                        }
                    },
                    {
                        data: null,
                        render: function(row) {
                            return `
                                <button class="btn btn-sm btn-warning" onclick="cambiarEstado(${row.id}, '${row.estado}')">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <a href="/servicios/nuevo?id=${row.id}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [
                    [4, 'asc']
                ]
            });
        }

        function cambiarEstado(id, estadoActual) {
            $('#servicio-id-estado').val(id);
            $('#nuevo-estado').val(estadoActual);
            $('#comentario-estado').val('');
            new bootstrap.Modal(document.getElementById('modalEstado')).show();
        }

        function guardarCambioEstado() {
            const data = {
                servicio_id: $('#servicio-id-estado').val(),
                estado: $('#nuevo-estado').val(),
                comentario: $('#comentario-estado').val()
            };

            $.ajax({
                url: '/servicios/cambiar-estado',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Éxito', response.message, 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalEstado')).hide();
                        cargarServicios();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al cambiar el estado', 'error');
                }
            });
        }
    </script>
</body>

</html>