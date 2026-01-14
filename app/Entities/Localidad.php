<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Localidad extends Entity
{
    protected $datamap = [
        'IdLocalidad' => 'id_localidad',
        'ClaveLocalidad' => 'cve_loc',
        'NombreLocalidad' => 'localidad',
        'ExtensionTerritorial' => 'ageb',
        'Manzana' => 'manzana',
        'IdMunicipio' => 'id_municipio',
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