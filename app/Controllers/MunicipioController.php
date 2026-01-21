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
            $limit   = min((int)($this->request->getVar('limit') ?? 20), 100);
            $page    = (int)($this->request->getVar('page') ?? 1);
            $termino = $this->request->getVar('q');
            $entidad = $this->request->getVar('entidad');
            $data = $this->model
                ->withEntidad()
                ->scopeBuscar($termino, $entidad)
                ->paginate($limit);
            if ($data) {
                $data = array_map([$this, 'hydrateMunicipio'], $data);
            }
            return $this->respond([
                'status' => 200,
                'data'   => $data,
                'meta'   => [
                    'total'        => $this->model->pager->getTotal(),
                    'per_page'     => $limit,
                    'current_page' => $page,
                    'total_pages'  => $this->model->pager->getPageCount()
                ],
                'links' => [
                    'next' => $this->model->pager->getNextPageURI(),
                    'prev' => $this->model->pager->getPreviousPageURI()
                ]
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en MunicipioController::show: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener la solicitud al recurso de Municipio');
        }
    }

    public function show($id = null)
    {
        try {
            $municipio = $this->model->withEntidad()->find($id);

            if (!$municipio) {
                return $this->failNotFound('No se encontró el municipio con ID: ' . $id);
            }

            return $this->respond([
                'status' => 200,
                'data'   => $this->hydrateMunicipio($municipio)
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en MunicipioController::show: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener la solicitud al recurso de Municipio');
        }
    }

    public function getArbol($cveEntidad = null)
    {
        ini_set('memory_limit', '256M');
        try {
            if (!$cveEntidad) {
                return $this->fail('Se requiere la Clave de la Entidad (ej: 15).', 400);
            }
            $datosPlanos = $this->model->getDatosArbol($cveEntidad);
            if (empty($datosPlanos)) {
                return $this->respond(['status' => 200, 'data' => []]);
            }
            $arbol = [];
            foreach ($datosPlanos as $row) {
                $claveMun = $row->cve_mun;
                if (!isset($arbol[$claveMun])) {
                    $arbol[$claveMun] = [
                        'Id'          => $cveEntidad . $row->cve_mun, // ID Único para municipio
                        'Clave'       => $row->cve_mun,  
                        'Nombre'      => $row->nom_mun,
                        'Type'        => 'Municipio',
                        'Localidades' => []
                    ];
                }

                if ($row->cve_loc) {
                    // GENERACIÓN DE ID ÚNICO GLOBAL PARA LOCALIDAD
                    // Esto evita que "0001" de un municipio se confunda con "0001" de otro
                    $idUnicoLoc = $cveEntidad . $row->cve_mun . $row->cve_loc;

                    $arbol[$claveMun]['Localidades'][] = [
                        'Id'     => $idUnicoLoc, // Usamos este para el checkbox
                        'Clave'  => $row->cve_loc,      
                        'Nombre' => $row->nom_loc,
                        'Type'   => 'Localidad'
                    ];
                }
            }
            return $this->respond([
                'status'   => 200,
                'cve_ent'  => $cveEntidad,
                'data'     => array_values($arbol)
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
