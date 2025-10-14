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

            // Verificar si el correo ya existe
            $existingUser = $usuarioModel->where('correo', $data['correo'])->first();
            if ($existingUser) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'El correo ya está en uso.']);
            }

            if ($data['idUsuario'] == 0) {
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
            ->where('usuarios.estado', 1)
            ->findAll();

        return $this->response->setJSON($usuarios);
    }
}
