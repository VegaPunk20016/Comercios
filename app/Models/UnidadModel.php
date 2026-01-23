<?php

namespace App\Models;

use CodeIgniter\Model;

class UnidadModel extends Model
{
    protected $table            = 'unidad_economica';
    protected $primaryKey       = 'id_denue';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Unidad';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'clee',
        'nom_estab',
        'raz_social',
        'codigo_act',
        'per_ocu',
        'tipoUniEco',
        'telefono',
        'correoelec',
        'www',
        'fecha_alta',
        'latitud',
        'longitud',
        'id_domicilio',
    ];

    public function withAllData()
    {
        $this->select('unidad_economica.*');

        // Relación con Actividad SCIAN
        $this->select('actividad_scian.nombre_act as act_nombre');
        $this->join('actividad_scian', 'actividad_scian.codigo_act = unidad_economica.codigo_act', 'left');

        // Relación con Personal Ocupado
        $this->select('personal_ocupado.per_ocu as per_desc');
        $this->join('personal_ocupado', 'personal_ocupado.per_ocu = unidad_economica.per_ocu', 'left');

        // Relación con Domicilio
        $this->select('domicilio.nom_vial, domicilio.numero_ext, domicilio.cod_postal, domicilio.ageb, domicilio.manzana, domicilio.nomb_asent');
        $this->join('domicilio', 'domicilio.id_domicilio = unidad_economica.id_domicilio', 'left');

        // Relación con Centro Comercial
        $this->select('centro_comercial.nom_CenCom as centrocomercial_nombre');
        $this->join('centro_comercial', 'centro_comercial.id_centro_comercial = domicilio.id_centro_comercial', 'left');

        $this->select('localidad.localidad as loc_nombre, localidad.id_municipio,');
        $this->join('localidad', 'localidad.id_localidad = domicilio.id_localidad', 'left');

        // Relación con Municipio
        $this->select('municipio.municipio as mun_nombre, municipio.id_entidad');
        $this->join('municipio', 'municipio.id_municipio = localidad.id_municipio', 'left');

        // Relación con Entidad Federativa
        $this->select('entidad_federativa.entidad as ent_nombre, entidad_federativa.cve_ent as ent_clave');
        $this->join('entidad_federativa', 'entidad_federativa.id_entidad = municipio.id_entidad', 'left');

        return $this;
    }


    public function aplicarFiltros($filtros)
    {
        // 1. Filtro por Entidad
        if (!empty($filtros['cve_ent'])) {
            $this->where('entidad_federativa.cve_ent', $filtros['cve_ent']);
        }

        // 2. Filtro por Municipios (Múltiple)
        if (!empty($filtros['municipio'])) {
            $municipios = explode(',', $filtros['municipio']);
            $this->whereIn('municipio.id_municipio', $municipios);
        }

        // 3. Filtro por Localidades (Múltiple) - Agregado para soportar el front
        if (!empty($filtros['localidad'])) {
            $localidades = explode(',', $filtros['localidad']);
            $this->whereIn('localidad.id_localidad', $localidades);
        }

        // 4. Filtro por Actividades SCIAN (Múltiple)
        if (!empty($filtros['actividad'])) {
            $actividades = explode(',', $filtros['actividad']);
            // Usamos whereIn para códigos exactos múltiples
            $this->whereIn('unidad_economica.codigo_act', $actividades);
        }

        // 5. Filtro por Personal Ocupado (Múltiple)
        if (!empty($filtros['per_ocu'])) {
            $estratos = explode(',', $filtros['per_ocu']);
            $this->whereIn('unidad_economica.per_ocu', $estratos);
        }

        // 6. Filtros de coincidencia exacta (AGEB, Manzana, CP)
        if (!empty($filtros['cp'])) {
            $this->where('domicilio.cod_postal', $filtros['cp']);
        }
        if (!empty($filtros['ageb'])) {
            $this->where('domicilio.ageb', $filtros['ageb']);
        }
        if (!empty($filtros['manzana'])) {
            $this->where('domicilio.manzana', $filtros['manzana']);
        }

        // 7. Buscador general (q) o por campos específicos
        if (!empty($filtros['q'])) {
            $this->groupStart()
                ->like('unidad_economica.nom_estab', $filtros['q'])
                ->orLike('unidad_economica.raz_social', $filtros['q'])
                ->groupEnd();
        } elseif (!empty($filtros['nombre'])) {
            $this->like('unidad_economica.nom_estab', $filtros['nombre']);
        }

        return $this;
    }
}
