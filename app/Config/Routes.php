<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Auth routes
$routes->get('/', 'Auth::signin');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

// Dashboard
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/estadisticas', 'Dashboard::getEstadisticas');
$routes->get('/dashboard/vencimientos', 'Dashboard::getProximosVencimientos');
$routes->get('/dashboard/carga-auxiliares', 'Dashboard::getCargaAuxiliares');

// Servicios
$routes->get('/servicios', 'Servicios::index');
$routes->get('/servicios/nuevo', 'Servicios::nuevo');
$routes->post('/servicios/guardar', 'Servicios::guardar');
$routes->get('/servicios/all', 'Servicios::showAll');
$routes->get('/servicios/(:num)', 'Servicios::getServicio/$1');
$routes->post('/servicios/cambiar-estado', 'Servicios::cambiarEstado');
$routes->post('/servicios/calcular-fecha', 'Servicios::calcularFechaEntrega');

// Calendario
$routes->get('/calendario', 'Servicios::calendario');
$routes->get('/calendario/(:num)', 'Servicios::calendario/$1');
$routes->get('/calendario/eventos/(:num)', 'Servicios::getEventosCalendario/$1');

// Clientes
$routes->get('/clientes', 'Clientes::index');
$routes->post('/clientes/guardar', 'Clientes::guardar');
$routes->get('/clientes/all', 'Clientes::showAll');
$routes->get('/clientes/buscar', 'Clientes::buscar');
$routes->get('/clientes/(:num)', 'Clientes::getCliente/$1');
$routes->get('/clientes/eliminar/(:num)', 'Clientes::deleteCliente/$1');

// Feriados
$routes->get('/feriados', 'Feriados::index');
$routes->post('/feriados/guardar', 'Feriados::guardar');
$routes->get('/feriados/all', 'Feriados::showAll');
$routes->get('/feriados/(:num)', 'Feriados::getFeriado/$1');
$routes->get('/feriados/eliminar/(:num)', 'Feriados::deleteFeriado/$1');

// Usuarios
$routes->get('/usuarios', 'Usuarios::index');
$routes->post('/usuarios/guardar', 'Usuarios::guardar');
$routes->get('/usuarios/all', 'Usuarios::showAll');
$routes->get('/usuarios/(:num)', 'Usuarios::getUsuario/$1');
$routes->get('/usuarios/eliminar/(:num)', 'Usuarios::deleteUsuario/$1');

// Permisos
$routes->get('/permisos', 'Permisos::index');
$routes->get('/permisos/lista-roles', 'Permisos::listaRoles');
$routes->post('/permisos/crear-rol', 'Permisos::createRol');
$routes->get('/permisos/eliminar-rol/(:num)', 'Permisos::deleteRol/$1');

$routes->get('/permisos/cargos', 'Permisos::cargosAll');
$routes->post('/permisos/cargos', 'Permisos::cargosCreate');
$routes->get('/permisos/cargos/(:num)', 'Permisos::permisosShow/$1');
$routes->post('/permisos/guardar', 'Permisos::guardar');

// Legacy routes (mantener compatibilidad)
$routes->get('/home', 'Home::index');
$routes->get('/mobile', 'Auth::dashboard');
$routes->get('/nueva-entrega', 'Entregas::nuevaEntrega');
$routes->get('/control-entregas', 'Entregas::controlEntregas');
$routes->get('/reporte-produccion', 'Entregas::reporteProduccion');
$routes->get('/tareas', 'Tareas::index');
$routes->post('/tareas/guardar', 'Tareas::guardar');
$routes->post('/tareas-all', 'Tareas::tareasAll');
$routes->get('/tarea-horas/(:num)', 'Tareas::getTareaHoras/$1');
$routes->get('/categorias-tareas', 'Tareas::categoriasTareas');
$routes->get('/categorias-tareas/all', 'Tareas::categoriasTareasAll');
