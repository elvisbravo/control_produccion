<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>

<link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="fs-5 pe-3">Tareas</div>
</div>
<!--end breadcrumb-->
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center px-3 py-2">
            <div>
                <h6 class="mb-0">Gestión de Horas Estándar por Tarea</h6>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-sm btn-outline-success" id="btnAdd">Nueva Tarea</button>
            </div>
        </div>
    </div>
</div>
<!--end row-->

<div class="row" id="tareas">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center px-3 py-2">
                    <div>
                        <h6 class="mb-0">Proyecto</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Tarea</th>
                                <th>Horas</th>
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
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center px-3 py-2">
                    <div>
                        <h6 class="mb-0">Proyecto</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Tarea</th>
                                <th>Horas</th>
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
    </div>

</div>

<div class="modal fade" id="modalTarea" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTarea">
                <input type="hidden" id="idTarea" name="idTarea" value="0">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select id="categoria" name="categoria" class="form-select">
                                <option value="" selected>Seleccione la categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre_categoria'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="name_tarea" class="form-label">Nombre de la Tarea</label>
                            <input type="text" class="form-control" name="name_tarea" id="name_tarea" placeholder="Ingrese el nombre de la tarea">
                        </div>
                        <div class="col-md-12">
                            <label for="horas_estimadas" class="form-label">Horas Estimadas (HH:MM)</label>
                            <input type="text" class="form-control" name="horas_estimadas" id="horas_estimadas" pattern="[0-9]{2}:[0-9]{2}" placeholder="05:00" required="">
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

<script src="js/tareas/index.js"></script>

<?= $this->endSection() ?>