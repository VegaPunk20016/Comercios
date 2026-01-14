<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Domicilio extends Entity
{
    protected $datamap = [
        'IdDomicilio' => 'id_domicilio',
        'TipoVialidad' => 'tipo_vial',
        'NombreVialidad' => 'nom_vial',
        'NumeroExterior' => 'numero_ext',
        'LetraExterior' => 'letra_ext',
        'NumeroInterior' => 'numero_int',
        'LetraInterior' => 'letra_int',
        'NombreEdificio' => 'edificio',
        'PisoEdificio' => 'edificio_e',
        'TipoAsentamiento' => 'tipo_asent',
        'NombreAsentamiento' => 'nomb_asent',
        'CodigoPostal' => 'cod_postal',
        'NumeroLocal' => 'num_local',
        'IdCentroComercial' => 'id_centro_comercial',
        'IdLocalidad' => 'id_localidad',
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