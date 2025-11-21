<?php

namespace App\Controllers;

use App\Models\ServicioModel;
use App\Models\UsuarioModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Verificar sesión
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/signin');
        }

        $servicioModel = new ServicioModel();

        // Obtener estadísticas
        $data['estadisticas'] = $servicioModel->obtenerEstadisticas();

        // Obtener próximos vencimientos
        $data['proximos_vencimientos'] = $servicioModel->obtenerProximosVencimientos(7, 10);

        // Obtener carga por auxiliar
        $data['carga_auxiliares'] = $servicioModel->obtenerCargaPorAuxiliar();

        // Datos del usuario
        $data['usuario'] = [
            'nombre' => session()->get('nombre'),
            'apellidos' => session()->get('apellidos'),
            'perfil' => session()->get('perfil')
        ];

        return view('dashboard/index', $data);
    }

    /**
     * API: Obtener estadísticas en JSON
     */
    public function getEstadisticas()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $servicioModel = new ServicioModel();
        $estadisticas = $servicioModel->obtenerEstadisticas();

        return $this->response->setJSON(['status' => 'success', 'data' => $estadisticas]);
    }

    /**
     * API: Obtener próximos vencimientos
     */
    public function getProximosVencimientos()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $dias = $this->request->getGet('dias') ?? 7;
        $limite = $this->request->getGet('limite') ?? 10;

        $servicioModel = new ServicioModel();
        $vencimientos = $servicioModel->obtenerProximosVencimientos($dias, $limite);

        return $this->response->setJSON(['status' => 'success', 'data' => $vencimientos]);
    }

    /**
     * API: Obtener carga de trabajo por auxiliar
     */
    public function getCargaAuxiliares()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $servicioModel = new ServicioModel();
        $carga = $servicioModel->obtenerCargaPorAuxiliar();

        return $this->response->setJSON(['status' => 'success', 'data' => $carga]);
    }
}
