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
use App\Models\ResignacionesModel;
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

            $datos = $prospecto->query("SELECT p.id, TO_CHAR(p.fecha_contacto, 'DD-MM-YYYY') AS fecha_contacto, TO_CHAR(p.fecha_entrega, 'DD-MM-YYYY') AS fecha_entrega, p.contenido, p.estado, a.estado_progreso, a.tipo_horario, a.modalidad_horario, o.nombre as origen, na.nombre as nivel_academico, c.nombre as carrera, i.nombre as institucion, i.abreviatura, per.nombres, per.apellidos, a.usuario_id 
            FROM prospectos p 
            LEFT JOIN origen o ON o.id = p.origen_id 
            LEFT JOIN nivel_academico na ON na.id = p.nivel_academico_id 
            LEFT JOIN carreras c ON c.id = p.carrera_id 
            LEFT JOIN institucion i ON i.id = c.institucion_id
            INNER JOIN actividades a ON a.prospecto_id = p.id
            INNER JOIN usuarios u ON u.id = a.usuario_id
            INNER JOIN personas per ON per.id = u.persona_id
            WHERE p.estado = true ORDER BY p.id DESC")->getResultArray();

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

            $data = $prospecto->query("SELECT p.*, u.rol_id as rol, a.tarea_id, a.tipo_horario, a.modalidad_horario FROM prospectos p INNER JOIN usuarios u ON u.id = p.responsable_id INNER JOIN actividades a ON a.prospecto_id = p.id WHERE p.id = $id")->getRowArray();

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
            $data = json_decode($this->request->getBody());

            $id = $data->id;

            if ($id == 0) {

                $nombres = $data->nombres;
                $apellidos = $data->apellidos;
                $celular = $data->celular;
                $fecha_inicio_manual = $data->fecha_inicio_manual ?? '';
                $hora_inicio_manual = $data->hora_inicio_manual ?? '';
                $usar_ultima_tarea = $data->usar_ultima_tarea ?? 0;

                if ($usar_ultima_tarea == 1) {
                    $fecha_consulta = (!empty($fecha_inicio_manual)) ? $fecha_inicio_manual : date('Y-m-d');
                    
                    $db = \Config\Database::connect();
                    $ultima_tarea = $db->table('horario_usuario')
                        ->where('usuario_id', $data->personal_id)
                        ->where('fecha', $fecha_consulta)
                        ->where('estado', true)
                        ->orderBy('hora_fin', 'DESC')
                        ->get()
                        ->getRow();

                    if ($ultima_tarea) {
                        $fecha_inicio_manual = $ultima_tarea->fecha;
                        $hora_inicio_manual = $ultima_tarea->hora_fin;
                    } else {
                        // Si no hay tareas ese día, y no se especificó fecha, usamos hoy a las 08:00
                        if (empty($fecha_inicio_manual)) {
                            $fecha_inicio_manual = date('Y-m-d');
                        }
                        // Si no hay tareas, podemos dejar la hora de inicio manual como estaba o ponerla por defecto
                        if (empty($hora_inicio_manual)) {
                            $hora_inicio_manual = '08:00:00';
                        }
                    }
                }

                $data_tarea = $tarea->find($data->tarea_id);
                $name_tarea = $data_tarea['nombre'];
                $check_personal = $data->check_personal;

                $tipo_jornada = $data->tipo_jornada;

                // ⌚ Cálculo de fin de canje si aplica (si hay fecha y hora de inicio)
                $hora_fin_canje = null;
                if (!empty($data->fecha_canje) && !empty($data->hora_inicio_canje)) {
                    $duracion_min = (int)$data_tarea['horas_estimadas'];
                    $inicio_c = new \DateTime($data->fecha_canje . ' ' . $data->hora_inicio_canje);
                    $fin_c = clone $inicio_c;
                    $fin_c->modify("+{$duracion_min} minutes");
                    $hora_fin_canje = $fin_c->format('H:i:s');
                }

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
                    'responsable_id' => $data->responsable_id,
                    'titulo_prospecto' => $data->titulo_prospecto
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
                    "tiempo_estimado_minutos" => $data_tarea['horas_estimadas'],
                    "tipo_horario" => $tipo_jornada ?? 'Laboral',
                    "modalidad_horario" => $data->modalidad_horario ?? null,
                    "fecha_canje" => (!empty($data->fecha_canje)) ? $data->fecha_canje : null,
                    "hora_inicio_canje" => (!empty($data->hora_inicio_canje)) ? $data->hora_inicio_canje : null,
                    "hora_fin_canje" => $hora_fin_canje
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

                // 1. Calcular minutos remanentes AL PRINCIPIO
                $minutos_remanentes = (int)($data->minutos_faltantes ?? 0);

                // 2. 🚀 Asignamos horas de trabajo si es jornada Laboral O si se ha especificado un inicio manual
                if(($check_personal == "0" && $tipo_jornada == 'Laboral') || (!empty($fecha_inicio_manual) && !empty($hora_inicio_manual))) {

                    $duracion_estimada = (int)$data_tarea['horas_estimadas'] - $minutos_remanentes;
                    if ($duracion_estimada > 0) {
                        asignar_horas_trabajo($data->personal_id, $duracion_estimada, $id_actividad, 'programado', null, $fecha_inicio_manual, $hora_inicio_manual, 'VENTAS');
                    }

                    reorganizar_horarios_usuario($data->personal_id);

                } elseif($check_personal == "1") {
                    $resignaciones = new \App\Models\ResignacionesModel();

                    $data_re = [
                        'usuario_id' => $data->personal_id,
                        'prospecto_id' => $id_prospecto,
                        'usuario_reasignar_id' => 0,
                        'usuario_id_remitente' => $data->usuarioVentaId,
                        'estado' => 'pendiente',
                        'name_tarea' => $name_tarea,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'fecha_reasignado' => null
                    ];

                    $resignaciones->insert($data_re);
                }

                // 3. ⚡ Manejo de Tiempo Insuficiente (Bono / Canje) - MOVIDO AL FINAL PARA EVITAR QUE REORGANIZAR LO ELIMINE
                if (!empty($data->opcion_faltante) && $minutos_remanentes > 0) {
                    $horasExtrasModel = new \App\Models\HorasExtrasModel();
                    
                    $fecha_canje_faltante = $data->fecha_canje_faltante ?? '';
                    $hora_canje_faltante = $data->hora_canje_faltante ?? '';

                    // Determinar estado de la hora extra
                    $estado_extra = (empty($fecha_canje_faltante)) ? 'pendiente' : 'aprobado';

                    $horasExtrasModel->insert([
                        'actividad_id' => $id_actividad,
                        'usuario_id'   => $data->personal_id,
                        'fecha'        => $fecha_inicio_manual,
                        'minutos'      => $minutos_remanentes,
                        'tipo'         => $data->opcion_faltante, 
                        'estado'       => $estado_extra
                    ]);

                    // Si hay fecha de recuperación futura
                    if (!empty($fecha_canje_faltante) && !empty($hora_canje_faltante)) {
                        
                        $inicio_rf = new \DateTime($fecha_canje_faltante . ' ' . $hora_canje_faltante);
                        $fin_rf = clone $inicio_rf;
                        $fin_rf->modify("+{$minutos_remanentes} minutes");

                        crear_horario(
                            $id_actividad, 
                            $fecha_canje_faltante, 
                            $hora_canje_faltante, 
                            $fin_rf->format('H:i:s'), 
                            $data->personal_id, 
                            $minutos_remanentes, 
                            'CANJE',
                            null,
                            'VENTAS'
                        );
                    } else {
                        // CANJE NORMAL (Hoy después de las 7 PM o del fin de la tarea programada)
                        $fecha_hoy = !empty($fecha_inicio_manual) ? $fecha_inicio_manual : date('Y-m-d');
                        $db = \Config\Database::connect();
                        
                        // Buscamos el fin de la parte 'programada' de esta actividad para empezar el canje justo después
                        $ultima = $db->table('horario_usuario')
                            ->where('usuario_id', $data->personal_id)
                            ->where('fecha', $fecha_hoy)
                            ->where('estado', true)
                            ->orderBy('hora_fin', 'DESC')
                            ->get()
                            ->getRow();
                        
                        $hora_ref = ($ultima && $ultima->hora_fin > '19:00:00') ? $ultima->hora_fin : '19:00:00';
                        $inicio_rem = new \DateTime($fecha_hoy . ' ' . $hora_ref);
                        $fin_rem = clone $inicio_rem;
                        $fin_rem->modify("+{$minutos_remanentes} minutes");

                        crear_horario(
                            $id_actividad, 
                            $fecha_hoy, 
                            $inicio_rem->format('H:i:s'), 
                            $fin_rem->format('H:i:s'), 
                            $data->personal_id, 
                            $minutos_remanentes, 
                            'CANJE',
                            null,
                            'VENTAS'
                        );
                    }
                }

                // 🗓️ Bloqueo de agenda (modalidad fuera de horario)
                if (!empty($data->fecha_canje) && !empty($data->hora_inicio_canje)) {
                    crear_horario($id_actividad, $data->fecha_canje, $data->hora_inicio_canje, $hora_fin_canje, $data->personal_id, (int)$data_tarea['horas_estimadas'], 'canje', null, 'VENTAS');
                }

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
                    'status' => 'success',
                    'message' => 'Prospecto creado correctamente',
                    'data' => null
                ], 201);
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
                    'status' => 'success',
                    'message' => 'Prospecto actualizado correctamente',
                    'data' => null
                ], 200);
            }
        } catch (\Exception $e) {
            $persona->db->transRollback();
            return $this->respond([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
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
