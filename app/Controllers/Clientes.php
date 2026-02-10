<?php

namespace App\Controllers;

use App\Models\institucionModel;
use App\Models\PersonaModel;
use App\Models\ProspectoPersonaModel;
use App\Models\ProspectosModel;
use CodeIgniter\RESTful\ResourceController;

class Clientes extends ResourceController
{
    protected $format = 'json';

    public function getInstituciones()
    {
        try {
            $institucion = new institucionModel();

            $datos = $institucion->query("SELECT * FROM institucion WHERE estado = true")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Instituciones obtenidas correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function createProspecto()
    {
        $prospecto = new ProspectosModel();
        $persona = new PersonaModel();
        $prospecto_persona = new ProspectoPersonaModel();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->id;
            if ($id == 0) {

                $nombres = $data->nombres;
                $apellidos = $data->apellidos;
                $celular = $data->celular;

                $prospecto->insert([
                    'fecha_contacto' => date('Y-m-d'),
                    'origen_id' => $data->origenId ?? 0,
                    'usuario_venta_id' => (int)$data->usuarioVentaId ?? 0,
                    'nivel_academico_id' => $data->nivelAcademicoId ?? 0,
                    'carrera_id' => $data->carreraId ?? 0,
                    'estado' => true,
                    'fecha_entrega' => $data->fechaEntrega ?? null,
                ]);

                $id_prospecto = $prospecto->getInsertID();

                for ($i = 0; $i < count($nombres); $i++) {
                    $datos_persona = array(
                        "nombre" => $nombres[$i]->nombre,
                        "apellidos" => $apellidos[$i]->apellidos,
                        'tipoDocumento_id' => 1,
                        "celular" => $celular[$i]->celular
                    );

                    $persona->insert($datos_persona);

                    $id_persona = $persona->getInsertID();

                    $prospecto_persona->insert([
                        'persona_id' => $id_persona,
                        'prospecto_id' => $id_prospecto
                    ]);
                }

                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Prospecto creado correctamente',
                    'result' => null
                ]);
            } else {
                $prospecto->update($id, [
                    'fecha_contacto' => $data->fechaContacto,
                    'origen_id' => $data->origenId,
                    'usuario_venta_id' => $data->usuarioVentaId,
                    'nivel_academico_id' => $data->nivelAcademicoId,
                    'carrera_id' => $data->carreraId,
                    'tarea_id' => $data->tareaId,
                    'fecha_entrega' => $data->fechaEntrega,
                    'usuario_jefe_valoro' => $data->usuarioJefeValoro
                ]);

                return $this->respond([
                    'status' => 200,
                    'message' => 'Prospecto actualizado correctamente',
                    'result' => null
                ]);
            }
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor: ' . $e->getMessage());
        }
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
        try {
            $institucion = new institucionModel();

            $datos_institucion = array(
                "estado" => false
            );

            $institucion->update($id, $datos_institucion);

            return $this->respondDeleted([
                'status' => 200,
                'message' => 'Institucion eliminada',
                'id' => $id
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }
}
