<?php

namespace App\Controllers;

use App\Models\AccionesModel;
use App\Models\AccionesModulosModel;
use CodeIgniter\RESTful\ResourceController;

class Acciones extends ResourceController
{
    protected $format = 'json';

    public function getAcciones()
    {
        try {
            $accion = new AccionesModel();
            $acciones = $accion->where('estado', true)->orderBy('id', 'ASC')->findAll();
            return $this->respond([
                'status' => 200,
                'message' => 'Acciones obtenidas correctamente',
                'result' => $acciones
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function create()
    {
        try {
            $data = json_decode($this->request->getBody(true));

            if (empty($data->nombre)) {
                return $this->failValidationErrors('Faltan datos obligatorios');
            }

            $id = $data->id;

            $accion = new AccionesModel();

            if ($id == 0) {
                $accion->insert([
                    'nombre_accion' => $data->nombre,
                    'estado' => true
                ]);

                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Accion creada correctamente',
                    'result' => null
                ]);
            } else {
                $accion->update($id, [
                    'nombre_accion' => $data->nombre
                ]);

                return $this->respond([
                    'status' => 200,
                    'message' => 'Accion actualizada correctamente',
                    'result' => null
                ]);
            }
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
        }
    }

    public function deleteAccion($id)
    {
        try {
            $accion = new AccionesModel();

            $accion->update($id, [
                'estado' => false
            ]);

            return $this->respond([
                'status' => 200,
                'message' => 'Accion eliminada correctamente',
                'result' => null
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function createAccionesModule()
    {
        try {
            $data = json_decode($this->request->getBody(true));

            $accion = new AccionesModulosModel();

            $accion->where('modulo_id', $data->id_modulo)->delete();

            if (is_array($data->acciones)) {
                $insertData = [];
                foreach ($data->acciones as $idAccion) {
                    $insertData[] = [
                        'modulo_id' => $data->id_modulo,
                        'accion_id' => $idAccion,
                    ];
                }
                if (!empty($insertData)) {
                    $accion->insertBatch($insertData);
                }
            } else {
                $accion->insert([
                    'modulo_id' => $data->id_modulo,
                    'accion_id' => $data->acciones,
                ]);
            }

            return $this->respondCreated([
                'status' => 201,
                'message' => 'Accion creada correctamente',
                'result' => null
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
        }
    }

    public function getAccionesModule($idmodulo)
    {
        try {
            $accion = new AccionesModel();
            $accionesModule = new AccionesModulosModel();

            $acciones = $accion->where('estado', true)->orderBy('id', 'ASC')->findAll();

            foreach ($acciones as $key => $value) {
                $accionesModuleSelected = $accionesModule->where('modulo_id', $idmodulo)->where('accion_id', $value['id'])->first();
                if ($accionesModuleSelected) {
                    $acciones[$key]['seleccionado'] = true;
                } else {
                    $acciones[$key]['seleccionado'] = false;
                }
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Acciones obtenidas correctamente',
                'result' => $acciones
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor ' . $e->getMessage());
        }
    }

}
