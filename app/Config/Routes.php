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
$routes->post('/usuario/create', 'Usuarios::create');
$routes->get('/usuario/get-all', 'Usuarios::getUsers');
$routes->get('/usuario/get-row/(:num)', 'Usuarios::getUser/$1');
$routes->get('/usuario/delete/(:num)', 'Usuarios::delete/$1');

//tareas
$routes->post('/tareas/type-save', 'Tareas::createType');
$routes->get('/tareas/type-all', 'Tareas::getTypes');
$routes->post('/tareas/save', 'Tareas::create');
$routes->get('/tareas/all', 'Tareas::getTareas');
$routes->get('/tareas/delete-type/(:num)', 'Tareas::deleteType/$1');
$routes->get('/tareas/delete/(:num)', 'Tareas::delete/$1');

// Permisos
$routes->get('/permisos', 'Permisos::index');
$routes->get('/permisos/lista-roles', 'Permisos::listaRoles');
$routes->post('/permisos/crear-rol', 'Permisos::createRol');
$routes->get('/permisos/eliminar-rol/(:num)', 'Permisos::deleteRol/$1');
