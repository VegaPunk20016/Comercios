<?php

namespace App\Models;

use CodeIgniter\Model;

class EntidadFederativaModel extends Model
{
    protected $table            = 'entidad_federativa';
    protected $primaryKey       = 'id_entidad';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\EntidadFederativa';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cve_ent',
        'entidad',
    ];
    public function scopeBuscar($termino)
    {
        return $this->groupStart()
            ->like('entidad', $termino)   
            ->orLike('cve_ent', $termino) 
            ->groupEnd();
    }
}