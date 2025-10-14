<?php

namespace App\Controllers;

use App\Models\CategoriaTareaModel;

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
}
