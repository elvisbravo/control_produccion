<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="fs-5 pe-3">Permisos</div>
</div>
<!--end breadcrumb-->
<div class="row">
    <div class="col-md-4 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Cargos</h6>
                    </div>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-sm btn-outline-success" id="btnAdd">Nuevo Cargo</button>
                    </div>
                </div>
            </div>
            <div class="card-body" id="loadCargos">

            </div>

        </div>
    </div>

    <div class="col-md-8 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0" id="namePerfil"></h6>
                    </div>
                    <div class="ms-auto" id="btnHtmlPermiso">
                        
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="formPermisos">
                    <input type="hidden" name="perfil_id" id="perfil_id">
                    <ul class="list-unstyled mb-2" id="listPermisos">

                    </ul>
                </form>
            </div>

        </div>
    </div>
</div>
<!--end row-->

<div class="modal fade" id="modalCargo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalCargo"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCargo">
                <div class="modal-body">
                    <input type="hidden" id="idCargo" name="idCargo" value="0">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="nameCargo" class="form-label">Nombre del Cargo</label>
                            <input type="text" class="form-control" name="nameCargo" id="nameCargo" placeholder="Nombre del Cargo">
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

<script src="js/permisos/permiso.js"></script>

<?= $this->endSection() ?>