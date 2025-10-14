<?php

namespace App\Controllers;

class Entregas extends BaseController
{
    public function nuevaEntrega()
    {
        return view('entregas/nueva');
    }

    public function controlEntregas()
    {
        return view('entregas/control_entregas');
    }

    public function reporteProduccion()
    {
        return view('entregas/reporte_produccion');
    }
}
