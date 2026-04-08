<?php

namespace App\Controllers;

use App\Models\ModulosModel;
use CodeIgniter\RESTful\ResourceController;

class Modulos extends ResourceController
{
    protected $format = 'json';

    public function getModulos()
    {
        try {
            $modulo = new ModulosModel();
            $modulos = $modulo->where('estado', true)->orderBy('id', 'ASC')->findAll();
            return $this->respond([
                'status' => 'sucess',
                'message' => 'Modulos obtenidos correctamente',
                'data' => $modulos
            ], 200);
        } catch (\Throwable $th) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function createModulo()
    {
        try {
            $data = json_decode($this->request->getBody(true));

            if (empty($data->nombre)) {
                return $this->failValidationErrors('Faltan datos obligatorios');
            }

            $id = $data->id;

            $modulo = new ModulosModel();

            if ($id == 0) {
                $modulo->insert([
                    'modulo' => $data->nombre,
                    'url' => $data->url,
                    'icono' => $data->icono,
                    'idpadre' => $data->id_padre,
                    'orden' => $data->orden,
                    'estado' => true
                ]);

                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Modulo creado correctamente',
                    'result' => null
                ]);
            } else {
                $modulo->update($id, [
                    'modulo' => $data->nombre,
                    'url' => $data->url,
                    'icono' => $data->icono,
                    'idpadre' => $data->id_padre,
                    'orden' => $data->orden
                ]);

                return $this->respond([
                    'status' => 200,
                    'message' => 'Modulo actualizado correctamente',
                    'result' => null
                ]);
            }
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
        }
    }

    public function deleteModulo($id)
    {
        try {
            $modulo = new ModulosModel();

            $modulo->update($id, [
                'estado' => false
            ]);

            return $this->respond([
                'status' => 'success',
                'message' => 'Modulo eliminado correctamente',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getModulosPadres()
    {
        try {
            $modulo = new ModulosModel();
            $modulos = $modulo->where('estado', true)->where('idpadre', 0)->orderBy('id', 'ASC')->findAll();
            return $this->respond([
                'status' => 'success',
                'message' => 'Modulos obtenidos correctamente',
                'data' => $modulos
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
