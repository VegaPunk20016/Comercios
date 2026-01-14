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

$routes->resource('entidadFederativa', [
    'controller' => 'EntidadFederativaController',
    'only' => ['index', 'show']
]);

$routes->resource('municipio', [
    'controller' => 'MunicipioController',
    'only' => ['index', 'show']
]);

$routes->resource('localidad', [
    'controller' => 'LocalidadController',
    'only' => ['index', 'show']
]);

$routes->resource('domicilio', [
    'controller' => 'DomicilioController',
    'only' => ['index', 'show']
]);