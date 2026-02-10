<?php

namespace App\Controllers;

use App\Models\OrigenModel;
use CodeIgniter\RESTful\ResourceController;

class Origen extends ResourceController
{
    protected $format = 'json';

    public function getOrigenes()
    {
        try {
            $origen = new OrigenModel();

            $datos = $origen->where('estado', true)->findAll();

            return $this->respond([
                'status' => 200,
                'message' => 'Origenes obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function create()
    {
        $origen = new OrigenModel();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            $datos_origen = array(
                "nombre" => $data->nombre,
                "estado" => true
            );

            if ($id == 0) {

                $origen->insert($datos_origen);
                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Carrera creada correctamente',
                    'result' => null
                ]);
            } else {
                $origen->update($id, $datos_origen);
                return $this->respond([
                    'status' => 200,
                    'message' => 'Carrera actualizada correctamente',
                    'result' => null
                ]);
            }
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
            $origen = new OrigenModel();

            $datos_origen = array(
                "estado" => false
            );

            $origen->update($id, $datos_origen);

            return $this->respondDeleted([
                'status' => 200,
                'message' => 'Carrera eliminada',
                'id' => $id
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }
}
