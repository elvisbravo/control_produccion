<?php

namespace App\Controllers;

use App\Models\AsignacionDiariaModel;
use App\Models\DiaModel;
use CodeIgniter\RESTful\ResourceController;

class Dia extends ResourceController
{
    protected $format = 'json';

    public function getDias()
    {
        try {
            $dia = new DiaModel();

            $datos = $dia->query("SELECT * FROM dias WHERE estado = true")->getResultArray();

            return $this->respond([
                'status' => 'success',
                'message' => 'Dias obtenidos correctamente',
                'data' => $datos
            ], 200);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function createAsignacion()
    {
        try {
            $data = json_decode($this->request->getBody(true));

            $asignacion = new AsignacionDiariaModel();

            $datos = array(
                "dia_id" => $data->dia_id,
                "usuario_id" => $data->usuario_id
            );

            $asignacion->insert($datos);

            return $this->respond([
                'status' => 'success',
                'message' => 'Asignacion creada correctamente',
                'data' => $datos
            ], 200);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    public function getAsignacionesUsuario($id)
    {
        try {
            $asignacion = new AsignacionDiariaModel();

            $datos = $asignacion->db->table('asignacion_dias as ad')
                ->select('ad.id, ad.dia_id, ad.usuario_id, u.usuario, p.nombres, p.apellidos')
                ->join('usuarios as u', 'u.id = ad.usuario_id')
                ->join('personas as p', 'p.id = u.persona_id')
                ->where('ad.dia_id', $id)
                ->get()
                ->getResultArray();

            return $this->respond([
                'status' => 'success',
                'message' => 'Asignaciones obtenidas correctamente',
                'data' => $datos
            ], 200);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

}
