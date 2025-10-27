<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class OdsPDI extends Model
{
    protected $table = 'cat_ods_pdi';
    public $timestamps = false;

    public function eje()
    {
        return $this->belongsTo(EjesPDI::class, 'id_eje');
    }

    public function objetivos()
    {
        return $this->hasMany(ObjetivosEspesificos::class, 'id_ods');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_ods_pdi');
    }
}
