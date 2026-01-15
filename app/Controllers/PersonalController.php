<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class PersonalController extends ResourceController
{
    protected $modelName = 'App\Models\PersonalModel';
    protected $format    = 'json';

    public function index()
    {
        set_time_limit(120);
        try {
            $limite = $this->request->getVar('limit') ?? 10;
            if ($limite > 100) {
                $limite = 100;
            }
            $pagina = $this->request->getVar('page') ?? 1;

            $personal = $this->model->paginate($limite);
            $paginacion = $this->model->pager;

            return $this->respond([
                'data' => $personal,
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
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en PersonalController::index: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener los registros de personal');
        }
    }

    public function show($id = null)
    {
        try {
            $personal = $this->model->find($id);

            if (!$personal) {
                return $this->failNotFound('No se encontró el personal con ID: ' . $id);
            }

            return $this->respond([
                'status' => 200,
                'data' => $personal
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en PersonalController::show: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener el registro de personal');
        }
    }
}
