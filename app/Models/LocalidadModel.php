<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalidadModel extends Model
{
    protected $table            = 'localidad';
    protected $primaryKey       = 'id_localidad';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Localidad';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cve_loc',
        'localidad',
        'ageb',
        'manzana',
        'id_municipio',
    ];
    public function withFullRelaciones()
    {
        $this->select('localidad.*');
        $this->select('municipio.municipio as mun_nombre, municipio.cve_mun as mun_clave, municipio.id_entidad');
        $this->join('municipio', 'municipio.id_municipio = localidad.id_municipio', 'left');
        $this->select('entidad_federativa.entidad as ent_nombre, entidad_federativa.cve_ent as ent_clave');
        $this->join('entidad_federativa', 'entidad_federativa.id_entidad = municipio.id_entidad', 'left');
        return $this;
    }

}