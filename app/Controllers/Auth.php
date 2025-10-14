<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    public function signin()
    {
        return view('login/signin');
    }

    public function dashboard()
    {
        return view('mobile');
    }

    public function login()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Método no permitido']);
        }

        $usuario = new UsuarioModel();

        $data = $this->request->getPost();

        $correo = $data['correo'];
        $password = $data['password'];

        if (empty($correo) || empty($password)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario y contraseña son obligatorios.']);
        }

        $user = $usuario->select('usuarios.*, perfiles.id as idperfil, perfiles.nombre_perfil')->join('perfiles', 'perfiles.id = usuarios.perfil_id')->where('usuarios.correo', $correo)->where('usuarios.estado', 1)->first();

        if ($user) {
            $session = session();
            $session->set([
                'id' => $user['id'],
                'correo' => $user['correo'],
                'nombre' => $user['nombres'],
                'apellidos' => $user['apellidos'],
                'perfil' => $user['nombre_perfil'],
                'perfil_id' => $user['perfil_id'],
                'logged_in' => true
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Inicio de sesión exitoso.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Credenciales inválidas.']);
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return $this->response->setJSON(['status' => 'success']);
    }
}
