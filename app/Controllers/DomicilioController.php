<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class DomicilioController extends ResourceController
{
    protected $modelName = 'App\Models\DomicilioModel';
    protected $format    = 'json';
 private function hydrateDomicilio($domicilio)
{    if (!empty($domicilio->id_localidad)) {
        $domicilio->Localidad = [
            'Nombre' => $domicilio->loc_nombre ?? null, 
            'Ageb'   => $domicilio->loc_ageb ?? null
        ];
    }

    if (!empty($domicilio->id_centro_comercial)) {
        $domicilio->CentroComercial = [
            'Id'     => $domicilio->id_centro_comercial,
            
            // CORREGIDO: Revisa cómo lo nombraste en el modelo. 
            // Si fue 'cc_nombre', esto está bien.
            'Nombre' => $domicilio->cc_nombre ?? null,
            'Tipo'   => $domicilio->cc_tipo ?? null
        ];
    }

    return $domicilio;
}

    public function index()
    {
        set_time_limit(120);
        try {
            $limite = $this->request->getVar('limit') ?? 10;
            $limite = ($limite > 100) ? 100 : $limite;
            $pagina = $this->request->getVar('page') ?? 1;
            $domicilios = $this->model->withRelaciones()->paginate($limite);

            $paginacion = $this->model->pager;
            if ($domicilios) {
                foreach ($domicilios as &$dom) { 
                    $this->hydrateDomicilio($dom);
                }
            }

            return $this->respond([
                'status' => 200,
                'error'  => false,
                'data'   => $domicilios, // Al enviarlo, se activa jsonSerialize de la Entidad
                'meta'   => [
                    'total'        => $paginacion->getTotal(),
                    'per_page'     => (int)$limite,
                    'current_page' => (int)$pagina,
                    'total_pages'  => $paginacion->getPageCount()
                ],
                'links' => [
                    'first' => $paginacion->getPageURI(1),
                    'last'  => $paginacion->getPageURI($paginacion->getPageCount()),
                    'prev'  => $paginacion->getPreviousPageURI(),
                    'next'  => $paginacion->getNextPageURI()
                ]
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en DomicilioController::index: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener los registros.');
        }
    }

    public function show($id = null)
    {
        set_time_limit(120);
        try {
            // 1. Usamos withRelaciones() con find()
            $solicitud = $this->model->withRelaciones()->find($id);

            if (!$solicitud) {
                return $this->failNotFound('No se encontró el domicilio con ID: ' . $id);
            }

            // 2. Hidratamos la única entidad encontrada
            $this->hydrateDomicilio($solicitud);

            return $this->respond([
                'status' => 200,
                'error'  => false,
                'data'   => $solicitud
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error DB.');
        } catch (\Exception $e) {
            log_message('error', 'Error en show: ' . $e->getMessage());
            return $this->failServerError('Error del servidor.');
        }
    }

    public function showByCodPost($cod_post = null)
    {
        set_time_limit(120);
        try {
            // 1. Usamos withRelaciones() con where()
            $solicitudes = $this->model->withRelaciones()
                ->where('domicilio.cod_postal', $cod_post) // Especificamos tabla por si hay ambigüedad
                ->findAll();

            if (empty($solicitudes)) {
                return $this->failNotFound('No se encontraron domicilios con CP: ' . $cod_post);
            }

            // 2. Hidratamos el array de resultados
            foreach ($solicitudes as &$sol) {
                $this->hydrateDomicilio($sol);
            }

            return $this->respond([
                'status' => 200,
                'error'  => false,
                'data'   => $solicitudes
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error DB.');
        } catch (\Exception $e) {
            log_message('error', 'Error en showByCodPost: ' . $e->getMessage());
            return $this->failServerError('Error del servidor.');
        }
    }
}
