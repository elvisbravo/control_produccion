<?php

namespace App\Controllers;

use App\Models\HorarioUsuarioModel;
use App\Libraries\OpenAIService;
use App\Models\TareaModel;

class IAController extends BaseController
{
    public function disponibilidad()
    {
        $model = new HorarioUsuarioModel();

        // consultar trabajadores de la BD
        $trabajadores = $model->query("SELECT u.id as usuario_id, p.nombres, p.apellidos, hu.hora_inicio, hu.hora_fin, hu.fecha
        FROM usuarios u 
        INNER JOIN personas p ON u.persona_id = p.id 
        LEFT JOIN horario_usuario hu ON u.id = hu.usuario_id 
        WHERE u.rol_id != 1 AND u.rol_id != 2 
        AND (hu.fecha = CURRENT_DATE OR hu.fecha IS NULL)
        AND (hu.estado = true OR hu.estado IS NULL)")->getResultArray();

        // llamar a la IA
        $ia = new OpenAIService();

        $respuesta = $ia->analizarTrabajadores($trabajadores);
        
        if ($respuesta['status'] === 'error') {
            return $this->response->setStatusCode(500)->setJSON($respuesta);
        }

        return $this->response->setJSON($respuesta);
    }

    public function verificarTarea()
    {
        $data = json_decode($this->request->getBody(true));
        
        if (!$data || !isset($data->tarea_id)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Falta el ID de la tarea']);
        }

        $tareaModel = new TareaModel();
        $tareaData = $tareaModel->find($data->tarea_id);
        
        if (!$tareaData) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Tarea no encontrada']);
        }

        $model = new HorarioUsuarioModel();
        $personal_id = $data->personal_id ?? null;

        // Query para obtener la carga de hoy del personal específico o de todos
        $sql = "SELECT u.id as usuario_id, p.nombres, p.apellidos, hu.hora_inicio, hu.hora_fin, hu.duracion_minutos
                FROM usuarios u 
                INNER JOIN personas p ON u.persona_id = p.id 
                LEFT JOIN horario_usuario hu ON u.id = hu.usuario_id 
                WHERE u.rol_id != 1 AND u.rol_id != 2 
                AND hu.fecha = CURRENT_DATE 
                AND hu.estado = true";
        
        if ($personal_id) {
            $sql .= " AND u.id = " . (int)$personal_id;
        }

        $cargaHoy = $model->query($sql)->getResultArray();

        $ia = new OpenAIService();
        $analisis = $ia->analizarPosibilidadTarea($cargaHoy, $tareaData, $personal_id);

        return $this->response->setJSON($analisis);
    }
}
