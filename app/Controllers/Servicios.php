<?php

namespace App\Controllers;

use App\Models\ServicioModel;
use App\Models\ClienteModel;
use App\Models\TipoServicioModel;
use App\Models\UsuarioModel;
use App\Models\HistorialServicioModel;
use App\Libraries\CalendarioLaboral;
use DateTime;

class Servicios extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/signin');
        }

        $usuarioModel = new UsuarioModel();

        $data['jefes'] = $usuarioModel->obtenerJefes();
        $data['auxiliares'] = $usuarioModel->obtenerAuxiliares();

        return view('servicios/index', $data);
    }

    public function nuevo()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/signin');
        }

        $clienteModel = new ClienteModel();
        $tipoServicioModel = new TipoServicioModel();
        $usuarioModel = new UsuarioModel();

        $data['clientes'] = $clienteModel->obtenerActivos();
        $data['tipos_servicio'] = $tipoServicioModel->obtenerActivos();
        $data['jefes'] = $usuarioModel->obtenerJefes();
        $data['auxiliares'] = $usuarioModel->obtenerAuxiliares();
        $data['captadores'] = $usuarioModel->obtenerCaptadores();

        return view('servicios/nuevo', $data);
    }

    public function guardar()
    {
        try {
            if (!$this->request->is('post')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Método no permitido']);
            }

            if (!session()->get('logged_in')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
            }

            $data = $this->request->getPost();
            $servicioModel = new ServicioModel();
            $calendarioLaboral = new CalendarioLaboral();

            // Validar datos obligatorios
            if (
                empty($data['cliente_id']) || empty($data['tipo_servicio_id']) ||
                empty($data['titulo']) || empty($data['horas_estimadas'])
            ) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Todos los campos obligatorios deben ser completados'
                ]);
            }

            $servicioId = $data['servicio_id'] ?? 0;

            // Preparar datos del servicio
            $servicioData = [
                'cliente_id' => $data['cliente_id'],
                'captador_id' => $data['captador_id'] ?? session()->get('id'),
                'tipo_servicio_id' => $data['tipo_servicio_id'],
                'titulo' => $data['titulo'],
                'descripcion' => $data['descripcion'] ?? '',
                'horas_estimadas' => $data['horas_estimadas'],
                'fecha_inicio' => $data['fecha_inicio'] ?? date('Y-m-d'),
                'fecha_limite' => $data['fecha_limite'] ?? null,
                'jefe_produccion_id' => $data['jefe_produccion_id'] ?? null,
                'auxiliar_produccion_id' => $data['auxiliar_produccion_id'] ?? null,
                'prioridad' => $data['prioridad'] ?? 'Media',
                'observaciones' => $data['observaciones'] ?? ''
            ];

            // Calcular fecha de entrega si hay auxiliar asignado
            if (!empty($servicioData['auxiliar_produccion_id'])) {
                $fechaInicio = new DateTime($servicioData['fecha_inicio']);
                $fechaCalculada = $calendarioLaboral->calcularFechaEntrega(
                    $fechaInicio,
                    $servicioData['horas_estimadas'],
                    $servicioData['auxiliar_produccion_id']
                );
                $servicioData['fecha_entrega_calculada'] = $fechaCalculada->format('Y-m-d H:i:s');
            }

            if ($servicioId == 0) {
                // Nuevo servicio
                $servicioData['codigo'] = $servicioModel->generarCodigo();
                $servicioData['estado'] = 'Pendiente';

                $nuevoId = $servicioModel->insert($servicioData);

                if ($nuevoId) {
                    // Registrar en historial
                    $historialModel = new HistorialServicioModel();
                    $historialModel->registrarCambio(
                        $nuevoId,
                        session()->get('id'),
                        null,
                        'Pendiente',
                        'Servicio creado'
                    );

                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Servicio creado exitosamente',
                        'servicio_id' => $nuevoId,
                        'codigo' => $servicioData['codigo']
                    ]);
                }
            } else {
                // Actualizar servicio existente
                $servicioActual = $servicioModel->find($servicioId);

                if (!$servicioActual) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Servicio no encontrado'
                    ]);
                }

                // Verificar si cambió el auxiliar para recalcular fecha
                if ($servicioActual['auxiliar_produccion_id'] != $servicioData['auxiliar_produccion_id']) {
                    if (!empty($servicioData['auxiliar_produccion_id'])) {
                        $fechaInicio = new DateTime($servicioData['fecha_inicio']);
                        $fechaCalculada = $calendarioLaboral->calcularFechaEntrega(
                            $fechaInicio,
                            $servicioData['horas_estimadas'],
                            $servicioData['auxiliar_produccion_id']
                        );
                        $servicioData['fecha_entrega_calculada'] = $fechaCalculada->format('Y-m-d H:i:s');
                    }
                }

                $servicioModel->update($servicioId, $servicioData);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Servicio actualizado exitosamente'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al guardar el servicio'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function showAll()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $servicioModel = new ServicioModel();

        // Obtener filtros
        $filtros = [
            'estado' => $this->request->getGet('estado'),
            'auxiliar_id' => $this->request->getGet('auxiliar_id'),
            'jefe_id' => $this->request->getGet('jefe_id'),
            'prioridad' => $this->request->getGet('prioridad'),
            'alerta' => $this->request->getGet('alerta')
        ];

        $servicios = $servicioModel->obtenerServiciosCompletos($filtros);

        return $this->response->setJSON($servicios);
    }

    public function getServicio($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $servicioModel = new ServicioModel();
        $servicio = $servicioModel->find($id);

        if ($servicio) {
            return $this->response->setJSON(['status' => 'success', 'data' => $servicio]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Servicio no encontrado']);
        }
    }

    public function cambiarEstado()
    {
        try {
            if (!$this->request->is('post')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Método no permitido']);
            }

            if (!session()->get('logged_in')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
            }

            $data = $this->request->getPost();
            $servicioId = $data['servicio_id'];
            $nuevoEstado = $data['estado'];
            $comentario = $data['comentario'] ?? '';

            $servicioModel = new ServicioModel();
            $servicio = $servicioModel->find($servicioId);

            if (!$servicio) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Servicio no encontrado']);
            }

            $estadoAnterior = $servicio['estado'];

            // Actualizar estado
            $updateData = ['estado' => $nuevoEstado];

            // Si se marca como entregado, registrar fecha real
            if ($nuevoEstado == 'Entregado') {
                $updateData['fecha_entrega_real'] = date('Y-m-d H:i:s');
            }

            $servicioModel->update($servicioId, $updateData);

            // Registrar en historial
            $historialModel = new HistorialServicioModel();
            $historialModel->registrarCambio(
                $servicioId,
                session()->get('id'),
                $estadoAnterior,
                $nuevoEstado,
                $comentario
            );

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Estado actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function calendario($auxiliarId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/signin');
        }

        $usuarioModel = new UsuarioModel();
        $data['auxiliares'] = $usuarioModel->obtenerAuxiliares();
        $data['auxiliar_seleccionado'] = $auxiliarId;

        return view('servicios/calendario', $data);
    }

    public function getEventosCalendario($auxiliarId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $servicioModel = new ServicioModel();
        $servicios = $servicioModel->obtenerPorAuxiliar($auxiliarId, false);

        $eventos = [];
        foreach ($servicios as $servicio) {
            $color = $this->getColorPorEstado($servicio['estado']);

            $eventos[] = [
                'id' => $servicio['id'],
                'title' => $servicio['titulo'],
                'start' => $servicio['fecha_inicio'],
                'end' => $servicio['fecha_entrega_calculada'] ?? $servicio['fecha_limite'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'codigo' => $servicio['codigo'],
                    'cliente' => $servicio['cliente_nombres'] . ' ' . $servicio['cliente_apellidos'],
                    'estado' => $servicio['estado'],
                    'horas' => $servicio['horas_estimadas']
                ]
            ];
        }

        return $this->response->setJSON($eventos);
    }

    private function getColorPorEstado($estado)
    {
        $colores = [
            'Pendiente' => '#6c757d',
            'En Proceso' => '#007bff',
            'En Revisión' => '#ffc107',
            'Completado' => '#28a745',
            'Entregado' => '#17a2b8'
        ];

        return $colores[$estado] ?? '#6c757d';
    }

    public function calcularFechaEntrega()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Método no permitido']);
        }

        $data = $this->request->getPost();
        $fechaInicio = $data['fecha_inicio'];
        $horasEstimadas = $data['horas_estimadas'];
        $auxiliarId = $data['auxiliar_id'] ?? null;

        $calendarioLaboral = new CalendarioLaboral();
        $fechaCalculada = $calendarioLaboral->calcularFechaEntrega(
            new DateTime($fechaInicio),
            $horasEstimadas,
            $auxiliarId
        );

        return $this->response->setJSON([
            'status' => 'success',
            'fecha_entrega' => $fechaCalculada->format('Y-m-d H:i:s'),
            'fecha_entrega_formato' => $fechaCalculada->format('d/m/Y H:i')
        ]);
    }
}
