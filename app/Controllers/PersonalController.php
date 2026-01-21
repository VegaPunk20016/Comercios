<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class PersonalController extends ResourceController
{
    protected $modelName = 'App\Models\PersonalModel';
    protected $format    = 'json';

    public function index()
    {
        try {
            $limite = $this->request->getVar('limit') ?? 20; // Subimos a 20 para ver todos
            $pagina = $this->request->getVar('page') ?? 1;

            // IMPORTANTE: Usar 'per_ocu' que es la columna real en la DB
            $personal = $this->model
                ->orderBy('CAST(per_ocu AS UNSIGNED)', 'ASC')
                ->paginate($limite, 'default', $pagina);

            $paginacion = $this->model->pager;

            return $this->respond([
                'data' => $personal,
                'meta' => [
                    'total' => $paginacion->getTotal()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
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
