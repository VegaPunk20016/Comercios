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

$routes->resource('api/entidadFederativa', [
    'controller' => 'EntidadFederativaController',
    'only' => ['index', 'show']
]);

$routes->resource('api/municipio', [
    'controller' => 'MunicipioController',
    'only' => ['index', 'show']
]);

$routes->resource('api/localidad', [
    'controller' => 'LocalidadController',
    'only' => ['index', 'show']
]);

$routes->resource('api/domicilio', [
    'controller' => 'DomicilioController',
    'only' => ['index', 'show']
]);

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
    'only' => ['index', 'show']
]);
