<?php

namespace App\Controllers;

use App\Models\FeriadoModel;

class Feriados extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/signin');
        }

        return view('feriados/index');
    }

    public function guardar()
    {
        try {
            if (!$this->request->is('post')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Método no permitido']);
            }

            if (!session()->get('logged_in')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
            }

            $data = $this->request->getPost();
            $feriadoModel = new FeriadoModel();

            if (empty($data['nombre']) || empty($data['fecha'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Nombre y fecha son obligatorios'
                ]);
            }

            $feriadoId = $data['feriado_id'] ?? 0;

            $feriadoData = [
                'nombre' => $data['nombre'],
                'fecha' => $data['fecha'],
                'tipo' => $data['tipo'] ?? 'Nacional',
                'es_laborable' => $data['es_laborable'] ?? 0
            ];

            if ($feriadoId == 0) {
                $nuevoId = $feriadoModel->insert($feriadoData);

                if ($nuevoId) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Feriado creado exitosamente'
                    ]);
                }
            } else {
                $feriadoModel->update($feriadoId, $feriadoData);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Feriado actualizado exitosamente'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al guardar el feriado'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function showAll()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $feriadoModel = new FeriadoModel();
        $anio = $this->request->getGet('anio') ?? date('Y');

        $feriados = $feriadoModel->obtenerPorAnio($anio);

        return $this->response->setJSON($feriados);
    }

    public function getFeriado($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $feriadoModel = new FeriadoModel();
        $feriado = $feriadoModel->find($id);

        if ($feriado) {
            return $this->response->setJSON(['status' => 'success', 'data' => $feriado]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Feriado no encontrado']);
        }
    }

    public function deleteFeriado($id)
    {
        try {
            if (!session()->get('logged_in')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
            }

            $feriadoModel = new FeriadoModel();
            $feriado = $feriadoModel->find($id);

            if (!$feriado) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Feriado no encontrado']);
            }

            $feriadoModel->update($id, ['estado' => 0]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Feriado eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
