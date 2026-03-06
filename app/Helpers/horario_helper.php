<?php

if (!function_exists('crear_horario')) {
    function crear_horario($actividad_id, $fecha, $hora_inicio, $hora_fin, $usuario_id, $duracion_minutos, $tipo, $orden = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('horario_usuario');

        $data = [
            'actividad_id' => $actividad_id,
            'fecha' => $fecha,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin,
            'estado' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'usuario_id' => $usuario_id,
            'duracion_minutos' => $duracion_minutos,
            'tipo' => $tipo,
            'orden' => $orden
        ];

        return $builder->insert($data);
    }
}

if (!function_exists('convertir_a_minutos')) {
    function convertir_a_minutos($tiempo)
    {
        list($h, $m) = explode(':', $tiempo);
        return ($h * 60) + $m;
    }
}

if (!function_exists('asignar_horas_trabajo')) {

    function asignar_horas_trabajo($usuario_id, $duracion, $actividad_id, $tipo = 'programado', $orden = null, $fecha_inicio_manual = '', $hora_inicio_manual = '')
    {
        $db = \Config\Database::connect();

        // Obtener usuario + fecha nacimiento
        $usuario = $db->table('usuarios')
            ->select('usuarios.id, personas.fecha_nacimiento')
            ->join('personas', 'personas.id = usuarios.persona_id')
            ->where('usuarios.id', $usuario_id)
            ->get()
            ->getRow();

        if (!$usuario) return false;

        $cumple = date('m-d', strtotime($usuario->fecha_nacimiento));

        // 🔎 Buscar último horario registrado (solo los activos)
        $horario = $db->table('horario_usuario')
            ->select('horario_usuario.*')
            ->join('actividades', 'actividades.id = horario_usuario.actividad_id')
            ->where('actividades.usuario_id', $usuario_id)
            ->where('horario_usuario.estado', true)
            ->orderBy('horario_usuario.fecha', 'DESC')
            ->orderBy('horario_usuario.hora_fin', 'DESC')
            ->get()
            ->getRow();

        $ahora = new DateTime();

        if ($fecha_inicio_manual != '' && $hora_inicio_manual != '') {
            $fechaActual = new DateTime($fecha_inicio_manual . ' ' . $hora_inicio_manual);
        } else {
            if ($horario) {

                $ultimaFechaHora = new DateTime($horario->fecha . ' ' . $horario->hora_fin);

                if ($ultimaFechaHora > $ahora) {
                    $fechaActual = clone $ultimaFechaHora;
                } else {
                    $fechaActual = clone $ahora;
                }
            } else {
                $fechaActual = clone $ahora;
            }
        }

        // La duración ya viene en minutos
        $minutosRestantes = (int)$duracion;

        while ($minutosRestantes > 0) {

            $diaSemana = $fechaActual->format('N'); // 1=Lun ... 7=Dom
            $fechaStr = $fechaActual->format('Y-m-d');

            // ❌ Saltar domingo o cumpleaños
            if ($diaSemana == 7 || $fechaActual->format('m-d') == $cumple) {
                $fechaActual->modify('+1 day');
                $fechaActual->setTime(8, 0);
                continue;
            }

            // Definir bloques laborales
            $bloques = [];

            if ($diaSemana >= 1 && $diaSemana <= 5) {
                $bloques = [
                    ['08:00', '13:00'],
                    ['15:00', '19:00']
                ];
            }

            if ($diaSemana == 6) {
                $bloques = [
                    ['08:00', '13:00']
                ];
            }

            foreach ($bloques as $bloque) {

                $inicioBloque = new DateTime("$fechaStr {$bloque[0]}");
                $finBloque    = new DateTime("$fechaStr {$bloque[1]}");

                // Si ya pasó el bloque
                if ($fechaActual >= $finBloque) continue;

                // Si está antes del bloque
                if ($fechaActual < $inicioBloque) {
                    $fechaActual = clone $inicioBloque;
                }

                $minutosDisponibles = ($finBloque->getTimestamp() - $fechaActual->getTimestamp()) / 60;

                if ($minutosDisponibles <= 0) continue;

                $minutosAsignar = floor(min($minutosRestantes, $minutosDisponibles));

                if ($minutosAsignar <= 0) continue;

                $horaInicio = $fechaActual->format('H:i:s');

                $fechaActual->modify("+{$minutosAsignar} minutes");

                $horaFin = $fechaActual->format('H:i:s');

                // Guardar bloque
                crear_horario($actividad_id, $fechaStr, $horaInicio, $horaFin, $usuario_id, $minutosAsignar, $tipo, $orden);

                $minutosRestantes -= $minutosAsignar;

                if ($minutosRestantes <= 0) break;
            }

            if ($minutosRestantes > 0) {
                $fechaActual->modify('+1 day');
                $fechaActual->setTime(8, 0);
            }
        }

        return true;
    }
}

if (!function_exists('verificar_tiempo_actividad')) {
    function verificar_tiempo_actividad($usuario_id, $duracion)
    {
        $db = \Config\Database::connect();

        $horario = $db->table('horario_usuario')
            ->select('horario_usuario.*')
            ->join('actividades', 'actividades.id = horario_usuario.actividad_id')
            ->where('actividades.usuario_id', $usuario_id)
            ->orderBy('horario_usuario.fecha', 'DESC')
            ->orderBy('horario_usuario.hora_fin', 'DESC')
            ->get()
            ->getRow();

        // La duración requerida ya viene en minutos
        $minutosRequeridos = (int)$duracion;

        if (!$horario) {
            return [
                'status' => true,
                'minutos_disponibles' => 660, // Asumiendo bloque completo (ej. 08:00 a 19:00)
                'ultima_hora' => null
            ];
        }

        // Definir última hora registrada y el límite de las 19:00
        $ultimoFin = new DateTime($horario->fecha . ' ' . $horario->hora_fin);
        $limite = new DateTime($horario->fecha . ' 19:00:00');

        // Calcular minutos disponibles hasta el límite
        $intervalo = $ultimoFin->diff($limite);
        $minutosDisponibles = ($intervalo->h * 60) + $intervalo->i;

        // Si el último fin es después del límite (por alguna razón), los minutos son 0
        if ($ultimoFin > $limite) {
            $minutosDisponibles = 0;
        }

        return [
            'status' => ($minutosDisponibles >= $minutosRequeridos),
            'minutos_disponibles' => $minutosDisponibles,
            'ultima_hora' => $horario->hora_fin
        ];
    }
}

if (!function_exists('reorganizar_horarios_usuario')) {
    function reorganizar_horarios_usuario($usuario_id)
    {
        $db = \Config\Database::connect();

        // 1. Obtener todas las actividades pendientes (programadas) del usuario que no han sido terminadas
        // Ordenadas por su fecha y hora de inicio actual (el orden original de planificación)
        $actividadesPendientes = $db->table('horario_usuario')
            ->select('actividad_id, SUM(duracion_minutos) as duracion_total')
            ->where('usuario_id', $usuario_id)
            ->where('tipo', 'programado')
            ->where('estado', true)
            ->groupBy('actividad_id')
            ->orderBy('MIN(fecha)', 'ASC')
            ->orderBy('MIN(hora_inicio)', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($actividadesPendientes)) return true;

        // 2. Eliminar (soft delete) los horarios programados actuales para re-planificarlos
        $db->table('horario_usuario')
            ->where('usuario_id', $usuario_id)
            ->where('tipo', 'programado')
            ->where('estado', true)
            ->update(['estado' => false]);

        // 3. Re-asignar cada actividad una por una usando la lógica de asignar_horas_trabajo
        // Esto automáticamente tomará la nueva "última hora" (que será la de la tarea recién ejecutada)
        foreach ($actividadesPendientes as $act) {
            asignar_horas_trabajo(
                $usuario_id,
                $act['duracion_total'],
                $act['actividad_id'],
                'programado'
            );
        }

        return true;
    }
}
