<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonalModel extends Model
{
    protected $table            = 'personal_ocupado';
    protected $primaryKey       = 'per_ocu';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Personal';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'descripcion',
        'rango_min',
        'rango_max',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
}
