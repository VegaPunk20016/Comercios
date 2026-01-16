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
        'num_local',
        'id_centro_comercial',
        'id_localidad',
    ];

    //Relaciones de domicilio con dif tablas
    public function withRelaciones()
    {
        $this->select('domicilio.*');
        $this->select('localidad.localidad as loc_nombre, localidad.ageb as loc_ageb');
        $this->join('localidad', 'localidad.id_localidad = domicilio.id_localidad', 'left');
        $this->select('centro_comercial.tipoCenCom as cc_tipo, centro_comercial.nom_CenCom as cc_nombre');
        $this->join('centro_comercial', 'centro_comercial.id_centro_comercial = domicilio.id_centro_comercial', 'left');
        return $this; 
    }

}