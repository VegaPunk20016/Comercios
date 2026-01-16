<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class ActividadController extends ResourceController
{
    protected $modelName = 'App\Models\ActividadModel';
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
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en ActividadController::index: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener los registros de Actividad');
        }
    }

    public function show($id = null)
    {
        try {
            $actividad = $this->model->find($id);

            if (!$actividad) {
                return $this->failNotFound('No se encontró la actividad con ID: ' . $id);
            }

            return $this->respond([
                'status' => 200,
                'data' => $actividad
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en ActividadController::show: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener la solicitud al recurso de Actividad');
        }
    }

    public function buscar()
    {
        try {
            $query = $this->request->getVar('q');
            if (!$query) {
                return $this->fail('Se requiere un término de búsqueda (q).', 400);
            }

            $data = $this->model->groupStart()
                ->like('nombre_act', $query)
                ->orLike('sector_nom', $query)
                ->orLike('rama_nom', $query)
                ->groupEnd()
                ->paginate(20);

            return $this->respond([
                'status' => 200,
                'data'   => $data,
                'meta'   => [
                    'total' => $this->model->pager->getTotal()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    private function buildTree(array $flatData, $startLevel = 'sector')
    {
        $tree = [];

        foreach ($flatData as $row) {
            if ($startLevel === 'sector') {
                $subKey = $row->subsector_cve;
                if (!isset($tree[$subKey])) {
                    $tree[$subKey] = [
                        'Clave'  => $row->subsector_cve,
                        'Nombre' => $row->subsector_nom,
                        'Type'   => 'Subsector',
                        'Children' => []
                    ];
                }
                $currentLevel = &$tree[$subKey]['Children'];
            } else {
                $currentLevel = &$tree;
            }

            if (in_array($startLevel, ['sector', 'subsector'])) {
                $ramaKey = $row->rama_cve;
                if (!isset($currentLevel[$ramaKey])) {
                    $currentLevel[$ramaKey] = [
                        'Clave'  => $row->rama_cve,
                        'Nombre' => $row->rama_nom,
                        'Type'   => 'Rama',
                        'Children' => []
                    ];
                }
                $currentLevel = &$currentLevel[$ramaKey]['Children'];
            }

            if ($startLevel !== 'subrama') {
                $subramaKey = $row->subrama_cve;
                if (!isset($currentLevel[$subramaKey])) {
                    $currentLevel[$subramaKey] = [
                        'Clave'  => $row->subrama_cve,
                        'Nombre' => $row->subrama_nom,
                        'Type'   => 'Subrama',
                        'Children' => []
                    ];
                }
                $currentLevel = &$currentLevel[$subramaKey]['Children'];
            }

            $currentLevel[] = [
                'Clave'  => $row->codigo_act,
                'Nombre' => $row->nombre_act,
                'Type'   => 'Actividad'
            ];
        }
        return $this->reindexTree($tree);
    }

    private function reindexTree($node)
    {
        $result = array_values($node);
        foreach ($result as &$item) {
            if (isset($item['Children']) && is_array($item['Children'])) {
                $item['Children'] = $this->reindexTree($item['Children']);
            }
        }
        return $result;
    }

    public function getArbol($nivel = null, $clave = null)
    {
        ini_set('memory_limit', '256M');
        try {
            if (!$nivel || !$clave) {
                return $this->fail('Se requiere el nivel (sector, subsector, rama) y la clave.', 400);
            }

            $nivel = strtolower($nivel);
            $claveOriginal = $clave;

            if ($nivel === 'sector') {
                if (in_array($clave, ['31', '32', '33'])) $clave = '31-33';
                if (in_array($clave, ['48', '49'])) $clave = '48-49';
            }
            
            $query = $this->model;

            switch ($nivel) {
                case 'sector':
                    $query->where('sector_cve', $clave);
                    if ($claveOriginal !== $clave) {
                        $query->like('codigo_act', $claveOriginal, 'after');
                    }
                    break;
                case 'subsector':
                    $query->where('subsector_cve', $clave);
                    break;
                case 'rama':
                    $query->where('rama_cve', $clave);
                    break;
                case 'subrama':
                    $query->where('subrama_cve', $clave);
                    break;
                default:
                    return $this->fail('Nivel no válido. Use: sector, subsector, rama, subrama', 400);
            }
            $flatData = $query->findAll(2000);

            if (empty($flatData)) {
                return $this->failNotFound('No se encontraron registros para esa jerarquía.');
            }

            $tree = $this->buildTree($flatData, $nivel);

            return $this->respond([
                'status' => 200,
                'nivel_busqueda' => $nivel,
                'clave_busqueda' => $clave,
                'data' => $tree
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
