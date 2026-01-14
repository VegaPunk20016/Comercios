<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->resource('actividad', [
    'controller' => 'ActividadController',
    'only' => ['index', 'show']
]);