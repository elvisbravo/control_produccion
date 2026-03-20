<?php

namespace App\Controllers;

use App\Models\ActividadesModel;
use App\Models\ActividadEstadoHistorialModel;
use App\Models\DriveLinksModel;
use App\Models\HistorialEstadoProspectoModel;
use App\Models\institucionModel;
use App\Models\PersonaModel;
use App\Models\ProspectoPersonaModel;
use App\Models\ProspectosModel;
use App\Models\TareaModel;
use App\Models\HorarioUsuarioModel;
use App\Libraries\OpenAIService;
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

            $datos = $prospecto->query("SELECT p.id, TO_CHAR(p.fecha_contacto, 'DD-MM-YYYY') AS fecha_contacto, TO_CHAR(p.fecha_entrega, 'DD-MM-YYYY') AS fecha_entrega, p.contenido, p.estado, a.estado_progreso, o.nombre as origen, na.nombre as nivel_academico, c.nombre as carrera, i.nombre as institucion, i.abreviatura 
            FROM prospectos p 
            LEFT JOIN origen o ON o.id = p.origen_id 
            LEFT JOIN nivel_academico na ON na.id = p.nivel_academico_id 
            LEFT JOIN carreras c ON c.id = p.carrera_id 
            LEFT JOIN institucion i ON i.id = c.institucion_id
            INNER JOIN actividades a ON a.prospecto_id = p.id
            WHERE p.estado = true")->getResultArray();

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

            $data = $prospecto->query("SELECT p.*, u.rol_id as rol, a.tarea_id FROM prospectos p INNER JOIN usuarios u ON u.id = p.responsable_id INNER JOIN actividades a ON a.prospecto_id = p.id WHERE p.id = $id")->getRowArray();

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
        $historial_actividad_estados = new ActividadEstadoHistorialModel();
        $drive_links = new DriveLinksModel();

        $persona->db->transStart();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;

            if ($id == 0) {

                $nombres = $data->nombres;
                $apellidos = $data->apellidos;
                $celular = $data->celular;
                $fecha_inicio_manual = $data->fecha_inicio_manual ?? '';
                $hora_inicio_manual = $data->hora_inicio_manual ?? '';

                $data_tarea = $tarea->find($data->tarea_id);
                $name_tarea = $data_tarea['nombre'];
                $check_personal = $data->check_personal;

                // VALIDACIÓN CON IA (Opcional si viene de confirmar)
                /*if (!isset($data->ignorar_ia) || $data->ignorar_ia === false) {
                    $horarioModel = new HorarioUsuarioModel();
                    $personal_id = $data->personal_id;

                    // Obtener carga de hoy para este usuario
                    $cargaHoy = $horarioModel->query("SELECT u.id as usuario_id, p.nombres, hu.hora_inicio, hu.hora_fin, hu.duracion_minutos
                        FROM usuarios u 
                        INNER JOIN personas p ON u.persona_id = p.id 
                        LEFT JOIN horario_usuario hu ON u.id = hu.usuario_id 
                        WHERE hu.fecha = CURRENT_DATE 
                        AND hu.estado = true
                        AND u.id = " . (int)$personal_id)->getResultArray();

                    $iaService = new OpenAIService();
                    $chequeo = $iaService->analizarPosibilidadTarea($cargaHoy, $data_tarea, $personal_id);

                    if ($chequeo['status'] === 'success' && $chequeo['result']['posible'] === false) {
                        return $this->respond([
                            'status' => 409, // Conflict
                            'tipo' => 'confirmacion_requerida',
                            'message' => $chequeo['result']['mensaje'],
                            'recomendacion' => $chequeo['result']['recomendacion'],
                            'data_ia' => $chequeo['result']
                        ]);
                    }
                }*/

                $prospecto->insert([
                    'fecha_contacto' => date('Y-m-d'),
                    'origen_id' => 1,
                    'usuario_venta_id' => (int)$data->usuarioVentaId,
                    'nivel_academico_id' => $data->nivelAcademicoId == '' ? null : $data->nivelAcademicoId,
                    'carrera_id' => $data->carreraId == '' ? null : $data->carreraId,
                    'estado' => true,
                    'fecha_entrega' => $data->fechaEntrega == '' ? null : $data->fechaEntrega,
                    'contenido' => $data->contenido,
                    'link_drive' => $data->linkDrive,
                    'estado_cliente' => 'Prospecto',
                    'prioridad' => $data->prioridad,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'responsable_id' => $data->responsable_id
                ]);

                $id_prospecto = $prospecto->getInsertID();

                $datos_drive_links = array(
                    "usuario_id" => $data->usuarioVentaId,
                    "prospecto_id" => $id_prospecto,
                    "link_drive" => $data->linkDrive,
                    "created_at" => date('Y-m-d H:i:s')
                );

                $drive_links->insert($datos_drive_links);

                $data_estado = [
                    "prospecto_id" => $id_prospecto,
                    "usuario_id" => $data->usuarioVentaId,
                    "estado" => 'Prospecto',
                    "fecha_inicio" => date('Y-m-d H:i:s'),
                    "fecha_fin" => null,
                    "comentario" => "Prospecto creado"
                ];

                $estados->insert($data_estado);
                $id_historial_estado = $estados->getInsertID();

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
                    "tarea_id" => $data->tarea_id,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                    "color" => extraerColorAleatorio(),
                    "prioridad" => $data->prioridad,
                    "estado_progreso" => "Pendiente",
                    "tiempo_estimado_minutos" => $data_tarea['horas_estimadas']
                ];

                if (!empty($fecha_inicio_manual)) {
                    $data_actividad["fecha_inicio"] = $fecha_inicio_manual;
                }
                if (!empty($hora_inicio_manual)) {
                    $data_actividad["hora_inicio"] = $hora_inicio_manual;
                }

                $actividad->insert($data_actividad);


                $id_actividad = $actividad->getInsertID();

                $data_historial_actividad_estados = [
                    "actividad_id" => $id_actividad,
                    "estado_progreso" => "Pendiente",
                    "fecha_inicio" => date('Y-m-d H:i:s'),
                    "fecha_fin" => null,
                    "duracion_segundos" => 0
                ];

                $historial_actividad_estados->insert($data_historial_actividad_estados);
                $id_historial_actividad_estado = $historial_actividad_estados->getInsertID();

                crear_notificacion($data->personal_id, $data->usuarioVentaId, 'Potencial Cliente', $name_tarea, 'info', 1);

                asignar_horas_trabajo($data->personal_id, $data_tarea['horas_estimadas'], $id_actividad, 'programado', null, $fecha_inicio_manual, $hora_inicio_manual);

                reorganizar_horarios_usuario($data->personal_id);

                // Sincronizar la fecha de inicio del historial con la fecha de inicio de la actividad proyectada
                $datos_actividad_nueva = $actividad->find($id_actividad);
                if ($datos_actividad_nueva && !empty($datos_actividad_nueva['fecha_inicio']) && !empty($datos_actividad_nueva['hora_inicio'])) {
                    $fecha_inicio_real = $datos_actividad_nueva['fecha_inicio'] . ' ' . $datos_actividad_nueva['hora_inicio'];

                    // Actualizar historial del prospecto
                    $estados->update($id_historial_estado, ['fecha_inicio' => $fecha_inicio_real]);

                    // Actualizar historial de la actividad
                    $historial_actividad_estados->update($id_historial_actividad_estado, ['fecha_inicio' => $fecha_inicio_real]);
                }



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
