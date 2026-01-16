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

    public $Actividad = null;
    public $Domicilio = null;
    public $EstratoPersonal = null;

    public function jsonSerialize(): array
    {
        $mappedData = parent::jsonSerialize();
        if (!empty($this->Actividad)) {
            $mappedData['Actividad'] = $this->Actividad;
        }
        if (!empty($this->Domicilio)) {
            $mappedData['Domicilio'] = $this->Domicilio;
        }
        if (!empty($this->EstratoPersonal)) {
            $mappedData['EstratoPersonal'] = $this->EstratoPersonal;
        }
        return $mappedData;
    }
}
