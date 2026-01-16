<?php

namespace App\Models;

use CodeIgniter\Model;

class UnidadModel extends Model
{
    protected $table            = 'unidad_economica';
    protected $primaryKey       = 'id_denue';
    protected $useAutoIncrement = true;
    protected $returnType       = '\App\Entities\Unidad';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'clee',
        'nom_estab',
        'raz_social',
        'codigo_act',
        'per_ocu',
        'tipoUniEco',
        'telefono',
        'correoelec',
        'www',
        'fecha_alta',
        'latitud',
        'longitud',
        'id_domicilio',
    ];

    public function withAllData()
    {
        $this->select('unidad_economica.*');

        $this->select('actividad_scian.nombre_act as act_nombre');
        $this->join('actividad_scian', 'actividad_scian.codigo_act = unidad_economica.codigo_act', 'left');

    
        $this->select('personal_ocupado.per_ocu as per_desc'); 
        $this->join('personal_ocupado', 'personal_ocupado.per_ocu = unidad_economica.per_ocu', 'left');

        $this->select('domicilio.nom_vial, domicilio.numero_ext, domicilio.cod_postal, domicilio.nomb_asent');
        $this->join('domicilio', 'domicilio.id_domicilio = unidad_economica.id_domicilio', 'left');

        $this->select('centro_comercial.nom_CenCom as centrocomercial_nombre');
        $this->join('centro_comercial', 'centro_comercial.id_centro_comercial = domicilio.id_centro_comercial', 'left');

        $this->select('localidad.localidad as loc_nombre, localidad.id_municipio'); 
        $this->join('localidad', 'localidad.id_localidad = domicilio.id_localidad', 'left');

        $this->select('municipio.municipio as mun_nombre, municipio.id_entidad');
        $this->join('municipio', 'municipio.id_municipio = localidad.id_municipio', 'left');

        $this->select('entidad_federativa.entidad as ent_nombre, entidad_federativa.cve_ent as ent_clave');
        $this->join('entidad_federativa', 'entidad_federativa.id_entidad = municipio.id_entidad', 'left');

        return $this;
    }
}
