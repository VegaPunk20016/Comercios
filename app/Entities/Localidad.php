<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Localidad extends Entity implements \JsonSerializable
{
    protected $datamap = [
        'IdLocalidad' => 'id_localidad',
        'ClaveLocalidad' => 'cve_loc',
        'NombreLocalidad' => 'localidad',
        'IdMunicipio' => 'id_municipio',
    ];
    public $Municipio = null;

    public function jsonSerialize(): array
    {
        $mappedData = parent::jsonSerialize();

        if (!empty($this->Municipio)) {
            $mappedData['Municipio'] = $this->Municipio;
        }

        return $mappedData;
    }
}