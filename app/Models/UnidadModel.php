<?php

namespace App\Models;

use CodeIgniter\Model;

class UnidadModel extends Model
{
    protected $table            = 'unidad_economica';
    protected $primaryKey       = 'id_denue';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Unidad';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'clee',
        'nom_estab',
        'raz_social',
        'codigo_act',
        'per_ocu',
        'tipoUniEco',
        'telefono',
        'correoelec',
        'www',
        'fecha_alta',
        'latitud',
        'longitud',
        'id_domicilio',
    ];
}
