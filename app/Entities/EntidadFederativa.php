<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class EntidadFederativa extends Entity
{
    protected $datamap = [
        'IdIdentidad' => 'id_identidad',
        'ClaveEntidad' => 'cve_ent',
        'NombreEntidad' => 'entidad',
    ];
    
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();  
        $mappedData = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->datamap)) {
                $mappedData[$this->datamap[$key]] = $value; 
            } else {
                $mappedData[$key] = $value; 
            }
        }

        if (isset($mappedData['CheckList'])) {
            $mappedData['CheckList'] = filter_var($mappedData['CheckList'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }
        return $mappedData;  
    }
}