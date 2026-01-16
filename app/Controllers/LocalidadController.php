<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class LocalidadController extends ResourceController
{
    protected $modelName = 'App\Models\LocalidadModel';
    protected $format    = 'json';

    private function hydrateLocalidad($localidad)
    {
        if (!empty($localidad->IdMunicipio)) {
            $municipioObj = [
                'Id'     => $localidad->IdMunicipio,
                'Nombre' => $localidad->mun_nombre ?? 'N/A',
                'Clave'  => $localidad->mun_clave ?? null,
                'Entidad' => [
                    'Id'     => $localidad->id_entidad,
                    'Nombre' => $localidad->ent_nombre ?? 'N/A',
                    'Clave'  => $localidad->ent_clave ?? null
                ]
            ];

            $localidad->Municipio = $municipioObj;
        }
        return $localidad;
    }

    public function index()
    {
        set_time_limit(120);
        try {
            $limite = $this->request->getVar('limit') ?? 10;
            if ($limite > 100) {
                $limite = 100;
            }
            $pagina = $this->request->getVar('page') ?? 1;
            $data = $this->model->withFullRelaciones()->paginate($limite);
            $pager = $this->model->pager;
            foreach ($data as $item) {
                $this->hydrateLocalidad($item);
            }

            return $this->respond([
                'data' => $data,
                'meta' => [
                    'total' => $pager->getTotal(),
                    'per_page' => $limite,
                    'current_page' => $pagina,
                    'total_pages' => $pager->getPageCount()
                ],
                'links' => [
                    'first' => $pager->getPageURI(1),
                    'last' => $pager->getPageURI($pager->getPageCount()),
                    'prev' => ($pagina > 1) ? $pager->getPageURI($pagina - 1) : null,
                    'next' => ($pagina < $pager->getPageCount()) ? $pager->getPageURI($pagina + 1) : null
                ]
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en LocalidadController::index: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener los registros de Localidad');
        }
    }

    public function show($id = null)
    {
        try {
            $solicitud = $this->model->withFullRelaciones()->find($id);
            if (!$solicitud) {
                return $this->failNotFound('No se encontró la localidad con ID: ' . $id);
            }
            $this->hydrateLocalidad($solicitud);
            return $this->respond([
                'status' => 200,
                'data' => $solicitud
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function flitrarAgeb($ageb = null)
    {
        try {
            if (!$ageb) {
                return $this->fail('Se requiere el AGEB.', 400);
            }
            $ageb = strtoupper($ageb);
            $localidades = $this->model->withFullRelaciones()
                ->where('localidad.ageb', $ageb)
                ->findAll();
            foreach ($localidades as $item) {
                $this->hydrateLocalidad($item);
            }
            return $this->respond([
                'status' => 200,
                'data' => $localidades
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function filtrarManzana($manzana = null)
    {
        set_time_limit(120);
        try {
            if (!$manzana) {
                return $this->fail('Se requiere el número de manzana.', 400);
            }
            $localidades = $this->model->withFullRelaciones()
                ->where('localidad.manzana', $manzana)
                ->findAll();
            foreach ($localidades as $item) {
                $this->hydrateLocalidad($item);
            }
            return $this->respond([
                'status' => 200,
                'data'   => $localidades
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en LocalidadController::filtrarManzana: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al filtrar las localidades por manzana');
        }
    }

    public function filtrarAgebManzana($ageb = null, $manzana = null)
    {
        set_time_limit(120);
        try {
            if (!$ageb || !$manzana) {
                return $this->fail('Se requieren el AGEB y la Manzana.', 400);
            }
            $ageb = strtoupper($ageb);
            $localidades = $this->model->withFullRelaciones()
                ->where('localidad.ageb', $ageb)
                ->where('localidad.manzana', $manzana)
                ->findAll();
            foreach ($localidades as $item) {
                $this->hydrateLocalidad($item);
            }
            return $this->respond([
                'status' => 200,
                'data'   => $localidades
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en LocalidadController::filtrarAgebManzana: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al filtrar por AGEB y manzana');
        }
    }

    public function getByMunicipio($idMunicipio = null)
    {
        try {
            $data = $this->model->withFullRelaciones()
                ->where('localidad.id_municipio', $idMunicipio)
                ->findAll();

            foreach ($data as $item) {
                $this->hydrateLocalidad($item);
            }

            return $this->respond(['status' => 200, 'data' => $data]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
