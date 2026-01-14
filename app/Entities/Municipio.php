<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Municipio extends Entity
{
    protected $datamap = [
        'IdMunicipio' => 'id_municipio',
        'ClaveMunicipio' => 'cve_mun',
        'NombreMunicipio' => 'municipio',
        'IdEntidad' => 'id_identidad',
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