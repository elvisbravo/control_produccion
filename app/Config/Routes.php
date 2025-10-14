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

$routes->get('/home', 'Home::index');
$routes->get('/mobile', 'Home::dashboard');

$routes->get('/nueva-entrega', 'Entregas::nuevaEntrega');
$routes->get('/control-entregas', 'Entregas::controlEntregas');

$routes->get('/permisos', 'Permisos::index');
$routes->get('/permisos/cargos', 'Permisos::cargosAll');
$routes->post('/permisos/cargos', 'Permisos::cargosCreate');
$routes->get('/permisos/cargos/(:num)', 'Permisos::permisosShow/$1');
$routes->post('/permisos/guardar', 'Permisos::guardar');
