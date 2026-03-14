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
                'status' => 200,
                'message' => 'Modulos obtenidos correctamente',
                'result' => $modulos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
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
                    'nombre_modulo' => $data->nombre,
                    'url' => $data->url,
                    'icono' => $data->icono,
                    'padre' => $data->padre,
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
                'status' => 200,
                'message' => 'Modulo eliminado correctamente',
                'result' => null
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function getModulosPadres()
    {
        try {
            $modulo = new ModulosModel();
            $modulos = $modulo->where('estado', true)->where('idpadre', 0)->orderBy('id', 'ASC')->findAll();
            return $this->respond([
                'status' => 200,
                'message' => 'Modulos obtenidos correctamente',
                'result' => $modulos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }
}
