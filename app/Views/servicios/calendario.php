<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - Control de Producción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .fc-event {
            cursor: pointer;
        }

        .fc-daygrid-event {
            white-space: normal !important;
            align-items: normal !important;
        }

        .legend {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <?php include(APPPATH . 'Views/layouts/sidebar.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Calendario de Producción</h1>
                    <a href="/servicios" class="btn btn-secondary">
                        <i class="fas fa-list me-2"></i>Ver Lista
                    </a>
                </div>

                <!-- Selector de Auxiliar -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label"><strong>Seleccionar Auxiliar:</strong></label>
                                <select id="select-auxiliar" class="form-select">
                                    <?php foreach ($auxiliares as $aux): ?>
                                        <option value="<?= $aux['id'] ?>" <?= ($auxiliar_seleccionado == $aux['id']) ? 'selected' : '' ?>>
                                            <?= $aux['nombres'] . ' ' . $aux['apellidos'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label"><strong>Leyenda:</strong></label>
                                <div class="legend">
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #6c757d;"></div>
                                        <span>Pendiente</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #007bff;"></div>
                                        <span>En Proceso</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #ffc107;"></div>
                                        <span>En Revisión</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #28a745;"></div>
                                        <span>Completado</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #17a2b8;"></div>
                                        <span>Entregado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendario -->
                <div id="calendar"></div>
            </main>
        </div>
    </div>

    <!-- Modal Detalles del Servicio -->
    <div class="modal fade" id="modalDetalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-content">
                    <!-- Se llenará dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/es.global.min.js'></script>

    <script>
        let calendar;
        let modalDetalles;

        document.addEventListener('DOMContentLoaded', function() {
            modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));
            const calendarEl = document.getElementById('calendar');
            const auxiliarId = document.getElementById('select-auxiliar').value;

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    list: 'Lista'
                },
                height: 'auto',
                events: `/calendario/eventos/${auxiliarId}`,
                eventClick: function(info) {
                    mostrarDetalles(info.event);
                },
                eventDidMount: function(info) {
                    // Tooltip al pasar el mouse
                    info.el.title = `${info.event.title}\nCliente: ${info.event.extendedProps.cliente}\nEstado: ${info.event.extendedProps.estado}`;
                }
            });

            calendar.render();
        });

        $('#select-auxiliar').on('change', function() {
            const auxiliarId = $(this).val();
            window.location.href = `/calendario/${auxiliarId}`;
        });

        function mostrarDetalles(event) {
            const props = event.extendedProps;
            const html = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Código:</strong> ${props.codigo}</p>
                        <p><strong>Título:</strong> ${event.title}</p>
                        <p><strong>Cliente:</strong> ${props.cliente}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado:</strong> <span class="badge" style="background-color: ${event.backgroundColor}">${props.estado}</span></p>
                        <p><strong>Horas Estimadas:</strong> ${props.horas}h</p>
                        <p><strong>Fecha Inicio:</strong> ${formatDate(event.start)}</p>
                        ${event.end ? `<p><strong>Fecha Fin:</strong> ${formatDate(event.end)}</p>` : ''}
                    </div>
                </div>
            `;

            $('#detalles-content').html(html);
            modalDetalles.show();
        }

        function formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('es-PE', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    </script>
</body>

</html>