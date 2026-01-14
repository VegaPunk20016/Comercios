<?php

namespace App\Models;

use CodeIgniter\Model;

class DomicilioModel extends Model
{
    protected $table            = 'domicilio';
    protected $primaryKey       = 'id_domicilio';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Domicilio';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tipo_vial',
        'nom_vial',
        'numero_ext',
        'letra_ext',
        'numero_int',
        'letra_int',
        'edificio',
        'edificio_e',
        'tipo_asent',
        'nomb_asent',
        'cod_postal',
        'num_local',
        'id_centro_comercial',
        'id_localidad',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
}