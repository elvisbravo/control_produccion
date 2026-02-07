<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\TipoTareaModel;
use CodeIgniter\RESTful\ResourceController;

class Tareas extends ResourceController
{
    protected $format = 'json';

    public function getTareas()
    {
        try {
            $tarea = new TareaModel();

            $datos = $tarea->query("SELECT tarea.id, tarea.nombre, tarea.horas_estimadas, tipo_tarea.tipo, tipo_tarea.color FROM tarea INNER JOIN tipo_tarea ON tipo_tarea.id = tarea.tipo_tarea WHERE tarea.estado = true")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Tareas obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function create()
    {
        $tarea = new TareaModel();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            $datos_tarea = array(
                "nombre" => $data->nombre,
                "horas_estimadas" => $data->horasEstimadas,
                "tipo_tarea" => $data->categoriaId,
                "estado" => true
            );

            if ($id == 0) {

                $tarea->insert($datos_tarea);
                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Tarea creada correctamente',
                    'result' => null
                ]);
            } else {
                $tarea->update($id, $datos_tarea);
                return $this->respond([
                    'status' => 200,
                    'message' => 'Tarea actualizada correctamente',
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
            $tarea = new TareaModel();

            $datos_tarea = array(
                "estado" => false
            );

            $tarea->update($id, $datos_tarea);

            return $this->respondDeleted([
                'status' => 200,
                'message' => 'Tarea eliminada',
                'id' => $id
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }

    public function createType()
    {
        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            $tipo = new TipoTareaModel();

            $datos_tipo = array(
                "tipo" => $data->nombre,
                "color" => $data->color,
                "estado" => true
            );

            if ($id == 0) {

                $tipo->insert($datos_tipo);
                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Tipo de tarea creado correctamente',
                    'result' => null
                ]);
            } else {
                $tipo->update($id, $datos_tipo);
                return $this->respond([
                    'status' => 200,
                    'message' => 'Tipo de tarea actualizado correctamente',
                    'result' => null
                ]);
            }
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function getTypes()
    {
        try {
            $tipo = new TipoTareaModel();

            $datos = $tipo->where('estado', true)->findAll();

            return $this->respond([
                'status' => 200,
                'message' => 'Tipos de tarea obtenidos correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function deleteType($id = null)
    {
        try {
            $tipo = new TipoTareaModel();

            $datos_tipo = array(
                "estado" => false
            );

            $tipo->update($id, $datos_tipo);

            return $this->respondDeleted([
                'status' => 200,
                'message' => 'Tipo de tarea eliminado'
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }
}
