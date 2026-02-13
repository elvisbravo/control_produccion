<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Auth routes
$routes->get('/', 'Auth::signin');
$routes->post('/auth/login', 'Auth::login');

$routes->get('notificaciones/(:num)', 'Notificaciones::getNotificaciones/$1');
$routes->get('count-notificaciones/(:num)', 'Notificaciones::countNotification/$1');

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
$routes->get('/tareas/get/(:num)', 'Tareas::getTareaRow/$1');
$routes->get('/tareas/get-by-rol/(:num)', 'Tareas::getTareasByRol/$1');

// Instituciones
$routes->get('/instituciones', 'Institucion::getInstituciones');
$routes->post('/instituciones/guardar', 'Institucion::create');
$routes->get('/instituciones/eliminar/(:num)', 'Institucion::delete/$1');

// Carreras
$routes->get('/carreras', 'Carreras::getInstituciones');
$routes->post('/carreras/guardar', 'Carreras::create');
$routes->get('/carreras/eliminar/(:num)', 'Carreras::delete/$1');

// Feriados
$routes->get('/feriados', 'Feriados::getFeriados');

// Clientes
$routes->post('/prospectos/crear', 'Clientes::createProspecto');
$routes->get('prospectos', 'Clientes::getProspectos');
$routes->get('prospecto/get-row/(:num)', 'Clientes::getProspecto/$1');

// Origen
$routes->get('origen/get-all', 'Origen::getOrigenes');
$routes->post('origen/save', 'Origen::create');
$routes->get('origen/delete/(:num)', 'Origen::delete/$1');

// horario
$routes->get('horario/get-by-id/(:num)', 'Horario::getHorarioById/$1');

// Permisos
$routes->get('/permisos', 'Permisos::index');
$routes->get('/permisos/lista-roles', 'Permisos::listaRoles');
$routes->post('/permisos/crear-rol', 'Permisos::createRol');
$routes->get('/permisos/eliminar-rol/(:num)', 'Permisos::deleteRol/$1');
