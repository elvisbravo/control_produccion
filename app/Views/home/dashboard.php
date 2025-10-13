<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col">
        <div class="card radius-10 bg-primary">
            <div class="card-body d-flex justify-content-center align-items-center" style="height: 150px;">
                <div class="text-center">
                    <h5 class="text-white mb-1" id="current-date"></h5>
                    <h3 class="text-white fw-bold" id="current-time"></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <button class="btn btn-primary px-5">Análisis Diario</button>
        <button class="btn btn-outline-primary px-5">Análisis Semanal</button>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mt-3">
        <div class="card radius-10 border-start border-0 border-4 border-info">
            <div class="card-body">
                <div class="text-center">
                    <h3 class="my-1 text-info">0</h3>
                    <p class="mb-0 font-13">Entregados Hoy</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mt-3">
        <div class="card radius-10 border-start border-0 border-4 border-info">
            <div class="card-body">
                <div class="text-center">
                    <h3 class="my-1 text-info">0</h3>
                    <p class="mb-0 font-13">Pendientes Hoy</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <button class="btn btn-primary px-5">Entregas de Hoy</button>
        <button class="btn btn-outline-primary px-5">Entregas de la Semana</button>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <table class="table mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Fecha y Hora</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Tarea</th>
                    <th scope="col">Auxiliar</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Entregado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay entregas pendientes para hoy</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>

<script src="js/home/home.js"></script>

<?= $this->endSection() ?>