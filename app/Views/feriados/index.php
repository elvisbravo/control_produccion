<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feriados - Control de Producción</title>
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
                    <h1 class="h2">Gestión de Feriados</h1>
                    <div>
                        <select id="filtro-anio" class="form-select d-inline-block" style="width: auto;">
                            <?php for ($i = date('Y') - 1; $i <= date('Y') + 2; $i++): ?>
                                <option value="<?= $i ?>" <?= ($i == date('Y')) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <button class="btn btn-primary ms-2" onclick="nuevoFeriado()">
                            <i class="fas fa-plus me-2"></i>Nuevo Feriado
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla-feriados" class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>¿Es Laborable?</th>
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

    <!-- Modal Feriado -->
    <div class="modal fade" id="modalFeriado" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFeriadoTitle">Nuevo Feriado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-feriado">
                    <div class="modal-body">
                        <input type="hidden" name="feriado_id" id="feriado-id" value="0">

                        <div class="mb-3">
                            <label class="form-label">Nombre del Feriado <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="feriado-nombre" class="form-control"
                                placeholder="Ej: Año Nuevo" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="feriado-fecha" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" id="feriado-tipo" class="form-select">
                                <option value="Nacional">Nacional</option>
                                <option value="Regional">Regional</option>
                                <option value="Local">Local</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="es_laborable"
                                    id="feriado-laborable" value="1">
                                <label class="form-check-label" for="feriado-laborable">
                                    ¿Es día laborable?
                                </label>
                                <small class="form-text text-muted d-block">
                                    Marque si se trabaja este día a pesar de ser feriado
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let tablaFeriados;
        let modalFeriado;

        $(document).ready(function() {
            modalFeriado = new bootstrap.Modal(document.getElementById('modalFeriado'));
            cargarFeriados();

            $('#filtro-anio').on('change', function() {
                cargarFeriados();
            });
        });

        function cargarFeriados() {
            const anio = $('#filtro-anio').val();

            if (tablaFeriados) {
                tablaFeriados.destroy();
            }

            tablaFeriados = $('#tabla-feriados').DataTable({
                ajax: {
                    url: '/feriados/all',
                    data: {
                        anio: anio
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'fecha',
                        render: function(data) {
                            const fecha = new Date(data + 'T00:00:00');
                            return fecha.toLocaleDateString('es-PE', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                        }
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'tipo',
                        render: function(data) {
                            const badges = {
                                'Nacional': 'primary',
                                'Regional': 'info',
                                'Local': 'secondary'
                            };
                            return `<span class="badge bg-${badges[data]}">${data}</span>`;
                        }
                    },
                    {
                        data: 'es_laborable',
                        render: function(data) {
                            return data == 1 ?
                                '<span class="badge bg-success">Sí</span>' :
                                '<span class="badge bg-danger">No</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(row) {
                            return `
                                <button class="btn btn-sm btn-primary" onclick="editarFeriado(${row.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarFeriado(${row.id}, '${row.nombre}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [
                    [0, 'asc']
                ]
            });
        }

        function nuevoFeriado() {
            $('#modalFeriadoTitle').text('Nuevo Feriado');
            $('#form-feriado')[0].reset();
            $('#feriado-id').val('0');
            modalFeriado.show();
        }

        function editarFeriado(id) {
            $.ajax({
                url: `/feriados/${id}`,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        const feriado = response.data;
                        $('#modalFeriadoTitle').text('Editar Feriado');
                        $('#feriado-id').val(feriado.id);
                        $('#feriado-nombre').val(feriado.nombre);
                        $('#feriado-fecha').val(feriado.fecha);
                        $('#feriado-tipo').val(feriado.tipo);
                        $('#feriado-laborable').prop('checked', feriado.es_laborable == 1);
                        modalFeriado.show();
                    }
                }
            });
        }

        function eliminarFeriado(id, nombre) {
            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea eliminar el feriado "${nombre}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/feriados/eliminar/${id}`,
                        method: 'GET',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Eliminado', response.message, 'success');
                                cargarFeriados();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        }
                    });
                }
            });
        }

        $('#form-feriado').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '/feriados/guardar',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Éxito', response.message, 'success');
                        modalFeriado.hide();
                        cargarFeriados();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al guardar el feriado', 'error');
                }
            });
        });
    </script>
</body>

</html>