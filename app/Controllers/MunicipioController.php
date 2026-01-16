<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class MunicipioController extends ResourceController
{
    protected $modelName = 'App\Models\MunicipioModel';
    protected $format    = 'json';
    private function hydrateMunicipio($municipio)
    {
        if (!empty($municipio->IdEntidad)) {
            $municipio->Entidad = [
                'Id'     => $municipio->IdEntidad,
                'Nombre' => $municipio->ent_nombre ?? 'N/A',
                'Clave'  => $municipio->ent_clave ?? null
            ];
        }
        return $municipio;
    }

    public function index()
    {
        try {
            $limit = $this->request->getVar('limit') ?? 20; // Municipios son muchos, subimos el default
            $page = $this->request->getVar('page') ?? 1;
            $data = $this->model->withEntidad()->paginate($limit);
            $pager = $this->model->pager;
            foreach ($data as $item) {
                $this->hydrateMunicipio($item);
            }
            return $this->respond([
                'status' => 200,
                'data'   => $data,
                'meta'   => [
                    'total' => $pager->getTotal(),
                    'page'  => $page,
                    'pages' => $pager->getPageCount()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
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
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en MunicipioController::show: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener la solicitud al recurso de Municipio');
        }
    }

    public function getByEntidad($idEntidad = null)
    {
        try {
            $data = $this->model->withEntidad()
                ->where('municipio.id_entidad', $idEntidad)
                ->findAll();

            if (empty($data)) {
                return $this->respond(['status' => 200, 'data' => []]); 
            }

            foreach ($data as $item) {
                $this->hydrateMunicipio($item);
            }

            return $this->respond([
                'status' => 200,
                'data'   => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
