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
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
}