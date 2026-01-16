<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class UnidadController extends ResourceController
{
    protected $modelName = 'App\Models\UnidadModel';
    protected $format    = 'json';

    private function hydrateUnidad($ue)
    {
        if (!empty($ue->CodigoActividad)) {
            $ue->Actividad = [
                'Codigo' => $ue->CodigoActividad,
                'Nombre' => $ue->act_nombre ?? 'N/A'
            ];
        }
        if (!empty($ue->PersonalOcupado)) {
            $ue->EstratoPersonal = $ue->per_desc ?? $ue->PersonalOcupado;
        }
        if (!empty($ue->IdDomicilio)) {

            $entidadObj = [
                'Nombre' => $ue->ent_nombre ?? 'N/A',
                'Clave'  => $ue->ent_clave ?? null
            ];
            $municipioObj = [
                'Nombre'  => $ue->mun_nombre ?? 'N/A',
                'Entidad' => $entidadObj // Anidamos Entidad
            ];
            $localidadObj = [
                'Nombre'    => $ue->loc_nombre ?? 'N/A',
                'Municipio' => $municipioObj // Anidamos Municipio
            ];
            $centroComercialObj = null;
            if (!empty($ue->cc_nombre)) {
                $centroComercialObj = ['Nombre' => $ue->cc_nombre];
            }
            $ue->Domicilio = [
                'Id'              => $ue->IdDomicilio,
                'Calle'           => $ue->nom_vial ?? '',
                'Numero'          => $ue->numero_ext ?? '',
                'Colonia'         => $ue->nomb_asent ?? '',
                'CP'              => $ue->cod_postal ?? '',
                'Localidad'       => $localidadObj, // Anidamos Localidad
                'CentroComercial' => $centroComercialObj
            ];
        }
        return $ue;
    }

    public function index()
    {
        set_time_limit(180);
        try {
            $limite = $this->request->getVar('limit') ?? 10;
            if ($limite > 100) {
                $limite = 100;
            }
            $pagina = $this->request->getVar('page') ?? 1;

            $unidades = $this->model->withAllData()->paginate($limite);
            $paginacion = $this->model->pager;

            foreach ($unidades as $item) {
                $this->hydrateUnidad($item);
            }
            return $this->respond([
                'data' => $unidades,
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
