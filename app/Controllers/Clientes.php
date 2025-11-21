<?php

namespace App\Controllers;

use App\Models\ClienteModel;

class Clientes extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/signin');
        }

        return view('clientes/index');
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
            $clienteModel = new ClienteModel();

            // Validar datos
            if (empty($data['nombres']) || empty($data['apellidos'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Nombres y apellidos son obligatorios'
                ]);
            }

            $clienteId = $data['cliente_id'] ?? 0;

            $clienteData = [
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'email' => $data['email'] ?? '',
                'telefono' => $data['telefono'] ?? '',
                'empresa' => $data['empresa'] ?? '',
                'direccion' => $data['direccion'] ?? ''
            ];

            if ($clienteId == 0) {
                // Nuevo cliente
                $nuevoId = $clienteModel->insert($clienteData);

                if ($nuevoId) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Cliente creado exitosamente',
                        'cliente_id' => $nuevoId
                    ]);
                }
            } else {
                // Actualizar cliente
                $clienteModel->update($clienteId, $clienteData);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Cliente actualizado exitosamente'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al guardar el cliente'
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

        $clienteModel = new ClienteModel();
        $clientes = $clienteModel->where('estado', 1)->findAll();

        return $this->response->setJSON($clientes);
    }

    public function getCliente($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find($id);

        if ($cliente) {
            return $this->response->setJSON(['status' => 'success', 'data' => $cliente]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cliente no encontrado']);
        }
    }

    public function deleteCliente($id)
    {
        try {
            if (!session()->get('logged_in')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
            }

            $clienteModel = new ClienteModel();
            $cliente = $clienteModel->find($id);

            if (!$cliente) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Cliente no encontrado']);
            }

            // Soft delete
            $clienteModel->update($id, ['estado' => 0]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Cliente eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function buscar()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $termino = $this->request->getGet('q');

        if (empty($termino)) {
            return $this->response->setJSON([]);
        }

        $clienteModel = new ClienteModel();
        $clientes = $clienteModel->buscarClientes($termino);

        return $this->response->setJSON($clientes);
    }
}
