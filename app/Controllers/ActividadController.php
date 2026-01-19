<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class ActividadController extends ResourceController
{
    protected $modelName = 'App\Models\ActividadModel';
    protected $format    = 'json';

    public function index()
    {
        try {
            $limit = min((int)($this->request->getVar('limit') ?? 10), 100);
            $page  = (int)($this->request->getVar('page') ?? 1);          
            $data = $this->model->paginate($limit);
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
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function show($id = null)
    {
        try {
            $actividad = $this->model->find($id);

            if (!$actividad) {
                return $this->failNotFound("No se encontró la actividad con ID: $id");
            }

            return $this->respond(['status' => 200, 'data' => $actividad]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function buscar()
    {
        try {
            $query = $this->request->getVar('q');
            if (!$query) {
                return $this->fail('Se requiere un término de búsqueda (q).', 400);
            }
            $data = $this->model->scopeBuscar($query)->paginate(20);
            return $this->respond([
                'status' => 200,
                'data'   => $data,
                'meta'   => ['total' => $this->model->pager->getTotal()]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function getArbol($nivel = null, $clave = null)
    {
        ini_set('memory_limit', '256M'); 
        try {
            if (!$nivel || !$clave) {
                return $this->fail('Faltan parámetros: nivel y clave.', 400);
            }

            $nivel = strtolower($nivel);
            $claveOriginal = $clave;
            $mapaSectores = [
                '31' => '31-33',
                '32' => '31-33',
                '33' => '31-33',
                '48' => '48-49',
                '49' => '48-49'
            ];

            if ($nivel === 'sector' && isset($mapaSectores[$clave])) {
                $clave = $mapaSectores[$clave];
            }

            $nivelesPermitidos = ['sector', 'subsector', 'rama', 'subrama'];
            if (!in_array($nivel, $nivelesPermitidos)) {
                return $this->fail('Nivel no válido.', 400);
            }
            $columna = $nivel . '_cve';
            $this->model->where($columna, $clave);
            if ($nivel === 'sector' && $claveOriginal !== $clave) {
                $this->model->like('codigo_act', $claveOriginal, 'after');
            }
            $flatData = $this->model->findAll(2000);
            if (empty($flatData)) {
                return $this->failNotFound('No hay datos para esta jerarquía.');
            }
            $tree = $this->buildTree($flatData, $nivel);
            return $this->respond([
                'status' => 200,
                'meta'   => [
                    'nivel' => $nivel,
                    'clave_solicitada' => $claveOriginal,
                    'clave_bd' => $clave
                ],
                'data'   => $tree
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    private function buildTree(array $flatData, $startLevel)
    {
        $tree = [];
        foreach ($flatData as $row) {
            $currentLevel = &$tree;

            if ($startLevel === 'sector') {
                $k = $row->subsector_cve;
                if (!isset($tree[$k])) {
                    $tree[$k] = $this->makeNode($k, $row->subsector_nom, 'Subsector');
                }
                $currentLevel = &$tree[$k]['Children'];
            }

            if (in_array($startLevel, ['sector', 'subsector'])) {
                $k = $row->rama_cve;
                if (!isset($currentLevel[$k])) {
                    $currentLevel[$k] = $this->makeNode($k, $row->rama_nom, 'Rama');
                }
                $currentLevel = &$currentLevel[$k]['Children'];
            }

            if ($startLevel !== 'subrama') {
                $k = $row->subrama_cve;
                if (!isset($currentLevel[$k])) {
                    $currentLevel[$k] = $this->makeNode($k, $row->subrama_nom, 'Subrama');
                }
                $currentLevel = &$currentLevel[$k]['Children'];
            }

            $currentLevel[] = $this->makeNode($row->codigo_act, $row->nombre_act, 'Actividad', false);
        }

        return $this->reindexTree($tree);
    }

    private function makeNode($clave, $nombre, $type, $hasChildren = true)
    {
        $node = [
            'Clave'  => $clave,
            'Nombre' => $nombre,
            'Type'   => $type
        ];
        if ($hasChildren) {
            $node['Children'] = [];
        }
        return $node;
    }

    private function reindexTree($node)
    {
        $result = array_values($node);
        foreach ($result as &$item) {
            if (isset($item['Children'])) {
                $item['Children'] = $this->reindexTree($item['Children']);
            }
        }
        return $result;
    }
}
