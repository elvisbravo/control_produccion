<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>

<link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="fs-5 pe-3">Control de Entregas</div>
</div>
<!--end breadcrumb-->
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div>
                <h6 class="mb-0">Lista de Entregas</h6>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-sm btn-outline-success" id="btnAdd">Nueva Entrega</button>
            </div>
        </div>
    </div>
    <div class="card-body">

        <div class="row">
            <div class="col-md-3 mb-3">
                <select class="form-select" name="" id="">
                    <option value="">Todos los periodos</option>
                    <option value="">Día</option>
                    <option value="">Semana</option>
                    <option value="">Mes</option>
                    <option value="">Año</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <input type="date" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <button class="btn btn-primary px-5">Filtrar</button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
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
                <tbody>

                </tbody>

            </table>
        </div>
    </div>
</div>
<!--end row-->

<div class="modal fade" id="modalEntrega" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Entrega</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEntrega">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="input1" class="form-label">Fecha y Hora</label>
                            <input type="datetime-local" class="form-control" id="fecha_hora_inicio" value="<?= date('Y-m-d H:i:s') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label for="input2" class="form-label">Auxiliar</label>
                            <select id="input7" class="form-select" required>
                                <option value="">Seleccione un Auxiliar</option>
                                <?php foreach ($usuarios as $user) : ?>
                                    <option value="<?= $user['id'] ?>"><?= $user['nombres'] . ' ' . $user['apellidos'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="input3" class="form-label">Tipo de Tarea</label>
                            <select id="input7" class="form-select" required>
                                <option value="">Seleccione una categoría</option>

                                <?php foreach ($categorias as $category) { ?>
                                    <?php $tareas = $category['tareas'] ?>
                                    <optgroup label="<?= $category['nombre_categoria'] ?>">

                                        <?php foreach ($tareas as $key => $value) { ?>
                                            <option value="<?= $value['id'] ?>"><?= $value['nombre_tarea'] ?></option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="input4" class="form-label">Título de Entrega</label>
                            <input type="text" class="form-control" id="input4" placeholder="titulo" required>
                        </div>
                        <div class="col-md-12">
                            <label for="input5" class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="input5" placeholder="Seleccione el cliente" required>
                        </div>
                        <div class="col-md-12">
                            <label for="input6" class="form-label">Tiempo Estimado (HH:MM)</label>
                            <input type="text" class="form-control" id="estimated-time" pattern="[0-9]{2}:[0-9]{2}" placeholder="05:00" required>
                        </div>
                        <div class="col-md-12">
                            <label for="input7" class="form-label">Estado</label>
                            <select id="input7" class="form-select">
                                <option>En Curso</option>
                                <option>Pausado</option>
                                <option>Completado</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label for="input6" class="form-label">Fecha y Hora de Entrega Calculada</label>
                            <input type="datetime-local" class="form-control" id="input6" required>
                        </div>

                        <div class="col-md-12">
                            <label for="input8" class="form-label">Link de Google Drive</label>
                            <input type="text" class="form-control" id="input8" placeholder="Link">
                        </div>
                        <div class="col-md-12">
                            <label for="input9" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="input11" placeholder="Escriba sus observaciones aquí..." rows="3"></textarea>
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

<script src="js/entregas/control_entregas.js"></script>

<?= $this->endSection() ?>