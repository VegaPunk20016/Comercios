<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class EntidadFederativaController extends ResourceController
{
    protected $modelName = 'App\Models\EntidadFederativaModel';
    protected $format    = 'json';

    public function index()
    {
        set_time_limit(120);
        try {
            $data = $this->model->findAll();

            return $this->respond([
                'status' => 200,
                'error'  => false,
                'data'   => $data 
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en EntidadFederativaController::index: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener los registros de Entidad Federativa');
        }
    }

    public function show($id = null)
    {
        set_time_limit(120);
        try {
            $solicitud = $this->model->find($id);

            if (!$solicitud) {
                return $this->failNotFound('No se encontró la solicitud con ID: ' . $id);
            }

            return $this->respond([
                'status' => 200,
                'data' => $solicitud
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en EntidadFederativaController::show: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener la solicitud al recurso de Entidad Federativa');
        }
    }
}
