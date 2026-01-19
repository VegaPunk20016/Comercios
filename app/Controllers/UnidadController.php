<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UnidadController extends ResourceController
{
    protected $modelName = 'App\Models\UnidadModel';
    protected $format    = 'json';

    /**
     * Convierte la fila plana de la BD en un objeto JSON anidado y estructurado.
     */
    private function hydrateUnidad($ue)
    {
        // 1. Actividad SCIAN
        if (!empty($ue->CodigoActividad)) {
            $ue->Actividad = [
                'Codigo' => $ue->CodigoActividad,
                'Nombre' => $ue->act_nombre ?? 'N/A'
            ];
        }

        // 2. Estrato de Personal
        if (!empty($ue->PersonalOcupado)) {
            $ue->EstratoPersonal = $ue->per_desc ?? $ue->PersonalOcupado;
        }

        // 3. Domicilio Completo
        if (!empty($ue->IdDomicilio)) {

            // Objeto Entidad
            $entidadObj = [
                'Nombre' => $ue->ent_nombre ?? 'N/A',
                'Clave'  => $ue->ent_clave ?? null
            ];

            // Objeto Municipio
            $municipioObj = [
                'Nombre'  => $ue->mun_nombre ?? 'N/A',
                'Entidad' => $entidadObj
            ];

            // Objeto Localidad (¡Ahora con AGEB y Manzana!)
            $localidadObj = [
                'Nombre'    => $ue->loc_nombre ?? 'N/A',
                'Municipio' => $municipioObj
            ];

            // Objeto Centro Comercial
            $centroComercialObj = null;
            if (!empty($ue->centrocomercial_nombre)) {
                $centroComercialObj = ['Nombre' => $ue->centrocomercial_nombre];
            }

            $ue->Domicilio = [
                'Id'              => $ue->IdDomicilio,
                'Calle'           => $ue->nom_vial ?? '',
                'Numero'          => $ue->numero_ext ?? '',
                'Colonia'         => $ue->nomb_asent ?? '',
                'CP'              => $ue->cod_postal ?? '',
                'AGEB'            => $ue->ageb ?? '',      
                'Manzana'         => $ue->manzana ?? '',   
                'Localidad'       => $localidadObj,
                'CentroComercial' => $centroComercialObj
            ];
        }

        return $ue;
    }

    public function index()
    {
        set_time_limit(180);
        try {
            $limit = $this->request->getVar('limit') ?? 10;
            $limit = ($limit > 100) ? 100 : $limit;
            $pagina = $this->request->getVar('page') ?? 1;

            $filtros = [
                'cve_ent'      => $this->request->getVar('cve_ent'),
                'municipio'    => $this->request->getVar('municipio'),
                'cp'           => $this->request->getVar('cp'),
                'ageb'         => $this->request->getVar('ageb'),
                'manzana'      => $this->request->getVar('manzana'),
                'clee'         => $this->request->getVar('clee'),
                'actividad'    => $this->request->getVar('actividad'), 
                'nombre'       => $this->request->getVar('nombre'),       
                'razon_social' => $this->request->getVar('razon_social'), 
                'fecha_alta'   => $this->request->getVar('fecha_alta'),   
                'per_ocu'      => $this->request->getVar('per_ocu'),      
                'q'            => $this->request->getVar('q')
            ];
            $unidades = $this->model
                ->withAllData()
                ->aplicarFiltros($filtros)
                ->paginate($limit);

            $paginacion = $this->model->pager;
            foreach ($unidades as $item) {
                $this->hydrateUnidad($item);
            }
            return $this->respond([
                'status' => 200,
                'filtros_aplicados' => array_filter($filtros), // Útil para depurar qué filtros llegaron
                'data' => $unidades,
                'meta' => [
                    'total' => $paginacion->getTotal(),
                    'per_page' => $limit,
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
            log_message('error', 'Error en UnidadController::index: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener los registros de unidades');
        }
    }

    public function show($id = null)
    {
        try {
            $unidad = $this->model->withAllData()->find($id);

            if (!$unidad) {
                return $this->failNotFound('No se encontró la unidad con ID: ' . $id);
            }
            $this->hydrateUnidad($unidad);
            return $this->respond([
                'status' => 200,
                'data' => $unidad
            ]);
        } catch (\mysqli_sql_exception $e) {
            log_message('critical', $e->getMessage());
            return $this->failServerError('Error crítico en la base de datos.');
        } catch (\Exception $e) {
            log_message('error', 'Error en UnidadController::show: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error al obtener el registro de unidad');
        }
    }
}
