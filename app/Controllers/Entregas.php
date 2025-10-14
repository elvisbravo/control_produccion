<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Entregas extends BaseController
{
    public function nuevaEntrega()
    {
        return view('entregas/nueva');
    }

    public function controlEntregas()
    {
        $usuario = new UsuarioModel();
        $usuarios = $usuario->where('estado', 1)->findAll();

        return view('entregas/control_entregas', compact('usuarios'));
    }

    public function reporteProduccion()
    {
        return view('entregas/reporte_produccion');
    }
}
