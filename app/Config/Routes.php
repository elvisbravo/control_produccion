<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::signin');

$routes->get('/home', 'Home::index');
$routes->get('/mobile', 'Home::dashboard');

$routes->get('/nueva-entrega', 'Entregas::nuevaEntrega');
$routes->get('/control-entregas', 'Entregas::controlEntregas');
