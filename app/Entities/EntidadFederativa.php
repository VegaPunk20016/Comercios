<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class EntidadFederativa extends Entity implements \JsonSerializable
{
    protected $datamap = [  
        'ClaveEntidad' => 'cve_ent',
        'NombreEntidad' => 'entidad',
    ];
    
   public function jsonSerialize(): array
    {
        return parent::jsonSerialize();
    }
}