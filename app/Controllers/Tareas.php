<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\TareaRolesModel;
use App\Models\TareasUsuarioModel;
use App\Models\TipoTareaModel;
use App\Models\UsuarioModel;
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
                $roles_tarea = $roles->query("SELECT tareas_roles.rol_id, roles.nombre, tareas_roles.prioridad FROM tareas_roles INNER JOIN roles ON roles.id = tareas_roles.rol_id WHERE tareas_roles.tarea_id = " . $value['id'])->getResultArray();
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

            $roles_tarea = $roles->query("SELECT rol_id, roles.nombre, tareas_roles.prioridad FROM tareas_roles INNER JOIN roles ON roles.id = tareas_roles.rol_id WHERE tareas_roles.tarea_id = " . $id)->getResultArray();

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
            $usuario = new UsuarioModel();

            $tareas = $tarea->query("SELECT tarea.id, tarea.nombre, tareas_roles.prioridad FROM tarea INNER JOIN tareas_roles ON tareas_roles.tarea_id = tarea.id WHERE tareas_roles.rol_id = $rol_id AND tarea.estado = true ORDER BY tareas_roles.prioridad DESC")->getResultArray();

            $users = $usuario->query("SELECT usuarios.id, personas.nombres, personas.apellidos FROM usuarios INNER JOIN personas ON personas.id = usuarios.persona_id WHERE usuarios.rol_id = $rol_id AND usuarios.estado = true")->getResultArray();

            $result = [
                "tareas" => $tareas,
                "users" => $users
            ];

            return $this->respond([
                'status' => 200,
                'message' => 'Tareas obtenidas correctamente',
                'result' => $result
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function getTareasByRolUsuario($rol_id, $usuario_id)
    {
        try {
            $tarea = new TareaModel();
            $tarea_usuario = new TareasUsuarioModel();

            $tareas_usuario = $tarea_usuario->where('usuario_id', $usuario_id)->findAll();

            if (count($tareas_usuario) > 0) {
                $tareas = $tarea->query("SELECT tarea.id, tarea.nombre, tareas_usuarios.activo, '1' as prioridad FROM tarea INNER JOIN tareas_usuarios ON tareas_usuarios.tarea_id = tarea.id WHERE tareas_usuarios.usuario_id = $usuario_id AND tarea.estado = true ORDER BY tareas_usuarios.activo DESC")->getResultArray();
            } else {
                $tareas = $tarea->query("SELECT tarea.id, tarea.nombre, tareas_roles.prioridad, 't' as activo FROM tarea INNER JOIN tareas_roles ON tareas_roles.tarea_id = tarea.id WHERE tareas_roles.rol_id = $rol_id AND tarea.estado = true ORDER BY tareas_roles.prioridad DESC")->getResultArray();
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Tareas obtenidas correctamente',
                'result' => $tareas
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
            $roles = json_decode($data->roles, true);

            $datos_tarea = array(
                "nombre" => $data->nombre,
                "horas_estimadas" => $data->horasEstimadas,
                "tipo_tarea" => $data->categoriaId,
                "estado" => true
            );

            if ($id == 0) {

                $tarea->insert($datos_tarea);

                $tarea_id = $tarea->insertID();

                for ($i = 0; $i < count($roles); $i++) {
                    $datos_tarea_roles = array(
                        "tarea_id" => $tarea_id,
                        "rol_id" => $roles[$i]['id'],
                        'prioridad' => $roles[$i]['prioridad'],
                        'estado' => true
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

                $tarea_roles
                    ->where('tarea_id', $id)
                    ->delete();

                for ($i = 0; $i < count($roles); $i++) {
                    $datos_tarea_roles = array(
                        "tarea_id" => $id,
                        "rol_id" => $roles[$i]['id'],
                        "prioridad" => $roles[$i]['prioridad']
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
        } catch (\Exception $e) {
            $tarea->db->transRollback();
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
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

    public function getTareasByRolAll($rol_id)
    {
        try {
            $tarea = new TareaModel();

            $tareas = $tarea->query("SELECT tarea.id, tarea.nombre, tareas_roles.prioridad FROM tarea INNER JOIN tareas_roles ON tareas_roles.tarea_id = tarea.id WHERE tareas_roles.rol_id = $rol_id AND tarea.estado = true ORDER BY tareas_roles.prioridad DESC")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Tareas obtenidas correctamente',
                'result' => $tareas
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function saveConfigTareasUsuario()
    {
        try {
            $tarea_usuario = new TareasUsuarioModel();

            $data = json_decode($this->request->getBody(true));

            $tarea_usuario->db->transStart();

            $tarea_usuario->where('usuario_id', $data->id_usuario)->delete();

            $tareas = $data->tareas;


            for ($i = 0; $i < count($tareas); $i++) {

                if($tareas[$i]->estado == 'activo'){
                    $estado = true;
                }else{
                    $estado = false;
                }

                $datos_tarea_usuario = array(
                    "tarea_id" => $tareas[$i]->id,
                    "usuario_id" => $data->id_usuario,
                    "activo" => $estado,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s')
                );
                $tarea_usuario->insert($datos_tarea_usuario);
            }

            $tarea_usuario->db->transComplete();

            if ($tarea_usuario->db->transStatus() === false) {
                throw new \Exception("Error al realizar la operación.");
            }

            return $this->respondCreated([
                'status' => 201,
                'message' => 'Configuración de tareas guardada correctamente',
                'result' => null
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
        }
    }
}
