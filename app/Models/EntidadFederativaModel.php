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

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
}