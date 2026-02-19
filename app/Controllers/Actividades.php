<?php

namespace App\Controllers;

use App\Models\ActividadesModel;
use App\Models\PersonaModel;
use App\Models\ProspectoPersonaModel;
use App\Models\ProspectosModel;
use App\Models\TareaModel;
use CodeIgniter\RESTful\ResourceController;

class Actividades extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        helper('notificacion_helper');
    }

    public function getActividades($id)
    {
        try {
            $actividad = new ActividadesModel();

            $datos = $actividad->query("SELECT actividades.id, actividades.prospecto_id, carreras.nombre AS carrera_nombre, prospectos.fecha_contacto, prospectos.prioridad, tarea.nombre, prospectos.created_at, prospectos.seguimiento, personas.nombres, personas.apellidos
            FROM actividades 
            INNER JOIN prospectos ON actividades.prospecto_id = prospectos.id
            INNER JOIN usuarios ON usuarios.id = prospectos.usuario_venta_id
            INNER JOIN personas ON personas.id = usuarios.persona_id
            left JOIN carreras ON prospectos.carrera_id = carreras.id 
            inner join tarea on tarea.id = prospectos.tarea_id
            WHERE actividades.estado = true AND actividades.usuario_id = $id")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Actividades obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
        }
    }

    public function getActividadById($id)
    {
        try {
            $actividad = new ActividadesModel();
            $prospecto_persona = new ProspectoPersonaModel();
            $persona = new PersonaModel();

            $datos = $actividad->query("SELECT act.id, act.prospecto_id,pro.fecha_contacto, pro.fecha_entrega, pro.contenido, pro.link_drive, pro.seguimiento, pro.prioridad, pro.estado_cliente, o.nombre as origen, t.nombre as tarea, na.nombre as nivel_academico, c.nombre as carrera, i.nombre as institucion, i.abreviatura as sigla, p.nombres, p.apellidos
            FROM actividades act 
            INNER JOIN prospectos pro ON act.prospecto_id = pro.id 
            INNER JOIN tarea t ON pro.tarea_id = t.id 
            LEFT JOIN origen o ON pro.origen_id = o.id 
            LEFT JOIN nivel_academico na ON na.id = pro.nivel_academico_id 
            LEFT JOIN carreras c ON c.id = pro.carrera_id 
            LEFT JOIN institucion i ON i.id = c.institucion_id
            INNER JOIN usuarios u ON u.id = pro.usuario_venta_id
            INNER JOIN personas p ON p.id = u.persona_id
            WHERE act.id = $id")->getRow();

            $id_prospecto = $datos->prospecto_id;

            $consulta_contacto = $prospecto_persona->where('prospecto_id', $id_prospecto)->findAll();

            $datosPersona = [];

            foreach ($consulta_contacto as $key => $value) {
                $datos_persona = $persona->where('id', $value['persona_id'])->first();
                $data = [
                    "nombres" => $datos_persona['nombres'],
                    "apellidos" => $datos_persona['apellidos'],
                    "celular" => $datos_persona['celular'],
                ];

                array_push($datosPersona, $data);
            }

            $datos->contactos = $datosPersona;

            return $this->respond([
                'status' => 200,
                'message' => 'Actividades obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor ');
        }
    }

    public function updateProceso()
    {
        try {
            $actividad = new ActividadesModel();
            $prospecto = new ProspectosModel();
            $tarea = new TareaModel();

            $data = json_decode($this->request->getBody(true));

            $id_actividad = $data->id_actividad;

            $id_prospecto = $data->id_prospecto;
            $usuario = $data->usuario;

            $datos_prospecto = array(
                "seguimiento" => "En proceso",
                "link_drive" => $data->link_drive
            );

            $prospecto->update($id_prospecto, $datos_prospecto);

            $datos_actividad = array(
                "fecha_inicio" => date('Y-m-d H:i:s')
            );

            $actividad->update($id_actividad, $datos_actividad);

            $data_prospecto = $prospecto->find($id_prospecto);

            $data_tarea = $tarea->find($data_prospecto['tarea_id']);

            crear_notificacion($data_prospecto['usuario_venta_id'], $usuario, 'Potencial Cliente - En proceso', $data_tarea['nombre'], 'warning', 1);

            return $this->respond([
                'status' => 200,
                'message' => 'Actividad actualizada correctamente',
                'result' => null
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
