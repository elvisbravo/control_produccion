<?php

namespace App\Controllers;

use App\Models\ActividadesModel;
use App\Models\HistorialEstadoProspectoModel;
use App\Models\institucionModel;
use App\Models\PersonaModel;
use App\Models\ProspectoPersonaModel;
use App\Models\ProspectosModel;
use App\Models\TareaModel;
use CodeIgniter\RESTful\ResourceController;

class Clientes extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        helper('notificacion_helper');
        helper('horario_helper');
        helper('base_helper');
    }

    public function getProspectos()
    {
        try {
            $prospecto = new ProspectosModel();
            $prospectoPersona = new ProspectoPersonaModel();

            $datos = $prospecto->query("SELECT p.id, TO_CHAR(p.fecha_contacto, 'DD-MM-YYYY') AS fecha_contacto, TO_CHAR(p.fecha_entrega, 'DD-MM-YYYY') AS fecha_entrega, p.contenido, p.estado, p.seguimiento, o.nombre as origen, na.nombre as nivel_academico, c.nombre as carrera, i.nombre as institucion, i.abreviatura FROM prospectos p LEFT JOIN origen o ON o.id = p.origen_id LEFT JOIN nivel_academico na ON na.id = p.nivel_academico_id LEFT JOIN carreras c ON c.id = p.carrera_id LEFT JOIN institucion i ON i.id = c.institucion_id WHERE p.estado = true")->getResultArray();

            foreach ($datos as $key => $value) {
                $id = $value['id'];
                $personas = $prospectoPersona->query("SELECT p.nombres, p.apellidos, p.celular FROM prospecto_persona pp INNER JOIN personas p ON p.id = pp.persona_id WHERE pp.prospecto_id = $id")->getResultArray();

                $datos[$key]['personas'] = $personas;
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Prospectos obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function getProspecto($id)
    {
        try {
            $prospecto = new ProspectosModel();
            $prospecto_persona = new ProspectoPersonaModel();

            $data = $prospecto->find($id);

            $personas = $prospecto_persona->query("SELECT p.nombres, p.apellidos, p.celular FROM prospecto_persona pp INNER JOIN personas p ON p.id = pp.persona_id WHERE pp.prospecto_id = $id")->getResultArray();

            $datos = [
                "prospecto" => $data,
                "contactos" => $personas
            ];

            return $this->respond([
                'status' => 200,
                'message' => 'Prospectos obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function createProspecto()
    {
        $prospecto = new ProspectosModel();
        $persona = new PersonaModel();
        $prospecto_persona = new ProspectoPersonaModel();
        $tarea = new TareaModel();
        $actividad = new ActividadesModel();
        $estados = new HistorialEstadoProspectoModel();

        $persona->db->transStart();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            if ($id == 0) {

                $nombres = $data->nombres;
                $apellidos = $data->apellidos;
                $celular = $data->celular;

                $data_tarea = $tarea->find($data->tarea_id);
                $name_tarea = $data_tarea['nombre'];

                $verificar_tiempo = verificar_tiempo_actividad($data->personal_id, $data_tarea['horas_estimadas']);

                if ($verificar_tiempo['status'] == false) {
                    return $this->respond([
                        'status' => 400,
                        'messages' => 'No se puede crear el prospecto, no hay tiempo disponible',
                        'result' => $verificar_tiempo
                    ]);
                }

                $prospecto->insert([
                    'fecha_contacto' => date('Y-m-d'),
                    'origen_id' => 1,
                    'usuario_venta_id' => (int)$data->usuarioVentaId,
                    'nivel_academico_id' => $data->nivelAcademicoId == '' ? null : $data->nivelAcademicoId,
                    'tarea_id' => $data->tarea_id == '' ? null : $data->tarea_id,
                    'usuario_jefe_valoro' => $data->personal_id,
                    'carrera_id' => $data->carreraId == '' ? null : $data->carreraId,
                    'estado' => true,
                    'fecha_entrega' => $data->fechaEntrega == '' ? null : $data->fechaEntrega,
                    'contenido' => $data->contenido,
                    'link_drive' => $data->linkDrive,
                    'seguimiento' => 'Pendiente',
                    'estado_cliente' => 'Prospecto',
                    'prioridad' => $data->prioridad,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $id_prospecto = $prospecto->getInsertID();

                $data_estado = [
                    "prospecto_id" => $id_prospecto,
                    "usuario_id" => $data->personal_id,
                    "estado" => 'Prospecto',
                    "fecha_inicio" => date('Y-m-d H:i:s'),
                    "fecha_fin" => null,
                    "comentario" => "Prospecto creado"
                ];

                $estados->insert($data_estado);

                for ($i = 0; $i < count($nombres); $i++) {
                    $datos_persona = array(
                        "nombres" => $nombres[$i],
                        "apellidos" => $apellidos[$i],
                        'tipoDocumento_id' => 1,
                        "celular" => $celular[$i]
                    );

                    $persona->insert($datos_persona);

                    $id_persona = $persona->getInsertID();

                    $prospecto_persona->insert([
                        'persona_id' => $id_persona,
                        'prospecto_id' => $id_prospecto
                    ]);
                }

                //insertar actividad
                $data_actividad = [
                    "prospecto_id" => $id_prospecto,
                    "usuario_id" => $data->personal_id,
                    "estado" => true,
                    "tiempo_estimado" => "00:00",
                    "tiempo_real" => "00:00",
                    "fecha_inicio" => null,
                    "fecha_fin" => null,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                    "color" => extraerColorAleatorio()
                ];

                $actividad->insert($data_actividad);

                $id_actividad = $actividad->getInsertID();

                crear_notificacion($data->personal_id, $data->usuarioVentaId, 'Potencial Cliente', $name_tarea, 'info', 1);

                asignar_horas_trabajo($data->personal_id, $data_tarea['horas_estimadas'], $id_actividad);

                $persona->db->transComplete();

                if ($persona->db->transStatus() === false) {
                    throw new \Exception("Error al realizar la operación.");
                }

                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Prospecto creado correctamente',
                    'result' => null
                ]);
            } else {
                $prospecto->update($id, [
                    'fecha_contacto' => $data->fechaContacto,
                    'origen_id' => $data->origenId,
                    'usuario_venta_id' => $data->usuarioVentaId,
                    'nivel_academico_id' => $data->nivelAcademicoId,
                    'carrera_id' => $data->carreraId,
                    'tarea_id' => $data->tareaId,
                    'fecha_entrega' => $data->fechaEntrega,
                    'usuario_jefe_valoro' => $data->usuarioJefeValoro
                ]);

                $persona->db->transComplete();

                if ($persona->db->transStatus() === false) {
                    throw new \Exception("Error al realizar la operación.");
                }

                return $this->respond([
                    'status' => 200,
                    'message' => 'Prospecto actualizado correctamente',
                    'result' => null
                ]);
            }
        } catch (\Exception $e) {
            $persona->db->transRollback();
            return $this->failServerError('Error interno del servidor: ' . $e->getMessage());
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
