<?php

use CodeIgniter\Commands\Utilities\Routes;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {

    #Actividad

    $routes->get('actividad/buscar', 'ActividadController::buscar');
    $routes->get('actividad/arbol/(:segment)/(:segment)', 'ActividadController::getArbol/$1/$2');
    $routes->resource('actividad', [
        'controller' => 'ActividadController',
        'only' => ['index', 'show']
    ]);

    #EntidadFederativa
    $routes->get('municipio/por-entidad/(:num)', 'MunicipioController::getByEntidad/$1');
    $routes->resource('entidadFederativa', [
        'controller' => 'EntidadFederativaController',
        'only' => ['index', 'show']
    ]);
    #Municipio
    $routes->get('municipiosPorEntidad/(:segment)', 'MunicipioController::getByEntidad/$1');
    $routes->resource('municipio', [
        'controller' => 'MunicipioController',
        'only' => ['index', 'show']
    ]);

    #Localidad
    $routes->get('localidad/municipio/(:num)', 'LocalidadController::getByMunicipio/$1');
    $routes->get('localidad/ageb/(:segment)', 'LocalidadController::flitrarAgeb/$1');
    $routes->get('localidad/manzana/(:segment)', 'LocalidadController::filtrarManzana/$1');
    $routes->get('localidad/filtro/(:segment)/(:segment)', 'LocalidadController::filtrarAgebManzana/$1/$2');
    $routes->resource('localidad', [
        'controller' => 'LocalidadController',
        'only' => ['index', 'show']
    ]);

    #Domicilio
    $routes->get('domicilio/cp/(:segment)', 'DomicilioController::showByCodPost/$1');
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
    $routes->get('centrocomercial/buscar', 'CentroComercialController::buscar');
    $routes->resource('centrocomercial', [
        'controller' => 'CentroComercialController',
        'only' => ['index', 'show']
    ]);
});
