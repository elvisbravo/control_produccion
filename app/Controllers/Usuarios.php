<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\UsuarioModel;
use CodeIgniter\RESTful\ResourceController;

class Usuarios extends ResourceController
{
    protected $format = 'json';

    public function getUser($id)
    {
        try {
            $user = new UsuarioModel();

            $datos = $user->query("SELECT usuarios.id, usuarios.usuario, usuarios.clave, usuarios.rol_id, personas.\"tipoDocumento_id\", personas.nombres, personas.apellidos, personas.celular, personas.direccion, personas.fecha_nacimiento, personas.numero_documento, roles.nombre as rol FROM usuarios INNER JOIN personas ON personas.id = usuarios.persona_id INNER JOIN roles ON roles.id = usuarios.rol_id WHERE usuarios.id = $id")->getRow();

            return $this->respond([
                'status' => 200,
                'message' => 'Usuario obtenido correctamente',
                'result' => $datos
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor: ' . $e->getMessage());
        }
    }

    public function getUsers()
    {
        try {
            $user = new UsuarioModel();

            $datos = $user->query("SELECT usuarios.id, usuarios.usuario, personas.nombres, personas.apellidos, personas.celular, personas.direccion, roles.nombre as rol FROM usuarios INNER JOIN personas ON personas.id = usuarios.persona_id INNER JOIN roles ON roles.id = usuarios.rol_id WHERE usuarios.estado = true AND usuarios.id != 1")->getResultArray();

            return $this->respond([
                'status' => 200,
                'message' => 'Usuarios obtenidos correctamente',
                'result' => $datos
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function create()
    {
        $user = new UsuarioModel();
        $persona = new PersonaModel();

        $persona->db->transStart();

        try {
            $data = json_decode($this->request->getBody(true));

            $id = $data->usuarioId;
            $correo = $data->correo;
            $numeroDocumento = $data->numeroDocumento;

            $verificarEmail = $user->where('usuario', $correo)->first();

            if ($verificarEmail) {
                return $this->failValidationErrors('El Correo esta registrado');
            }

            $datos_persona = array(
                "nombres" => $data->nombres,
                "apellidos" => $data->apellidos,
                "tipoDocumento_id" => $data->tipoDocumento,
                "numero_documento" => $numeroDocumento,
                "email" => $correo,
                "celular" => $data->celular,
                "direccion" => $data->direccion,
                "fecha_nacimiento" => $data->fechaNacimiento,
                "estado" => true
            );

            if ($id == 0) {
                $persona->insert($datos_persona);
                $idPersona = $persona->getInsertID();

                $datos_user = array(
                    "usuario" => $correo,
                    "clave" => $data->password,
                    "persona_id" => $idPersona,
                    "rol_id" => $data->rol_id,
                    "estado" => true
                );

                $user->insert($datos_user);

                $persona->db->transComplete();

                if ($persona->db->transStatus() === false) {
                    throw new \Exception("Error al realizar la operación.");
                }

                return $this->respondCreated([
                    'status' => 201,
                    'message' => 'Usuario creado correctamente',
                    'result' => null
                ]);
            } else {
                $persona->update($id, $datos_persona);

                $usuario = $user->where('persona_id', $id)->first();

                $idusuario = $usuario['id'];

                $datos_user = array(
                    "usuario" => $correo,
                    "clave" => $data->password,
                    "rol_id" => $data->rol_id,
                    "estado" => true
                );

                $user->update($idusuario, $datos_user);

                $persona->db->transComplete();

                if ($persona->db->transStatus() === false) {
                    throw new \Exception("Error al realizar la operación.");
                }

                return $this->respond([
                    'status' => 200,
                    'message' => 'Usuario actualizado correctamente',
                    'result' => null
                ]);
            }
        } catch (\Throwable $th) {
            $persona->db->transRollback();

            return $this->failServerError('Error interno del servidor');
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
            $user = new UsuarioModel();

            $datos_user = array(
                "estado" => false
            );

            $user->update($id, $datos_user);

            return $this->respondDeleted([
                'mensaje' => 'Usuario eliminado',
                'id' => $id
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }
}
