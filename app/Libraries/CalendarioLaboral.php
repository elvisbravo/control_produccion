<?php

namespace App\Libraries;

use App\Models\FeriadoModel;
use App\Models\UsuarioModel;
use DateTime;
use DateInterval;

/**
 * Librería para cálculos de calendario laboral
 * Considera horario de trabajo, feriados y cumpleaños de auxiliares
 */
class CalendarioLaboral
{
    // Horario laboral
    const HORAS_LUNES_VIERNES_MANANA = 5;  // 8:00 - 13:00
    const HORAS_LUNES_VIERNES_TARDE = 4;   // 15:00 - 19:00
    const HORAS_LUNES_VIERNES_TOTAL = 9;   // Total diario L-V
    const HORAS_SABADO = 5;                 // 8:00 - 13:00
    const HORAS_DOMINGO = 0;                // No se trabaja

    private $feriadoModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->feriadoModel = new FeriadoModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Calcula la fecha de entrega basada en horas estimadas y calendario laboral
     * 
     * @param DateTime|string $fechaInicio Fecha de inicio del trabajo
     * @param float $horasEstimadas Horas estimadas para completar el trabajo
     * @param int|null $auxiliarId ID del auxiliar asignado
     * @return DateTime Fecha calculada de entrega
     */
    public function calcularFechaEntrega($fechaInicio, $horasEstimadas, $auxiliarId = null)
    {
        // Convertir a DateTime si es string
        if (is_string($fechaInicio)) {
            $fecha = new DateTime($fechaInicio);
        } else {
            $fecha = clone $fechaInicio;
        }

        $horasRestantes = $horasEstimadas;

        // Iterar día por día hasta completar las horas
        while ($horasRestantes > 0) {
            // Verificar si es día laboral
            if ($this->esDiaLaboral($fecha, $auxiliarId)) {
                $horasDisponibles = $this->obtenerHorasLaborablesDia($fecha);

                if ($horasRestantes <= $horasDisponibles) {
                    // El trabajo se completa este día
                    $horaFinal = $this->calcularHoraFinal($fecha, $horasRestantes);
                    $fecha->setTime((int)$horaFinal['hora'], (int)$horaFinal['minuto']);
                    $horasRestantes = 0;
                } else {
                    // Continuar al siguiente día
                    $horasRestantes -= $horasDisponibles;
                    $fecha->add(new DateInterval('P1D'));
                    $fecha->setTime(8, 0); // Reiniciar a las 8 AM
                }
            } else {
                // Día no laboral, pasar al siguiente
                $fecha->add(new DateInterval('P1D'));
                $fecha->setTime(8, 0);
            }
        }

        return $fecha;
    }

    /**
     * Calcula la hora final del día considerando el horario laboral
     * 
     * @param DateTime $fecha Fecha actual
     * @param float $horasRestantes Horas restantes a trabajar
     * @return array ['hora' => int, 'minuto' => int]
     */
    private function calcularHoraFinal($fecha, $horasRestantes)
    {
        $diaSemana = (int)$fecha->format('N'); // 1=Lunes, 7=Domingo

        if ($diaSemana == 6) { // Sábado
            // Solo turno mañana: 8:00 - 13:00
            $horaInicio = 8;
            $minutosRestantes = $horasRestantes * 60;
            $horaFinal = $horaInicio + floor($minutosRestantes / 60);
            $minutoFinal = $minutosRestantes % 60;

            return ['hora' => $horaFinal, 'minuto' => $minutoFinal];
        } else { // Lunes a Viernes
            // Turno mañana: 8:00 - 13:00 (5 horas)
            // Turno tarde: 15:00 - 19:00 (4 horas)

            if ($horasRestantes <= self::HORAS_LUNES_VIERNES_MANANA) {
                // Se completa en la mañana
                $horaInicio = 8;
                $minutosRestantes = $horasRestantes * 60;
                $horaFinal = $horaInicio + floor($minutosRestantes / 60);
                $minutoFinal = $minutosRestantes % 60;
            } else {
                // Se completa en la tarde
                $horasRestantesTarde = $horasRestantes - self::HORAS_LUNES_VIERNES_MANANA;
                $horaInicio = 15;
                $minutosRestantes = $horasRestantesTarde * 60;
                $horaFinal = $horaInicio + floor($minutosRestantes / 60);
                $minutoFinal = $minutosRestantes % 60;
            }

            return ['hora' => $horaFinal, 'minuto' => $minutoFinal];
        }
    }

    /**
     * Obtiene las horas laborables de un día específico
     * 
     * @param DateTime $fecha Fecha a consultar
     * @return float Horas laborables
     */
    public function obtenerHorasLaborablesDia($fecha)
    {
        $diaSemana = (int)$fecha->format('N'); // 1=Lunes, 7=Domingo

        switch ($diaSemana) {
            case 6: // Sábado
                return self::HORAS_SABADO;
            case 7: // Domingo
                return self::HORAS_DOMINGO;
            default: // Lunes a Viernes
                return self::HORAS_LUNES_VIERNES_TOTAL;
        }
    }

    /**
     * Verifica si un día es laboral
     * Considera: día de la semana, feriados y cumpleaños del auxiliar
     * 
     * @param DateTime $fecha Fecha a verificar
     * @param int|null $auxiliarId ID del auxiliar
     * @return bool True si es día laboral
     */
    public function esDiaLaboral($fecha, $auxiliarId = null)
    {
        $diaSemana = (int)$fecha->format('N');

        // Domingo no es laboral
        if ($diaSemana == 7) {
            return false;
        }

        // Verificar si es feriado
        if ($this->esFeriado($fecha)) {
            return false;
        }

        // Verificar si es cumpleaños del auxiliar
        if ($auxiliarId && $this->esCumpleanosAuxiliar($fecha, $auxiliarId)) {
            return false;
        }

        return true;
    }

    /**
     * Verifica si una fecha es feriado
     * 
     * @param DateTime $fecha Fecha a verificar
     * @return bool True si es feriado
     */
    private function esFeriado($fecha)
    {
        $fechaStr = $fecha->format('Y-m-d');
        $feriado = $this->feriadoModel
            ->where('fecha', $fechaStr)
            ->where('estado', 1)
            ->where('es_laborable', 0)
            ->first();

        return $feriado !== null;
    }

    /**
     * Verifica si una fecha es el cumpleaños del auxiliar
     * 
     * @param DateTime $fecha Fecha a verificar
     * @param int $auxiliarId ID del auxiliar
     * @return bool True si es su cumpleaños
     */
    private function esCumpleanosAuxiliar($fecha, $auxiliarId)
    {
        $usuario = $this->usuarioModel->find($auxiliarId);

        if (!$usuario || !$usuario['fecha_nacimiento']) {
            return false;
        }

        $fechaNacimiento = new DateTime($usuario['fecha_nacimiento']);

        // Comparar mes y día (ignorar año)
        return $fecha->format('m-d') === $fechaNacimiento->format('m-d');
    }

    /**
     * Calcula la carga de trabajo de un auxiliar en un rango de fechas
     * 
     * @param int $auxiliarId ID del auxiliar
     * @param DateTime|string $fechaInicio Fecha de inicio del rango
     * @param DateTime|string $fechaFin Fecha de fin del rango
     * @return array ['horas_ocupadas' => float, 'trabajos' => array]
     */
    public function calcularCargaAuxiliar($auxiliarId, $fechaInicio, $fechaFin)
    {
        // Convertir a DateTime si son strings
        if (is_string($fechaInicio)) {
            $fechaInicio = new DateTime($fechaInicio);
        }
        if (is_string($fechaFin)) {
            $fechaFin = new DateTime($fechaFin);
        }

        $db = \Config\Database::connect();

        // Obtener servicios asignados al auxiliar en el rango
        $query = $db->table('servicios')
            ->select('id, titulo, horas_estimadas, fecha_inicio, fecha_entrega_calculada, estado')
            ->where('auxiliar_produccion_id', $auxiliarId)
            ->where('estado !=', 'Completado')
            ->where('estado !=', 'Entregado')
            ->groupStart()
            ->where('fecha_inicio <=', $fechaFin->format('Y-m-d'))
            ->where('fecha_entrega_calculada >=', $fechaInicio->format('Y-m-d'))
            ->groupEnd()
            ->get();

        $trabajos = $query->getResultArray();
        $horasOcupadas = 0;

        foreach ($trabajos as $trabajo) {
            $horasOcupadas += $trabajo['horas_estimadas'];
        }

        return [
            'horas_ocupadas' => $horasOcupadas,
            'trabajos' => $trabajos,
            'cantidad_trabajos' => count($trabajos)
        ];
    }

    /**
     * Obtiene los días no laborables en un rango de fechas para un auxiliar
     * 
     * @param int|null $auxiliarId ID del auxiliar
     * @param DateTime|string $fechaInicio Fecha de inicio
     * @param DateTime|string $fechaFin Fecha de fin
     * @return array Array de fechas no laborables
     */
    public function obtenerDiasNoLaborables($auxiliarId, $fechaInicio, $fechaFin)
    {
        if (is_string($fechaInicio)) {
            $fechaInicio = new DateTime($fechaInicio);
        }
        if (is_string($fechaFin)) {
            $fechaFin = new DateTime($fechaFin);
        }

        $diasNoLaborables = [];
        $fecha = clone $fechaInicio;

        while ($fecha <= $fechaFin) {
            if (!$this->esDiaLaboral($fecha, $auxiliarId)) {
                $diasNoLaborables[] = [
                    'fecha' => $fecha->format('Y-m-d'),
                    'motivo' => $this->obtenerMotivoNoLaboral($fecha, $auxiliarId)
                ];
            }
            $fecha->add(new DateInterval('P1D'));
        }

        return $diasNoLaborables;
    }

    /**
     * Obtiene el motivo por el cual un día no es laboral
     * 
     * @param DateTime $fecha Fecha a verificar
     * @param int|null $auxiliarId ID del auxiliar
     * @return string Motivo
     */
    private function obtenerMotivoNoLaboral($fecha, $auxiliarId)
    {
        $diaSemana = (int)$fecha->format('N');

        if ($diaSemana == 7) {
            return 'Domingo';
        }

        $fechaStr = $fecha->format('Y-m-d');
        $feriado = $this->feriadoModel
            ->where('fecha', $fechaStr)
            ->where('estado', 1)
            ->first();

        if ($feriado) {
            return 'Feriado: ' . $feriado['nombre'];
        }

        if ($auxiliarId && $this->esCumpleanosAuxiliar($fecha, $auxiliarId)) {
            return 'Cumpleaños del auxiliar';
        }

        return 'Día no laboral';
    }

    /**
     * Calcula las horas laborables entre dos fechas
     * 
     * @param DateTime|string $fechaInicio Fecha de inicio
     * @param DateTime|string $fechaFin Fecha de fin
     * @param int|null $auxiliarId ID del auxiliar
     * @return float Total de horas laborables
     */
    public function calcularHorasLaborablesEnRango($fechaInicio, $fechaFin, $auxiliarId = null)
    {
        if (is_string($fechaInicio)) {
            $fechaInicio = new DateTime($fechaInicio);
        }
        if (is_string($fechaFin)) {
            $fechaFin = new DateTime($fechaFin);
        }

        $horasTotales = 0;
        $fecha = clone $fechaInicio;

        while ($fecha <= $fechaFin) {
            if ($this->esDiaLaboral($fecha, $auxiliarId)) {
                $horasTotales += $this->obtenerHorasLaborablesDia($fecha);
            }
            $fecha->add(new DateInterval('P1D'));
        }

        return $horasTotales;
    }
}
