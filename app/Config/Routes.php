<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::signin');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

$routes->get('/usuarios', 'Usuarios::index');
$routes->post('/usuarios/guardar', 'Usuarios::guardar');
$routes->get('/usuarios/all', 'Usuarios::showAll');
$routes->get('/usuarios/(:num)', 'Usuarios::getUsuario/$1');
$routes->get('/usuarios/eliminar/(:num)', 'Usuarios::deleteUsuario/$1');

$routes->get('/home', 'Home::index');
$routes->get('/mobile', 'Home::dashboard');

$routes->get('/nueva-entrega', 'Entregas::nuevaEntrega');
$routes->get('/control-entregas', 'Entregas::controlEntregas');

$routes->get('/clientes', 'Clientes::index');

$routes->get('/permisos', 'Permisos::index');
$routes->get('/permisos/cargos', 'Permisos::cargosAll');
$routes->post('/permisos/cargos', 'Permisos::cargosCreate');
$routes->get('/permisos/cargos/(:num)', 'Permisos::permisosShow/$1');
$routes->post('/permisos/guardar', 'Permisos::guardar');

$routes->get('/reporte-produccion', 'Entregas::reporteProduccion');

$routes->get('/tareas', 'Tareas::index');
$routes->post('/tareas/guardar', 'Tareas::guardar');
$routes->post('/tareas-all', 'Tareas::tareasAll');
$routes->get('/tarea-horas/(:num)', 'Tareas::getTareaHoras/$1');

$routes->get('/categorias-tareas', 'Tareas::categoriasTareas');
$routes->get('/categorias-tareas/all', 'Tareas::categoriasTareasAll');
