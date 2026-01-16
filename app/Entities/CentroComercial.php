<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class CentroComercial extends Entity implements \JsonSerializable
{
    protected $datamap = [
        'Tipo' => 'tipoCenCom',
        'Nombre' => 'nom_CenCom',
    ];
    
   public function jsonSerialize(): array
    {
        return parent::jsonSerialize();
    }
}
