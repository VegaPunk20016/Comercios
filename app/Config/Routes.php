<?php

use CodeIgniter\Commands\Utilities\Routes;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {

    #Actividad
    $routes->get('actividad/arbol/(:segment)/(:segment)', 'ActividadController::getArbol/$1/$2');
    $routes->resource('actividad', [
        'controller' => 'ActividadController',
        'only' => ['index', 'show']
    ]);

    #EntidadFederativa
    $routes->resource('entidadFederativa', [
        'controller' => 'EntidadFederativaController',
        'only' => ['index', 'show']
    ]);
    
    #Municipio
    $routes->get('municipio/arbol/(:num)', 'MunicipioController::getArbol/$1');
    $routes->resource('municipio', [
        'controller' => 'MunicipioController',
        'only' => ['index', 'show']
    ]);


    #Localidad
    $routes->resource('localidad', [
        'controller' => 'LocalidadController',
        'only' => ['index', 'show']
    ]);

    #Domicilio
    $routes->resource('domicilio', [
        'controller' => 'DomicilioController',
        'only' => ['index', 'show']
    ]);

    #Personal
    $routes->resource('personal', [
        'controller' => 'PersonalController',
        'only' => ['index', 'show']
    ]);

    #Unidad 
    $routes->resource('unidad', [
        'controller' => 'UnidadController',
        'only' => ['index', 'show']
    ]);

    #CentroComercial
    $routes->resource('centrocomercial', [
        'controller' => 'CentroComercialController',
        'only' => ['index', 'show']
    ]);
});
