<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\TareaModel;

class Entregas extends BaseController
{
    public function nuevaEntrega()
    {
        return view('entregas/nueva');
    }

    public function controlEntregas()
    {
        $usuario = new UsuarioModel();
        $usuarios = $usuario->where('estado', 1)->where('perfil_id !=', 1)->findAll();

        $tarea = new TareaModel();

        $categorias = $tarea->select('tareas.categoria_tarea_id, MAX(categoria_tarea.nombre_categoria) AS nombre_categoria')->join('categoria_tarea', 'categoria_tarea.id = tareas.categoria_tarea_id')->groupBy('categoria_tarea_id')->findAll();

        foreach ($categorias as $key => $value) {
            $idcategoria = $value['categoria_tarea_id'];
            $tareas = $tarea->where('categoria_tarea_id', $idcategoria)->findAll();
            $categorias[$key]['tareas'] = $tareas;
        }

        return view('entregas/control_entregas', compact('usuarios', 'categorias'));
    }

    public function reporteProduccion()
    {
        return view('entregas/reporte_produccion');
    }
}
