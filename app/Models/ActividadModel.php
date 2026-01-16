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
        'sector_cve',
        'sector_nom',
        'subsector_cve',
        'subsector_nom',
        'rama_cve',
        'rama_nom',
        'subrama_cve',
        'subrama_nom',
    ];

}
