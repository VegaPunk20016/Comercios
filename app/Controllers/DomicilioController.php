<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class DomicilioController extends ResourceController
{
    protected $modelName = 'App\Models\DomicilioModel';
    protected $format    = 'json';
    private function hydrateDomicilio($domicilio)
    {
        if (!empty($domicilio->id_localidad)) {
            $domicilio->Localidad = [
                'Nombre' => $domicilio->loc_nombre ?? null,
            ];
        }

        if (!empty($domicilio->id_centro_comercial)) {
            $domicilio->CentroComercial = [
                'Id'     => $domicilio->id_centro_comercial,
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
            $filtros = [
                'ageb'    => $this->request->getVar('ageb'),    // Ej: 0850
                'manzana' => $this->request->getVar('manzana'), // Ej: 024
                'cp'      => $this->request->getVar('cp'),      // Ej: 54800
                'colonia' => $this->request->getVar('colonia'), // Búsqueda por nombre de asentamiento
                'calle'   => $this->request->getVar('calle')    // Búsqueda por nombre de vialidad
            ];
            $domicilios = $this->model->withRelaciones()->aplicarFiltros($filtros)->paginate($limite);

            $paginacion = $this->model->pager;
            if ($domicilios) {
                $domicilios = array_map([$this, 'hydrateDomicilio'], $domicilios);
            }

            return $this->respond([
                'status' => 200,
                'error'  => false,
                'data'   => $domicilios,
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
        try {
            $domicilio = $this->model->withRelaciones()->find($id);
            if (!$domicilio) {
                return $this->failNotFound('Domicilio no encontrado');
            }
            return $this->respond([
                'status' => 200,
                'data'   => $this->hydrateDomicilio($domicilio)
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error DB.');
        } catch (\Exception $e) {
            log_message('error', 'Error en show: ' . $e->getMessage());
            return $this->failServerError('Error del servidor.');
        }
    }
}
