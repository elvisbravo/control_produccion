<?php

namespace App\Controllers;

use App\Models\CategoriaTareaModel;

class Tareas extends BaseController
{
    public function index()
    {
        return view('tareas/index');
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
}
