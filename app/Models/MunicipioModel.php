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
    public function scopeBuscar($termino, $idEntidad = null)
    {
        if (!empty($idEntidad)) {
            $this->where('municipio.id_entidad', $idEntidad);
        }
        if (!empty($termino)) {
            $this->like('municipio.municipio', $termino);
        }
        return $this;
    }

    public function getDatosArbol($cveEntidad)
    {
        return $this->select('municipio.cve_mun, municipio.municipio as nom_mun')
            ->select('localidad.cve_loc, localidad.localidad as nom_loc')
            ->join('localidad', 'localidad.id_municipio = municipio.id_municipio')
            ->join('entidad_federativa', 'entidad_federativa.id_entidad = municipio.id_entidad')
            ->where('entidad_federativa.cve_ent', $cveEntidad)
            ->orderBy('municipio.cve_mun', 'ASC')
            ->orderBy('localidad.cve_loc', 'ASC')
            ->findAll();
    }
}
