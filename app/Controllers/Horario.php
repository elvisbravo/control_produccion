<?php

namespace App\Controllers;

use App\Models\CarreraModel;
use App\Models\HorarioUsuarioModel;
use CodeIgniter\RESTful\ResourceController;

class Horario extends ResourceController
{
    protected $format = 'json';

    public function getHorarioById($usuario_id)
    {
        try {
            $horario = new HorarioUsuarioModel();

            $datos = $horario->query("SELECT id, titulo as title, concat(fecha,' ', hora_inicio) as start, concat(fecha,' ', hora_fin) as end FROM horario_usuario WHERE usuario_id = $usuario_id AND estado = true")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Carreras obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function create()
    {
        $carrera = new CarreraModel();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            $datos_carrera = array(
                "nombre" => $data->nombre,
                "institucion_id" => $data->institucion_id,
                "estado" => true
            );

            if ($id == 0) {

                $carrera->insert($datos_carrera);
                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Carrera creada correctamente',
                    'result' => null
                ]);
            } else {
                $carrera->update($id, $datos_carrera);
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
            $carrera = new CarreraModel();

            $datos_carrera = array(
                "estado" => false
            );

            $carrera->update($id, $datos_carrera);

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
