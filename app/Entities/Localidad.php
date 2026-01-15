<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Localidad extends Entity implements \JsonSerializable
{
    protected $datamap = [
        'IdLocalidad' => 'id_localidad',
        'ClaveLocalidad' => 'cve_loc',
        'NombreLocalidad' => 'localidad',
        'ExtensionTerritorial' => 'ageb',
        'Manzana' => 'manzana',
        'IdMunicipio' => 'id_municipio',
    ];
    
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize(); 
        $mappedData = [];

        $reverseMap = array_flip($this->datamap);

        foreach ($data as $dbKey => $value) {
            if (array_key_exists($dbKey, $reverseMap)) {
                $cleanKey = $reverseMap[$dbKey];
                $mappedData[$cleanKey] = $value;
            } else {
                $mappedData[$dbKey] = $value;
            }
        }

        if (isset($mappedData['CheckList'])) {
            $mappedData['CheckList'] = filter_var($mappedData['CheckList'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        return $mappedData; 
    }
}