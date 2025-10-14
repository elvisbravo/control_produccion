<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\PermisosModel;
use App\Models\ModulosModel;

class Permisos extends BaseController
{
    public function index()
    {
        return view('permisos/index');
    }

    public function cargosAll()
    {
        $cargos = new PerfilModel();
        $data = $cargos->where('estado', 1)->findAll();

        return $this->response->setJSON($data);
    }

    public function cargosCreate()
    {
        $cargos = new PerfilModel();

        $id = $this->request->getPost('idCargo');
        $nombre = $this->request->getPost('nameCargo');

        try {
            if ($id == 0) {
                $cargos->insert([
                    'nombre_perfil' => $nombre,
                    'estado' => 1
                ]);

                return $this->response->setJSON(['status' => 'ok', 'message' => 'Cargo creado con éxito']);
            } else {
                $cargos->update($id, [
                    'nombre_perfil' => $nombre
                ]);

                return $this->response->setJSON(['status' => 'ok', 'message' => 'Cargo actualizado con éxito']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al procesar la solicitud ' . $e->getMessage()]);
        }
    }

    public function permisosShow($idperfil)
    {
        $permisos = new PermisosModel();
        $modulos = new ModulosModel();
        $perfil = new PerfilModel();

        $perfil = $perfil->find($idperfil);

        $modulos_padres = $modulos->where('padre', 0)->where('estado', 1)->orderBy('orden', 'asc')->findAll();

        foreach ($modulos_padres as $key => $value) {
            $hijos = $modulos->where('padre', $value['id'])->where('estado', 1)->orderBy('orden', 'asc')->findAll();

            foreach ($hijos as $keys => $values) {

                $modulo_id = $values['id'];

                $permiso = $permisos->where('modulo_id', $modulo_id)->where('perfil_id', $idperfil)->first();

                $acciones = $permisos->query("SELECT ma.accion_id, a.nombre_accion FROM acciones_modulos ma INNER JOIN acciones a ON a.id = ma.accion_id WHERE ma.modulo_id = $modulo_id AND ma.accion_id != 1")->getResultArray();

                foreach ($acciones as $keyes => $item) {
                    $peraccion = $permisos->where('modulo_id', $modulo_id)->where('accion_id', $item['accion_id'])->where('perfil_id', $idperfil)->first();

                    if ($peraccion) {
                        $acciones[$keyes]['permiso'] = 1;
                    } else {
                        $acciones[$keyes]['permiso'] = 0;
                    }
                }

                $hijos[$keys]['acciones'] = $acciones;

                if ($permiso) {
                    $hijos[$keys]['permiso'] = 1;
                } else {
                    $hijos[$keys]['permiso'] = 0;
                }
            }

            $modulos_padres[$key]['hijos'] = $hijos;
        }

        return $this->response->setJSON([
            'modulos' => $modulos_padres,
            'perfil' => $perfil['nombre_perfil'],
            'idperfil' => $perfil['id']
        ]);
    }

    public function guardar()
    {
        try {
            /*if (!session()->logged_in) {
                return redirect()->to(base_url());
            }*/

            if (!$this->request->getPost('permisos')) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Seleccione al menos un módulo'
                ]);
            }

            $permisos = new PermisosModel();

            $idperfil = $this->request->getPost('perfil_id');
            $modulos = $this->request->getPost('permisos');

            $consulta = $permisos->where('perfil_id', $idperfil)->findAll();

            if ($consulta) {
                $permisos->where('perfil_id', $idperfil)->delete();
            }

            for ($i = 0; $i < count($modulos); $i++) {
                $datos = array(
                    'perfil_id' => $idperfil,
                    'modulo_id' => $modulos[$i],
                    'accion_id' => 1
                );

                $permisos->save($datos);

                if ($this->request->getPost('permisosAcciones-' . $modulos[$i])) {
                    foreach ($this->request->getPost('permisosAcciones-' . $modulos[$i]) as $key => $value) {
                        $datos = array(
                            'perfil_id' => $idperfil,
                            'modulo_id' => $modulos[$i],
                            'accion_id' => $value
                        );

                        $permisos->save($datos);
                    }
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Permisos actualizados correctamente"
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
