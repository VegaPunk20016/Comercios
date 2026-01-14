<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EntidadFederativaModel;
use CodeIgniter\RESTful\ResourceController;

class EntidadFederativaController extends ResourceController
{
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new EntidadFederativaModel();
    }

    public function index()
    {
        $limite = 10; // Número de registros por página
        $pagina = $this->request->getVar('page') ?? 1;

        // Obtener datos paginados
        $actividad = $this->model->paginate($limite);
        $paginacion = $this->model->pager;

        return $this->respond([
            'data' => $actividad, 
            'meta' => [
                'total' => $paginacion->getTotal(),
                'per_page' => $limite,
                'current_page' => $pagina,
                'total_pages' => $paginacion->getPageCount()
            ],
            'links' => [
                'first' => $paginacion->getPageURI(1),
                'last' => $paginacion->getPageURI($paginacion->getPageCount()),
                'prev' => ($pagina > 1) ? $paginacion->getPageURI($pagina - 1) : null,
                'next' => ($pagina < $paginacion->getPageCount()) ? $paginacion->getPageURI($pagina + 1) : null
            ]
        ]);
    }

    public function show($id = null)
    {
        try {
            $solicitud = $this->model->find($id);

            if (!$solicitud) {
                return $this->failNotFound('No se encontró la solicitud con ID: ' . $id);
            }

            return $this->respond([
                'status' => 200,
                'data' => $solicitud
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en SolicitudController::show: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener la solicitud');
        }
    }
}