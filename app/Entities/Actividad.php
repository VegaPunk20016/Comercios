<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Actividad extends Entity implements \JsonSerializable
{
    // Mapeo: 'NombreEnCodigo' => 'nombre_en_db'
    protected $datamap = [
        'CodigoActividad' => 'codigo_act',
        'NombreActividad' => 'nombre_act',
        'ClaveSector' => 'sector_cve',
        'NombreSector' => 'sector_nom',
        'ClaveSubsector' => 'subsector_cve',
        'NombreSubsector' => 'subsector_nom',
        'ClaveRama' => 'rama_cve',
        'NombreRama' => 'rama_nom',
        'ClaveSubrama' => 'subrama_cve',
        'NombreSubrama' => 'subrama_nom',
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