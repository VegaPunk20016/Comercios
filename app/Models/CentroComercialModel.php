<?php

namespace App\Models;

use CodeIgniter\Model;

class CentroComercialModel extends Model
{
    protected $table            = 'centro_comercial';
    protected $primaryKey       = 'id_centro_comercial';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\CentroComercial';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tipoCenCom',
        'nom_CenCom',
    ];
}
