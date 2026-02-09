<?php

namespace App\Controllers;

use App\Models\RolModel;
use CodeIgniter\RESTful\ResourceController;

class Permisos extends ResourceController
{
    protected $format = 'json';

    public function listaRoles()
    {
        try {
            $rol = new RolModel();
            $roles = $rol->where('estado', true)->where('id !=', 1)->orderBy('id', 'ASC')->findAll();
            return $this->respond([
                'status' => 200,
                'message' => 'Roles obtenidos correctamente',
                'result' => $roles
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function createRol()
    {
        try {
            $data = json_decode($this->request->getBody(true));

            if (empty($data->nombre)) {
                return $this->failValidationErrors('Faltan datos obligatorios');
            }

            $id = $data->rolId;

            $rol = new RolModel();

            if ($id == 0) {
                
                $rol->insert([
                    'nombre' => $data->nombre,
                    'estado' => true
                ]);

                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Rol creado correctamente',
                    'result' => null
                ]);
            } else {
                $rol->update($id, [
                    'nombre' => $data->nombre
                ]);

                return $this->respond([
                    'status' => 200,
                    'message' => 'Rol actualizado correctamente',
                    'result' => null
                ]);
            }
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function deleteRol($id)
    {
        try {
            $rol = new RolModel();

            $rol->update($id, [
                'estado' => false
            ]);

            return $this->respond([
                'status' => 200,
                'message' => 'Rol eliminado correctamente',
                'result' => null
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function show($id = null)
    {
        return $this->respond([
            'id' => $id,
            'nombre' => 'Cliente ' . $id
        ]);
    }

    public function create()
    {
        $data = [];

        return $this->respondCreated([
            'mensaje' => 'Cliente creado',
            'data' => $data
        ]);
    }

    public function update($id = null)
    {
        $data = [];

        return $this->respond([
            'mensaje' => 'Cliente actualizado',
            'id' => $id,
            'data' => $data
        ]);
    }

    public function delete($id = null)
    {
        return $this->respondDeleted([
            'mensaje' => 'Cliente eliminado',
            'id' => $id
        ]);
    }
}
