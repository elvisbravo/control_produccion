<?php

if (!function_exists('crear_horario')) {
    function crear_horario($actividad_id, $fecha, $hora_inicio, $hora_fin)
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
            'updated_at' => date('Y-m-d H:i:s')
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

    function asignar_horas_trabajo($usuario_id, $duracion, $actividad_id)
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

        // 🔎 Buscar último horario registrado
        $horario = $db->table('horario_usuario')
            ->select('horario_usuario.*')
            ->join('actividades', 'actividades.id = horario_usuario.actividad_id')
            ->where('actividades.usuario_id', $usuario_id)
            ->orderBy('horario_usuario.fecha', 'DESC')
            ->orderBy('horario_usuario.hora_fin', 'DESC')
            ->get()
            ->getRow();

        $ahora = new DateTime();

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

        // Convertir duración a minutos
        $minutosRestantes = convertir_a_minutos($duracion);

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

                $minutosAsignar = min($minutosRestantes, $minutosDisponibles);

                $horaInicio = $fechaActual->format('H:i:s');

                $fechaActual->modify("+{$minutosAsignar} minutes");

                $horaFin = $fechaActual->format('H:i:s');

                // Guardar bloque
                crear_horario($actividad_id, $fechaStr, $horaInicio, $horaFin);

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

        // Convertir duración requerida a minutos
        $minutosRequeridos = convertir_a_minutos($duracion);

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
