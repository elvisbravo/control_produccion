<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Auth routes
$routes->get('/', 'Auth::signin');
$routes->post('/auth/login', 'Auth::login');

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
