<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        try {
            $data = json_decode($this->request->getBody(true));

            if (empty($data->username) || empty($data->password)) {
                return $this->failValidationErrors('Faltan datos obligatorios');
            }

            $usuario = new UsuarioModel();

            $usuarios = $usuario->query("SELECT usuarios.id, usuarios.usuario, personas.nombres, personas.apellidos, roles.id AS rol_id, roles.nombre AS rol FROM usuarios INNER JOIN roles ON usuarios.rol_id = roles.id INNER JOIN personas ON usuarios.persona_id = personas.id WHERE usuarios.estado = true and usuarios.usuario = ? AND usuarios.clave = ?", [$data->username, $data->password])->getRow();

            if (empty($usuarios)) {
                return $this->failNotFound('Usuario o contraseña incorrectos');
            }

            return $this->respond([
                'status' => 200,
                'mensaje' => 'Login exitoso',
                'result' => $usuarios
            ]);
        } catch (\Exception $e) {
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
