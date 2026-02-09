<?php

namespace App\Controllers;

use App\Models\FeriadoModel;
use CodeIgniter\RESTful\ResourceController;

class Feriados extends ResourceController
{
    protected $format = 'json';

    public function getFeriados()
    {
        try {
            $feriado = new FeriadoModel();

            $datos = $feriado->query("SELECT * FROM feriados WHERE estado = true ORDER BY fecha ASC")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Feriados obtenidos correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function create()
    {
        $feriado = new FeriadoModel();

        try {
            //aqui la logica
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function update($id = null)
    {
        $data = [];

        return $this->respond([
            'mensaje' => 'Cliente actualizado',
            'id' => $id,
            'data' => $data
        ]);
    }

    public function delete($id = null)
    {
        try {
            //aqui la logica
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }
}
