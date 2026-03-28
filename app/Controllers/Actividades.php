<?php

namespace App\Controllers;

use App\Models\ActividadesModel;
use App\Models\ActividadEstadoHistorialModel;
use App\Models\DriveLinksModel;
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
        helper('horario_helper');
    }

    public function getEstadosActividades()
    {
        try {
            $actividad = new ActividadesModel();

            $data = json_decode($this->request->getBody(true));

            $id = $data->id;
            $fecha_inicio = $data->fecha_inicio;
            $fecha_fin = $data->fecha_fin;
            $estado_progreso = $data->estado_progreso;

            $datos = $actividad->query("SELECT a.id, a.prospecto_id, c.nombre AS carrera_nombre, a.fecha_inicio as fecha_contacto, a.prioridad, t.nombre, p.created_at, a.estado_progreso, per.nombres, per.apellidos, i.abreviatura as sigla, aeh.fecha_inicio, aeh.fecha_fin
            FROM actividades a 
            INNER JOIN tarea t ON a.tarea_id = t.id
            INNER JOIN prospectos p ON a.prospecto_id = p.id
            INNER JOIN usuarios u ON u.id = p.usuario_venta_id
            INNER JOIN personas per ON per.id = u.persona_id
            LEFT JOIN carreras c ON c.id = p.carrera_id
            LEFT JOIN institucion i ON i.id = c.institucion_id
            LEFT JOIN actividad_estado_historial aeh ON aeh.id = (
                SELECT MAX(id) 
                FROM actividad_estado_historial 
                WHERE actividad_id = a.id AND estado_progreso = a.estado_progreso
            )
            WHERE a.estado = true AND a.usuario_id = $id AND a.estado_progreso = '$estado_progreso' AND aeh.fecha_inicio >= '$fecha_inicio' AND aeh.fecha_inicio < (DATE '$fecha_fin' + INTERVAL '1 day') ORDER BY a.id ASC")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Actividades obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
        }
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

            $datos = $actividad->query("SELECT act.id, act.prospecto_id,pro.fecha_contacto, pro.fecha_entrega, pro.contenido, pro.link_drive, act.estado_progreso, act.prioridad, pro.estado_cliente, o.nombre as origen, t.nombre as tarea, na.nombre as nivel_academico, c.nombre as carrera, i.nombre as institucion, i.abreviatura as sigla, p.nombres, p.apellidos
            FROM actividades act 
            INNER JOIN tarea t ON act.tarea_id = t.id 
            INNER JOIN prospectos pro ON act.prospecto_id = pro.id 
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
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
        }
    }

    public function updateLinkDrive()
    {
        try {
            $prospecto = new ProspectosModel();
            $drive_links = new DriveLinksModel();

            $data = json_decode($this->request->getBody(true));

            $id_prospecto = $data->id_prospecto;
            $usuario = $data->usuario;

            $datos_prospecto = array(
                "link_drive" => $data->link_drive
            );

            $prospecto->update($id_prospecto, $datos_prospecto);

            $datos_drive_links = array(
                "usuario_id" => $usuario,
                "prospecto_id" => $id_prospecto,
                "link_drive" => $data->link_drive,
                "created_at" => date('Y-m-d H:i:s')
            );

            $drive_links->insert($datos_drive_links);

            return $this->respond([
                'status' => 200,
                'message' => 'Link Drive actualizado correctamente',
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

    public function updateEstado()
    {
        $actividad = new ActividadesModel();
        $historial_actividad_estados = new ActividadEstadoHistorialModel();

        try {

            $data = json_decode($this->request->getBody(true));

            $id_actividad = $data->id_actividad;
            $estado = $data->estado_progreso;

            $datos_actividad = array(
                "estado_progreso" => $estado
            );

            $actividad->update($id_actividad, $datos_actividad);

            $datos_historial_estado_ = $historial_actividad_estados->where('actividad_id', $id_actividad)->orderBy('id', 'DESC')->first();

            $fecha = date('Y-m-d H:i:s');

            $duracion_segundos = strtotime($fecha) - strtotime($datos_historial_estado_['fecha_inicio']);

            $duracion_minutos = $duracion_segundos / 60;

            $datos_historial_estado = array(
                "fecha_fin" => $fecha,
                "duracion_segundos" => $duracion_minutos
            );

            $historial_actividad_estados->update($datos_historial_estado_['id'], $datos_historial_estado);

            $datos_historial = array(
                "actividad_id" => $id_actividad,
                "estado_progreso" => $estado,
                "fecha_inicio" => $fecha,
                "fecha_fin" => null,
                "usuario_id" => $data->usuario_id
            );

            $historial_actividad_estados->insert($datos_historial);

            if ($estado == 'Finalizado') {
                $db = \Config\Database::connect();
                // 1. Consultar la tabla actividad_estado_historial para obtener el estado inmediatamente anterior al que acabamos de insertar
                $horario_inicio = $db->table('actividad_estado_historial')
                    ->where('actividad_id', $id_actividad)
                    ->where('estado_progreso !=', $estado) // Usamos la variable $estado directamente para que coincida exactamente con lo que se acaba de insertar
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getRow();

                if ($horario_inicio) {
                    $datetimeInicio = new \DateTime($horario_inicio->fecha_inicio ?? $horario_inicio->created_at);
                    $datetimeFin = new \DateTime($fecha);
                    $intervalo = $datetimeInicio->diff($datetimeFin);

                    // Calcular minutos totales transcurridos
                    $minutos_ejecutados = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

                    // 2. Insertar en horario_usuario como ejecutado usando el helper
                    crear_horario(
                        $id_actividad,
                        $datetimeInicio->format('Y-m-d'),
                        $datetimeInicio->format('H:i:s'),
                        $datetimeFin->format('H:i:s'),
                        $data->usuario_id,
                        $minutos_ejecutados,
                        'ejecutado'
                    );

                    // Desactivar los bloques 'programado' antiguos para evitar doble contabilización.
                    $db->table('horario_usuario')
                        ->where('actividad_id', $id_actividad)
                        ->where('tipo', 'programado')
                        ->update(['estado' => false]);
                }
            }

            // Recalcular el horario para todas las tareas pendientes del usuario
            // Esto permite que si el usuario empieza una tarea "fuera de orden",
            // el resto del horario se ajuste automáticamente a esa elección.
            reorganizar_horarios_usuario($data->usuario_id);

            return $this->respond([
                'status' => 200,
                'message' => 'Estado actualizado correctamente',
                'result' => null
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }

    public function getUltimoHorario($usuario_id, $id_tarea)
    {
        try {
            // ─── Consultar la tarea y obtener minutos estimados ────────────────
            $tareaModel = new TareaModel();
            $tarea      = $tareaModel->find($id_tarea);

            if (!$tarea) {
                return $this->failNotFound("No se encontró la tarea con id $id_tarea.");
            }

            $duracion_tarea = (int)$tarea['horas_estimadas'];

            // ─── Meses en español para formato legible ─────────────────────────
            $meses = [
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            ];

            // Obtener el último registro de horario del usuario (el más futuro)
            $data = obtener_ultimo_horario_usuario($usuario_id);
            $ahora = new \DateTime();
            $hoy_str = $ahora->format('Y-m-d');
            $hoy_formateado = (int)$ahora->format('d') . ' de ' . $meses[(int)$ahora->format('m')] . ' de ' . $ahora->format('Y');

            // ─── Caso: El trabajador NO tiene ningún horario registrado ────────
            if (!$data) {
                $ultimo_fin = new \DateTime($hoy_str . ' 08:00:00');
            } else {
                $fecha_ultimo = $data->fecha;
                $ultimo_dt = new \DateTime($fecha_ultimo);
                $hoy_dt = new \DateTime($hoy_str);

                // ─── Caso: El último horario es en el FUTURO ────────────────
                if ($ultimo_dt > $hoy_dt) {
                    $ts_ultimoRaw = strtotime($fecha_ultimo);
                    $formateada_futuro = (int)date('d', $ts_ultimoRaw) . ' de ' . $meses[(int)date('m', $ts_ultimoRaw)] . ' de ' . date('Y', $ts_ultimoRaw);
                    
                    return $this->respond([
                        'status'  => 200,
                        'tipo'    => 'dia_futuro',
                        'message' => "Usted no puede realizar hoy la actividad, su próximo horario programado es el $formateada_futuro a las " . date('H:i', strtotime($data->hora_inicio)),
                        'result'  => $data->fecha . ' ' . $data->hora_fin
                    ]);
                }

                // ─── Caso: El último horario fue en el PASADO ────────────────
                if ($ultimo_dt < $hoy_dt) {
                    $ultimo_fin = new \DateTime($hoy_str . ' 08:00:00');
                } else {
                    // El último horario es HOY
                    $ultimo_fin = new \DateTime($data->fecha . ' ' . $data->hora_fin);
                }
            }

            // Aseguramos que el cursor de tiempo sea al menos 'ahora' si estamos calculando para hoy
            if ($ultimo_fin < $ahora) {
                $ultimo_fin = clone $ahora;
            }

            // ─── Calcular minutos disponibles RESTANTES para el día de HOY ─────
            $dia_semana_hoy = (int)date('N'); // 1=Lun ... 7=Dom
            $bloques = [];
            if ($dia_semana_hoy >= 1 && $dia_semana_hoy <= 5) {
                $bloques = [['08:00:00', '13:00:00'], ['15:00:00', '19:00:00']];
            } elseif ($dia_semana_hoy == 6) {
                $bloques = [['08:00:00', '13:00:00']];
            } else {
                // Domingo
                $bloques = [];
            }

            $minutos_disponibles = 0;
            foreach ($bloques as $bloque) {
                $inicio_bloque = new \DateTime($hoy_str . ' ' . $bloque[0]);
                $fin_bloque    = new \DateTime($hoy_str . ' ' . $bloque[1]);
                
                if ($ultimo_fin < $fin_bloque) {
                    $punto_inicio = ($ultimo_fin > $inicio_bloque) ? $ultimo_fin : $inicio_bloque;
                    $diff = $punto_inicio->diff($fin_bloque);
                    $minutos_disponibles += ($diff->h * 60) + $diff->i;
                }
            }

            // ─── Respuesta final basada en la disponibilidad calculada ────────
            if ($minutos_disponibles >= $duracion_tarea) {
                return $this->respond([
                    'status'              => 200,
                    'tipo'                => 'ok',
                    'message'             => 'Sí puede tomar la tarea para hoy.',
                    'minutos_disponibles' => $minutos_disponibles,
                    'result'              => $ultimo_fin->format('Y-m-d H:i:s')
                ]);
            } else {
                // Faltan minutos
                $minutos_faltantes = $duracion_tarea - $minutos_disponibles;
                $horas_faltantes   = floor($minutos_faltantes / 60);
                $mins_faltantes    = $minutos_faltantes % 60;
                $tiempo_legible    = ($horas_faltantes > 0) ? "{$horas_faltantes}h {$mins_faltantes}min" : "{$mins_faltantes} minutos";

                return $this->respond([
                    'status'              => 200,
                    'tipo'                => 'tiempo_insuficiente',
                    'message'             => "No hay más tiempo para terminar esa tarea el día de hoy ($hoy_formateado). Si guarda, se pasará al día siguiente.",
                    'minutos_disponibles' => $minutos_disponibles,
                    'minutos_faltantes'   => $minutos_faltantes,
                    'result'              => $ultimo_fin->format('Y-m-d H:i:s')
                ]);
            }
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor: ' . $e->getMessage());
        }
    }

    public function fueraHorarioLaboral()
    {
        try {
            $data = [];
           
            return $this->respond([
                'status' => 200,
                'message' => 'Estado actualizado correctamente',
                'result' => null
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }
}
