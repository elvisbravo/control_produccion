<?php

namespace App\Controllers;

use App\Models\institucionModel;
use CodeIgniter\RESTful\ResourceController;

class Institucion extends ResourceController
{
    protected $format = 'json';

    public function getInstituciones()
    {
        try {
            $institucion = new institucionModel();

            $datos = $institucion->query("SELECT * FROM institucion WHERE estado = true")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Instituciones obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function create()
    {
        $institucion = new institucionModel();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            $datos_institucion = array(
                "tipo" => $data->tipo,
                "nombre" => $data->nombre,
                "abreviatura" => $data->abreviatura,
                "sector" => $data->sector,
                "estado" => true
            );

            if ($id == 0) {

                $institucion->insert($datos_institucion);
                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Institucion creada correctamente',
                    'result' => null
                ]);
            } else {
                $institucion->update($id, $datos_institucion);
                return $this->respond([
                    'status' => 200,
                    'message' => 'Institucion actualizada correctamente',
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
            $institucion = new institucionModel();

            $datos_institucion = array(
                "estado" => false
            );

            $institucion->update($id, $datos_institucion);

            return $this->respondDeleted([
                'status' => 200,
                'message' => 'Institucion eliminada',
                'id' => $id
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }
}
