<?php

namespace App\Models;

use CodeIgniter\Model;

class DomicilioModel extends Model
{
    protected $table            = 'domicilio';
    protected $primaryKey       = 'id_domicilio';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Domicilio';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tipo_vial',
        'nom_vial',
        'numero_ext',
        'letra_ext',
        'numero_int',
        'letra_int',
        'edificio',
        'tipo_asent',
        'nomb_asent',
        'cod_postal',
        'ageb',
        'manzana',
        'num_local',
        'id_centro_comercial',
        'id_localidad',
    ];

    //Relaciones de domicilio con dif tablas
    public function withRelaciones()
    {
        $this->select('domicilio.*');
        $this->select('localidad.localidad as loc_nombre');
        $this->join('localidad', 'localidad.id_localidad = domicilio.id_localidad', 'left');
        $this->select('centro_comercial.tipoCenCom as cc_tipo, centro_comercial.nom_CenCom as cc_nombre');
        $this->join('centro_comercial', 'centro_comercial.id_centro_comercial = domicilio.id_centro_comercial', 'left');
        return $this;
    }
    public function aplicarFiltros($filtros)
    {
        if (!empty($filtros['ageb'])) {
            $this->where('domicilio.ageb', $filtros['ageb']);
        }
        if (!empty($filtros['manzana'])) {
            $this->where('domicilio.manzana', $filtros['manzana']);
        }
        if (!empty($filtros['cp'])) {
            $this->where('domicilio.cod_postal', $filtros['cp']);
        }
        if (!empty($filtros['asentamiento'])) {
            $this->like('domicilio.nomb_asent', $filtros['asentamiento']);
        }
        return $this;
    }

}
