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
        return parent::jsonSerialize();
    }
}