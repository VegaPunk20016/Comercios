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
    ];

    public function scopeBuscar($termino)
    {
        return $this->groupStart()
            ->like('per_ocu', $termino) 
            ->groupEnd();
    }
}
