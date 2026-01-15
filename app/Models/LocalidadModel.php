<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalidadModel extends Model
{
    protected $table            = 'localidad';
    protected $primaryKey       = 'id_localidad';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Localidad';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cve_loc',
        'localidad',
        'ageb',
        'manzana',
        'id_municipio',
    ];

}