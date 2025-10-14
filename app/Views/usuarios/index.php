<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>

<link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="fs-5 pe-3">Usuarios</div>
</div>
<!--end breadcrumb-->
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div>
                <h6 class="mb-0">Lista de Usuarios</h6>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-sm btn-outline-success" id="btnAdd">Nuevo Usuario</button>
            </div>
        </div>
    </div>
    <div class="card-body">

        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyUsuarios">
                    <!-- Aquí se llenarán los datos dinámicamente -->

                </tbody>

            </table>
        </div>
    </div>
</div>
<!--end row-->

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUsuario">
                <input type="hidden" id="idUsuario" name="idUsuario" value="0">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="input1" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre del usuario">
                        </div>
                        <div class="col-md-12">
                            <label for="input1" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" id="apellidos" placeholder="Apellidos del usuario">
                        </div>
                        <div class="col-md-12">
                            <label for="input2" class="form-label">Cargo</label>
                            <select id="cargo" name="cargo" class="form-select">
                                <option value="" selected>Seleccione el cargo</option>
                                <?php foreach($perfiles as $perfil): ?>
                                    <option value="<?= $perfil['id'] ?>"><?= $perfil['nombre_perfil'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="input1" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo del usuario">
                        </div>
                        <div class="col-md-12">
                            <label for="input1" class="form-label">Contraseña</label>
                            <input type="text" class="form-control" id="password" name="password" placeholder="Contraseña del usuario">
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>

<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>

<script src="js/usuarios/user.js"></script>

<?= $this->endSection() ?>