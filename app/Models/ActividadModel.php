<?php

namespace App\Models;

use CodeIgniter\Model;

class ActividadModel extends Model
{
    protected $table            = 'actividad_scian';
    protected $primaryKey       = 'codigo_act';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Actividad';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nombre_act',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
}
