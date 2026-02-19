<?php

namespace App\Controllers;

use App\Models\ActividadesModel;
use CodeIgniter\RESTful\ResourceController;

class Actividades extends ResourceController
{
    protected $format = 'json';

    public function getActividades($id)
    {
        try {
            $actividad = new ActividadesModel();

            $datos = $actividad->query("SELECT actividades.id, carreras.nombre AS carrera_nombre, prospectos.fecha_contacto, prospectos.prioridad, tarea.nombre, prospectos.created_at, prospectos.seguimiento
            FROM actividades 
            INNER JOIN prospectos ON actividades.prospecto_id = prospectos.id 
            left JOIN carreras ON prospectos.carrera_id = carreras.id 
            inner join tarea on tarea.id = prospectos.tarea_id
            WHERE actividades.estado = true AND actividades.usuario_id = $id")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Actividades obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function create()
    {
        $actividad = new ActividadesModel();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            $datos_actividad = array(
                "nombre" => $data->nombre,
                "institucion_id" => $data->institucion_id,
                "estado" => true
            );

            if ($id == 0) {

                $actividad->insert($datos_actividad);
                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Actividad creada correctamente',
                    'result' => null
                ]);
            } else {
                $actividad->update($id, $datos_actividad);
                return $this->respond([
                    'status' => 200,
                    'message' => 'Actividad actualizada correctamente',
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
            $actividad = new ActividadesModel();

            $datos_actividad = array(
                "estado" => false
            );

            $actividad->update($id, $datos_actividad);

            return $this->respondDeleted([
                'status' => 200,
                'message' => 'Actividad eliminada',
                'id' => $id
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }
}
