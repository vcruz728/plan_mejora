<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class ObjetivosEspecificos extends Model
{
    protected $table = 'cat_objetivos_especifico'; // (sí, con ese nombre en BD)
    public $timestamps = false;

    public function ods()
    {
        return $this->belongsTo(OdsPDI::class, 'id_ods');
    }

    public function estrategias()
    {
        return $this->hasMany(Estrategias::class, 'id_objetivo');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'objetivo_pdi');
    }
}
