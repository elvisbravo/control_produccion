<?php

namespace App\Controllers;

use App\Models\AccionesModel;
use App\Models\AccionesModulosModel;
use App\Models\ModulosModel;
use App\Models\PermisosModel;
use App\Models\RolModel;
use CodeIgniter\RESTful\ResourceController;

class Permisos extends ResourceController
{
    protected $format = 'json';

    public function listaRoles()
    {
        try {
            $rol = new RolModel();
            $roles = $rol->where('estado', true)->orderBy('id', 'ASC')->findAll();
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

    public function permisosModulos($idperfil)
    {
        try {
            $moduloModel = new ModulosModel();
            $accionModuloModel = new AccionesModulosModel();
            $permisosModel = new PermisosModel();

            // Obtener permisos asignados al perfil
            $permisosAsignados = [];
            $permisos = $permisosModel->where('rol_id', $idperfil)->findAll();
            foreach ($permisos as $permiso) {
                // Generar una clave única combinando módulo y acción
                $permisosAsignados[$permiso['modulo_id'] . '_' . $permiso['accion_id']] = true;
            }

            // 1. Obtener módulos padres (donde idpadre es 0 o nulo)
            $modulosPadres = $moduloModel->where('estado', true)
                ->groupStart()
                    ->where('idpadre', 0)
                    ->orWhere('idpadre', null)
                ->groupEnd()
                ->orderBy('orden', 'ASC')
                ->findAll();

            foreach ($modulosPadres as $keyPadre => $padre) {
                // 2. Obtener submódulos (hijos) del módulo padre
                $submodulos = $moduloModel->where('estado', true)
                    ->where('idpadre', $padre['id'])
                    ->orderBy('orden', 'ASC')
                    ->findAll();

                foreach ($submodulos as $keySub => $sub) {
                    // 3. Obtener acciones de cada submódulo
                    $accionesSub = $accionModuloModel->select('acciones_modulos.*, acciones.nombre_accion')
                        ->join('acciones', 'acciones.id = acciones_modulos.accion_id')
                        ->where('acciones_modulos.modulo_id', $sub['id'])
                        ->where('acciones.estado', true)
                        ->findAll();
                    
                    foreach ($accionesSub as $keyAccion => $accion) {
                        $accionesSub[$keyAccion]['seleccionado'] = isset($permisosAsignados[$sub['id'] . '_' . $accion['accion_id']]);
                    }

                    $submodulos[$keySub]['acciones'] = $accionesSub;
                }

                $modulosPadres[$keyPadre]['submodulos'] = $submodulos;

                // 4. Obtener acciones directas del módulo padre (si aplica)
                $accionesPadre = $accionModuloModel->select('acciones_modulos.*, acciones.nombre_accion')
                    ->join('acciones', 'acciones.id = acciones_modulos.accion_id')
                    ->where('acciones_modulos.modulo_id', $padre['id'])
                    ->where('acciones.estado', true)
                    ->findAll();

                foreach ($accionesPadre as $keyAccion => $accion) {
                    $accionesPadre[$keyAccion]['seleccionado'] = isset($permisosAsignados[$padre['id'] . '_' . $accion['accion_id']]);
                }

               
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Modulos obtenidos correctamente',
                'result' => $modulosPadres
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Error interno del servidor: ' . $e->getMessage());
        }
    }

    public function createPermiso()
    {
        try {
            $data = json_decode($this->request->getBody(true));

            /*if (!$data || !isset($data->rolId) || !isset($data->permisos)) {
                return $this->failValidationErrors('Faltan datos obligatorios');
            }*/

            $rolId = $data->perfil_id;
            $permisos = $data->permisos;

            $permisosModel = new PermisosModel();

            // 1. Eliminar los permisos anteriores del rol para evitar duplicados al actualizar
            $permisosModel->where('rol_id', $rolId)->delete();

            // 2. Si hay permisos seleccionados (evita fallos si viene vacío), insertarlos
            if (!empty($permisos) && (is_array($permisos) || is_object($permisos))) {
                foreach ($permisos as $menu_id => $acciones) {
                    foreach ($acciones as $permiso_id) {
                        $nuevoPermiso = [
                            'rol_id' => $rolId,
                            'modulo_id' => $menu_id,
                            'accion_id' => $permiso_id
                        ];
                        $permisosModel->insert($nuevoPermiso);
                    }
                }
            }

            return $this->respondCreated([
                'status' => 201,
                'message' => 'Permiso creado correctamente',
                'result' => null
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor');
        }
    }

    public function getPermisosByRol($rolId)
    {
        try {
            $permisosModel = new PermisosModel();
            $moduloModel = new ModulosModel();

            // 1. Obtener los permisos del rol
            $permisos = $permisosModel->where('rol_id', $rolId)->findAll();

            if (empty($permisos)) {
                return $this->respond([
                    'status' => 200,
                    'message' => 'Permisos obtenidos correctamente',
                    'result' => []
                ]);
            }

            // 2. Extraer los IDs únicos de los módulos a los que tiene acceso
            $modulosIds = array_unique(array_column($permisos, 'modulo_id'));

            // 3. Obtener los detalles de esos módulos
            $modulosPermitidos = $moduloModel->whereIn('id', $modulosIds)
                                             ->where('estado', true)
                                             ->orderBy('orden', 'ASC')
                                             ->findAll();

            // 4. Asegurar que tenemos los módulos padres de cualquier submódulo permitido
            $padresIds = [];
            $modulosIdsAgregados = array_column($modulosPermitidos, 'id');

            foreach ($modulosPermitidos as $mod) {
                if ($mod['idpadre'] != 0 && $mod['idpadre'] != null) {
                    // Si el padre de este submódulo aún no está en nuestra lista principal de IDs
                    if (!in_array($mod['idpadre'], $modulosIdsAgregados)) {
                        $padresIds[] = $mod['idpadre'];
                    }
                }
            }

            if (!empty($padresIds)) {
                $padresIds = array_unique($padresIds);
                $padresFaltantes = $moduloModel->whereIn('id', $padresIds)
                                               ->where('estado', true)
                                               ->findAll();
                $modulosPermitidos = array_merge($modulosPermitidos, $padresFaltantes);
                
                // Re-ordenar por orden tras hacer un merge
                usort($modulosPermitidos, function($a, $b) {
                    return $a['orden'] <=> $b['orden'];
                });
            }

            // 5. Estructurar en formato Padre -> Submódulos
            $menuEstructurado = [];
            $submodulosList = [];

            // Separar padres e hijos
            foreach ($modulosPermitidos as $mod) {
                if ($mod['idpadre'] == 0 || $mod['idpadre'] == null) {
                    $mod['submodulos'] = [];
                    $menuEstructurado[$mod['id']] = $mod;
                } else {
                    $submodulosList[] = $mod;
                }
            }

            // Asignar los submódulos a sus respectivos padres
            foreach ($submodulosList as $sub) {
                $padreId = $sub['idpadre'];
                if (isset($menuEstructurado[$padreId])) {
                    $menuEstructurado[$padreId]['submodulos'][] = $sub;
                }
            }

            // Devolver como un arreglo indexado numéricamente para el frontend
            $menuEstructurado = array_values($menuEstructurado);

            return $this->respond([
                'status' => 200,
                'message' => 'Permisos obtenidos correctamente',
                'result' => $menuEstructurado
            ]);
        } catch (\Throwable $th) {
            return $this->failServerError('Error interno del servidor: ' . $th->getMessage());
        }
    }
}
