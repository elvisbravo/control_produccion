<?php

namespace App\Controllers;

use App\Models\PerfilModel;

class Clientes extends BaseController
{
    public function index()
    {
        return view('clientes/index');
    }
}
