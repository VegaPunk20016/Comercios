<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Unidad extends Entity implements \JsonSerializable
{
    protected $datamap = [
        'claveUnidad' => 'id_denue',
        'clee' => 'clee',
        'nombreEstablecimiento' => 'nom_estab',
        'razonSocial' => 'raz_social',
        'codigoActividad' => 'codigo_act',
        'personalOcupado' => 'per_ocu',
        'tipoUnidadEconomica' => 'tipoUniEco',
        'telefono' => 'telefono',
        'correoElectronico' => 'correoelec',
        'paginaWeb' => 'www',
        'fechaAlta' => 'fecha_alta',
        'latitud' => 'latitud',
        'longitud' => 'longitud',
        'idDomicilio' => 'id_domicilio'
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
