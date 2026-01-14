<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->resource('api/actividad', [
    'controller' => 'ActividadController',
    'only' => ['index', 'show']
]);

<<<<<<< HEAD
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
=======
$routes->resource('api/personal', [
    'controller' => 'PersonalController',
    'only' => ['index', 'show']
]);

$routes->resource('api/unidad', [
    'controller' => 'UnidadController',
    'only' => ['index', 'show']
]);

$routes->resource('api/centrocomercial', [
    'controller' => 'CentroComercialController',
>>>>>>> 88f1bcb56df4ad64e6609dbcae6633a43094ca8f
    'only' => ['index', 'show']
]);