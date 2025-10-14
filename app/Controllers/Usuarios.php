<?php

namespace App\Controllers;

use App\Models\PerfilModel;

class Usuarios extends BaseController
{
    public function index()
    {
        $perfil = new PerfilModel();
        $perfiles = $perfil->where('estado', 1)->findAll();
        return view('usuarios/index', compact('perfiles'));
    }

    public function guardar()
    {
        try {
            if (!$this->request->is('post')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Método no permitido']);
            }

            $data = $this->request->getPost();

            // Validar datos
            if (empty($data['nombre']) || empty($data['apellidos']) || empty($data['correo']) || empty($data['password']) || empty($data['cargo'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
            }

            $usuarioModel = new \App\Models\UsuarioModel();

            if ($data['idUsuario'] == 0) {
                // Verificar si el correo ya existe
                $existingUser = $usuarioModel->where('correo', $data['correo'])->first();
                if ($existingUser) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'El correo ya está en uso.']);
                }

                // Nuevo usuario
                $usuarioModel->insert([
                    'nombres' => $data['nombre'],
                    'apellidos' => $data['apellidos'],
                    'correo' => $data['correo'],
                    'password' => $data['password'],
                    'perfil_id' => $data['cargo'],
                    'estado' => 1 // Activo por defecto
                ]);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario creado exitosamente.']);
            } else {
                // Actualizar usuario existente
                $usuarioModel->update($data['idUsuario'], [
                    'nombres' => $data['nombre'],
                    'apellidos' => $data['apellidos'],
                    'correo' => $data['correo'],
                    'password' => $data['password'],
                    'perfil_id' => $data['cargo']
                ]);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario actualizado exitosamente.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar el usuario: ' . $e->getMessage()]);
        }
    }

    public function showAll()
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        $usuarios = $usuarioModel->select('usuarios.*, perfiles.nombre_perfil')
            ->join('perfiles', 'perfiles.id = usuarios.perfil_id')
            ->where('usuarios.estado', 1)->where('usuarios.perfil_id !=', 1)
            ->findAll();

        return $this->response->setJSON($usuarios);
    }

    public function getUsuario($id)
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        $usuario = $usuarioModel->find($id);

        if ($usuario) {
            return $this->response->setJSON(['status' => 'success', 'data' => $usuario]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado']);
        }
    }

    public function deleteUsuario($id)
    {
        try {
            $usuarioModel = new \App\Models\UsuarioModel();
            $usuario = $usuarioModel->find($id);

            if (!$usuario) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado']);
            }

            // Cambiar estado a 0 (inactivo) en lugar de eliminar
            $usuarioModel->update($id, ['estado' => 0]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario eliminado exitosamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar el usuario: ' . $e->getMessage()]);
        }
    }
}
