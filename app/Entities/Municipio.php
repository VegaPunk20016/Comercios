<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Municipio extends Entity implements \JsonSerializable
{
    protected $datamap = [
        'IdMunicipio' => 'id_municipio',
        'ClaveMunicipio' => 'cve_mun',
        'NombreMunicipio' => 'municipio',
        'IdEntidad' => 'id_entidad',
    ];
    public $Entidad = null;

    public function jsonSerialize(): array
    {
        $mappedData = parent::jsonSerialize();

        if (!empty($this->Entidad)) {
            $mappedData['Entidad'] = $this->Entidad;
        }

        return $mappedData;
    }
}
