<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="fs-5 pe-3">Nueva Entrega</div>
</div>
<!--end breadcrumb-->
<div class="row">
    <div class="col-md-12 mx-auto">

        <div class="card">
            <div class="card-body p-4">
                <h5 class="mb-4">Agregar Nueva Entrega</h5>
                <form class="row g-3">
                    <div class="col-md-6">
                        <label for="input1" class="form-label">Fecha y Hora</label>
                        <input type="datetime-local" class="form-control" id="fecha_hora" value="<?= date('Y-m-d H:i:s') ?>" >
                    </div>
                    <div class="col-md-6">
                        <label for="input2" class="form-label">Auxiliar</label>
                        <select id="input7" class="form-select">
                            <option selected>Choose...</option>
                            <option>One</option>
                            <option>Two</option>
                            <option>Three</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="input3" class="form-label">Tipo de Tarea</label>
                        <select id="input7" class="form-select">
                            <option selected>Choose...</option>
                            <option>One</option>
                            <option>Two</option>
                            <option>Three</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="input4" class="form-label">Título de Entrega</label>
                        <input type="email" class="form-control" id="input4" placeholder="Email">
                    </div>
                    <div class="col-md-6">
                        <label for="input5" class="form-label">Cliente</label>
                        <input type="text" class="form-control" id="input5" placeholder="Seleccione el cliente">
                    </div>
                    <div class="col-md-6">
                        <label for="input6" class="form-label">Tiempo Estimado (HH:MM)</label>
                        <input type="time" class="form-control" id="input6">
                    </div>
                    <div class="col-md-3">
                        <label for="input7" class="form-label">Estado</label>
                        <select id="input7" class="form-select">
                            <option>En Curso</option>
                            <option>Pausado</option>
                            <option>Completado</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="input6" class="form-label">Fecha y Hora de Entrega Calculada</label>
                        <input type="datetime-local" class="form-control" id="input6">
                    </div>

                    <div class="col-md-6">
                        <label for="input8" class="form-label">Link de Google Drive</label>
                        <input type="text" class="form-control" id="input8" placeholder="City">
                    </div>
                    <div class="col-md-12">
                        <label for="input9" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="input11" placeholder="Address ..." rows="3"></textarea>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <button type="button" class="btn btn-primary px-4">Guardar</button>
                            <button type="button" class="btn btn-danger px-4">Limpiar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<!--end row-->

<?= $this->endSection() ?>

<?= $this->section('js') ?>

<script src="js/home/home.js"></script>

<?= $this->endSection() ?>