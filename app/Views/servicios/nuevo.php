<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Servicio - Control de Producción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>

<body>
    <?php include(APPPATH . 'Views/layouts/sidebar.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Nuevo Servicio</h1>
                    <a href="/servicios" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="form-servicio">
                            <input type="hidden" name="servicio_id" value="0">

                            <!-- Información del Cliente -->
                            <h5 class="mb-3 text-primary"><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                        <select name="cliente_id" id="cliente-select" class="form-select" required>
                                            <option value="">Seleccione un cliente...</option>
                                            <?php foreach ($clientes as $cliente): ?>
                                                <option value="<?= $cliente['id'] ?>">
                                                    <?= $cliente['nombres'] . ' ' . $cliente['apellidos'] ?>
                                                    <?php if (!empty($cliente['empresa'])): ?>
                                                        - <?= $cliente['empresa'] ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Puede buscar por nombre o empresa</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Captador <span class="text-danger">*</span></label>
                                        <select name="captador_id" class="form-select" required>
                                            <?php foreach ($captadores as $captador): ?>
                                                <option value="<?= $captador['id'] ?>" <?= (session()->get('perfil_id') == 2 && session()->get('id') == $captador['id']) ? 'selected' : '' ?>>
                                                    <?= $captador['nombres'] . ' ' . $captador['apellidos'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Servicio -->
                            <h5 class="mb-3 mt-4 text-primary"><i class="fas fa-briefcase me-2"></i>Información del Servicio</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                                        <select name="tipo_servicio_id" id="tipo-servicio" class="form-select" required>
                                            <option value="">Seleccione...</option>
                                            <?php foreach ($tipos_servicio as $tipo): ?>
                                                <option value="<?= $tipo['id'] ?>" data-horas="<?= $tipo['horas_estimadas_base'] ?>">
                                                    <?= $tipo['nombre'] ?> (<?= $tipo['horas_estimadas_base'] ?>h base)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Prioridad</label>
                                        <select name="prioridad" class="form-select">
                                            <option value="Baja">Baja</option>
                                            <option value="Media" selected>Media</option>
                                            <option value="Alta">Alta</option>
                                            <option value="Urgente">Urgente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Título del Servicio <span class="text-danger">*</span></label>
                                        <input type="text" name="titulo" class="form-control" placeholder="Ej: Tesis de Maestría en Administración" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción detallada del servicio..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Planificación -->
                            <h5 class="mb-3 mt-4 text-primary"><i class="fas fa-calendar-alt me-2"></i>Planificación</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Horas Estimadas <span class="text-danger">*</span></label>
                                        <input type="number" name="horas_estimadas" id="horas-estimadas"
                                            class="form-control" step="0.5" min="0.5" placeholder="20.0" required>
                                        <small class="text-muted">Horas totales para completar el trabajo</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_inicio" id="fecha-inicio"
                                            class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha Límite (Cliente)</label>
                                        <input type="date" name="fecha_limite" class="form-control">
                                        <small class="text-muted">Fecha comprometida con el cliente</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Asignación de Personal -->
                            <h5 class="mb-3 mt-4 text-primary"><i class="fas fa-users me-2"></i>Asignación de Personal</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jefe de Producción</label>
                                        <select name="jefe_produccion_id" class="form-select">
                                            <option value="">Sin asignar</option>
                                            <?php foreach ($jefes as $jefe): ?>
                                                <option value="<?= $jefe['id'] ?>">
                                                    <?= $jefe['nombres'] . ' ' . $jefe['apellidos'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Responsable de revisar el trabajo</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Auxiliar de Producción</label>
                                        <select name="auxiliar_produccion_id" id="auxiliar-produccion" class="form-select">
                                            <option value="">Sin asignar</option>
                                            <?php foreach ($auxiliares as $aux): ?>
                                                <option value="<?= $aux['id'] ?>">
                                                    <?= $aux['nombres'] . ' ' . $aux['apellidos'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Responsable de ejecutar el trabajo</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Fecha Calculada Automáticamente -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info d-none" id="fecha-calculada-display">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-calculator me-2"></i>Fecha de Entrega Calculada
                                        </h6>
                                        <p class="mb-0">
                                            <strong id="fecha-calculada-texto"></strong>
                                        </p>
                                        <small class="text-muted">
                                            Calculada considerando horario laboral (L-V: 8-13, 15-19; S: 8-13),
                                            feriados y cumpleaños del auxiliar
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Observaciones</label>
                                        <textarea name="observaciones" class="form-control" rows="3"
                                            placeholder="Notas adicionales, requerimientos especiales, etc..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Guardar Servicio
                                </button>
                                <a href="/servicios" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inicializar Select2 para búsqueda de clientes
            $('#cliente-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Buscar cliente...',
                allowClear: true
            });

            // Auto-rellenar horas al seleccionar tipo de servicio
            $('#tipo-servicio').on('change', function() {
                const horasBase = $(this).find(':selected').data('horas');
                if (horasBase) {
                    $('#horas-estimadas').val(horasBase);
                    calcularFechaEntrega();
                }
            });

            // Calcular fecha automáticamente al cambiar parámetros
            $('#horas-estimadas, #auxiliar-produccion, #fecha-inicio').on('change', function() {
                calcularFechaEntrega();
            });
        });

        function calcularFechaEntrega() {
            const horas = $('#horas-estimadas').val();
            const auxiliarId = $('#auxiliar-produccion').val();
            const fechaInicio = $('#fecha-inicio').val();

            if (!horas || !auxiliarId || !fechaInicio) {
                $('#fecha-calculada-display').addClass('d-none');
                return;
            }

            $.ajax({
                url: '/servicios/calcular-fecha',
                method: 'POST',
                data: {
                    horas_estimadas: horas,
                    auxiliar_id: auxiliarId,
                    fecha_inicio: fechaInicio
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#fecha-calculada-texto').text(response.fecha_entrega_formato);
                        $('#fecha-calculada-display').removeClass('d-none');
                    }
                },
                error: function() {
                    $('#fecha-calculada-display').addClass('d-none');
                }
            });
        }

        $('#form-servicio').on('submit', function(e) {
            e.preventDefault();

            // Validar que se haya seleccionado un cliente
            if (!$('#cliente-select').val()) {
                Swal.fire('Error', 'Debe seleccionar un cliente', 'error');
                return;
            }

            const btnSubmit = $(this).find('button[type="submit"]');
            btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');

            $.ajax({
                url: '/servicios/guardar',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '/servicios';
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                        btnSubmit.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Servicio');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al guardar el servicio', 'error');
                    btnSubmit.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Servicio');
                }
            });
        });
    </script>
</body>

</html>