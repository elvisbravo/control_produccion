<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\TareaRolesModel;
use App\Models\TipoTareaModel;
use CodeIgniter\RESTful\ResourceController;

class Tareas extends ResourceController
{
    protected $format = 'json';

    public function getTareas()
    {
        try {
            $tarea = new TareaModel();
            $roles = new TareaRolesModel();

            $datos = $tarea->query("SELECT tarea.id, tarea.nombre, tarea.horas_estimadas, tarea.tipo_tarea, tipo_tarea.tipo, tipo_tarea.color FROM tarea INNER JOIN tipo_tarea ON tipo_tarea.id = tarea.tipo_tarea WHERE tarea.estado = true")->getResultArray();

            foreach ($datos as $key => $value) {
                $roles_tarea = $roles->query("SELECT rol_id, roles.nombre FROM tareas_roles INNER JOIN roles ON roles.id = tareas_roles.rol_id WHERE tareas_roles.tarea_id = " . $value['id'])->getResultArray();
                $datos[$key]['roles'] = $roles_tarea;
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Tareas obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function getTareaRow($id)
    {
        try {
            $tarea = new TareaModel();
            $roles = new TareaRolesModel();

            $tarea_row = $tarea->where('id', $id)->where('estado', true)->first();

            if (!$tarea_row) {
                return $this->failNotFound('Tarea no encontrada');
            }

            $roles_tarea = $roles->query("SELECT rol_id, roles.nombre FROM tareas_roles INNER JOIN roles ON roles.id = tareas_roles.rol_id WHERE tareas_roles.tarea_id = " . $id)->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Tarea obtenida correctamente',
                'result' => [
                    'tarea' => $tarea_row,
                    'roles' => $roles_tarea
                ]
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function getTareasByRol($rol_id)
    {
        try {
            $tarea = new TareaModel();

            $datos = $tarea->query("SELECT tarea.id, tarea.nombre FROM tarea INNER JOIN tareas_roles ON tareas_roles.tarea_id = tarea.id WHERE tareas_roles.rol_id = $rol_id AND tarea.estado = true")->getResultArray();

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
        $tarea_roles = new TareaRolesModel();

        $tarea->db->transStart();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            $datos_tarea = array(
                "nombre" => $data->nombre,
                "horas_estimadas" => $data->horasEstimadas,
                "tipo_tarea" => $data->categoriaId,
                "estado" => true
            );

            $roles = $data->roles;

            if ($id == 0) {

                $tarea->insert($datos_tarea);

                $tarea_id = $tarea->insertID();

                for ($i=0; $i < count($roles); $i++) { 
                    $datos_tarea_roles = array(
                        "tarea_id" => $tarea_id,
                        "rol_id" => $roles[$i]
                    );
                    $tarea_roles->insert($datos_tarea_roles);
                }

                $tarea->db->transComplete();

                if ($tarea->db->transStatus() === false) {
                    throw new \Exception("Error al realizar la operación.");
                }

                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Tarea creada correctamente',
                    'result' => null
                ]);
            } else {
                $tarea->update($id, $datos_tarea);

                $tarea_roles->delete(['tarea_id' => $id]);

                for ($i=0; $i < count($roles); $i++) { 
                    $datos_tarea_roles = array(
                        "tarea_id" => $id,
                        "rol_id" => $roles[$i]
                    );
                    $tarea_roles->insert($datos_tarea_roles);
                }

                $tarea->db->transComplete();

                if ($tarea->db->transStatus() === false) {
                    throw new \Exception("Error al realizar la operación.");
                }

                return $this->respond([
                    'status' => 200,
                    'message' => 'Tarea actualizada correctamente',
                    'result' => null
                ]);
            }
        } catch (\Throwable $th) {
            $tarea->db->transRollback();
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
