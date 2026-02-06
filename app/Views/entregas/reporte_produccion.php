<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>

<link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="fs-5 pe-3">Reporte de Produccion</div>
</div>
<!--end breadcrumb-->
<div class="card">

    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-control" name="" id="">
                    <option value="">Reporte Diario</option>
                    <option value="">Reporte Semanal</option>
                    <option value="">Reporte Individual</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" id="fecha_inicio">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Auxiliar</th>
                        <th>Total</th>
                        <th>Completadas</th>
                        <th>Pendientes</th>
                        <th>A tiempo</th>
                        <th>Antes</th>
                        <th>Después</th>
                    </tr>
                </thead>
                <tbody id="reporte">
                    <!-- Aquí se llenarán los datos dinámicamente -->

                </tbody>

            </table>
        </div>
    </div>
</div>
<!--end row-->

<?= $this->endSection() ?>

<?= $this->section('js') ?>

<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>

<script src="js/entregas/reporteProduccion.js"></script>

<?= $this->endSection() ?>