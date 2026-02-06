<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Control de Producción</title>
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
                    <h1 class="h2">Gestión de Clientes</h1>
                    <button class="btn btn-primary" onclick="nuevoCliente()">
                        <i class="fas fa-plus me-2"></i>Nuevo Cliente
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla-clientes" class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Empresa</th>
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

    <!-- Modal Cliente -->
    <div class="modal fade" id="modalCliente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteTitle">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-cliente">
                    <div class="modal-body">
                        <input type="hidden" name="cliente_id" id="cliente-id" value="0">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" name="nombres" id="cliente-nombres" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" name="apellidos" id="cliente-apellidos" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="cliente-email" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" id="cliente-telefono" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Empresa</label>
                                    <input type="text" name="empresa" id="cliente-empresa" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion" id="cliente-direccion" class="form-control">
                                </div>
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
        let tablaClientes;
        let modalCliente;

        $(document).ready(function() {
            modalCliente = new bootstrap.Modal(document.getElementById('modalCliente'));
            cargarClientes();
        });

        function cargarClientes() {
            if (tablaClientes) {
                tablaClientes.destroy();
            }

            tablaClientes = $('#tabla-clientes').DataTable({
                ajax: {
                    url: '/clientes/all',
                    dataSrc: ''
                },
                columns: [{
                        data: 'nombres'
                    },
                    {
                        data: 'apellidos'
                    },
                    {
                        data: 'email',
                        defaultContent: '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'telefono',
                        defaultContent: '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'empresa',
                        defaultContent: '<span class="text-muted">-</span>'
                    },
                    {
                        data: null,
                        render: function(row) {
                            return `
                                <button class="btn btn-sm btn-primary" onclick="editarCliente(${row.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarCliente(${row.id}, '${row.nombres} ${row.apellidos}')">
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

        function nuevoCliente() {
            $('#modalClienteTitle').text('Nuevo Cliente');
            $('#form-cliente')[0].reset();
            $('#cliente-id').val('0');
            modalCliente.show();
        }

        function editarCliente(id) {
            $.ajax({
                url: `/clientes/${id}`,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        const cliente = response.data;
                        $('#modalClienteTitle').text('Editar Cliente');
                        $('#cliente-id').val(cliente.id);
                        $('#cliente-nombres').val(cliente.nombres);
                        $('#cliente-apellidos').val(cliente.apellidos);
                        $('#cliente-email').val(cliente.email);
                        $('#cliente-telefono').val(cliente.telefono);
                        $('#cliente-empresa').val(cliente.empresa);
                        $('#cliente-direccion').val(cliente.direccion);
                        modalCliente.show();
                    }
                }
            });
        }

        function eliminarCliente(id, nombre) {
            Swal.fire({
                title: '¿Está seguro?',
                text: `¿Desea eliminar al cliente ${nombre}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/clientes/eliminar/${id}`,
                        method: 'GET',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Eliminado', response.message, 'success');
                                cargarClientes();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        }
                    });
                }
            });
        }

        $('#form-cliente').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '/clientes/guardar',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Éxito', response.message, 'success');
                        modalCliente.hide();
                        cargarClientes();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al guardar el cliente', 'error');
                }
            });
        });
    </script>
</body>

</html>