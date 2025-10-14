<?php

namespace App\Controllers;

use App\Models\CategoriaTareaModel;
use App\Models\TareaModel;

class Tareas extends BaseController
{
    public function index()
    {
        $categoria = new CategoriaTareaModel();
        $categorias = $categoria->where('estado', 1)->findAll();

        return view('tareas/index', compact('categorias'));
    }

    public function categoriasTareas()
    {
        return view('tareas/categoria');
    }

    public function categoriasTareasAll()
    {
        $categoria = new CategoriaTareaModel();
        $data = $categoria->where('estado', 1)->findAll();

        return $this->response->setJSON($data);
    }

    public function guardar()
    {
        try {
            $tarea = new TareaModel();
            $idTarea = $this->request->getPost('idTarea');
            $data = [
                'categoria_tarea_id' => $this->request->getPost('categoria'),
                'nombre_tarea' => $this->request->getPost('name_tarea'),
                'horas_estimadas' => $this->request->getPost('horas_estimadas'),
                'estado' => 1
            ];
            if ($idTarea != 0) {
                // Actualizar
                $tarea->update($idTarea, $data);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Tarea actualizada correctamente']);
            } else {
                // Crear
                $tarea->insert($data);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Tarea creada correctamente']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function tareasAll()
    {
        $tarea = new TareaModel();

        $categorias = $tarea->select('tareas.categoria_tarea_id, categoria_tarea.nombre_categoria')->join('categoria_tarea', 'categoria_tarea.id = tareas.categoria_tarea_id')->groupBy('categoria_tarea_id')->groupBy('categoria_tarea.nombre_categoria')->findAll();

        foreach ($categorias as $key => $value) {
            $idcategoria = $value['categoria_tarea_id'];
            $tareas = $tarea->where('categoria_tarea_id', $idcategoria)->findAll();
            $categorias[$key]['tareas'] = $tareas;

            $total_horas = $tarea->query("SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(horas_estimadas))), '%H:%i') as total_horas FROM tareas WHERE horas_estimadas IS NOT NULL AND categoria_tarea_id = $idcategoria;")->getRow();
            $categorias[$key]['total_horas'] = $total_horas->total_horas;
        }

        return $this->response->setJSON($categorias);
    }
}
