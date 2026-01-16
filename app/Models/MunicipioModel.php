<?php

namespace App\Models;

use CodeIgniter\Model;

class MunicipioModel extends Model
{
    protected $table            = 'municipio';
    protected $primaryKey       = 'id_municipio';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Municipio';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cve_mun',
        'municipio',
        'id_entidad'
    ];

    public function withEntidad()
    {
        $this->select('municipio.*');
        $this->select('entidad_federativa.entidad as ent_nombre, entidad_federativa.cve_ent as ent_clave');
        $this->join('entidad_federativa', 'entidad_federativa.id_entidad = municipio.id_entidad', 'left');
        return $this;
    }
}