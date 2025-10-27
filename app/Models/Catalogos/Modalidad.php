<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Modalidad extends Model
{
    protected $table = 'cat_modalidades_estudio';
    public $timestamps = false;

    public function nivelEstudio()
    {
        return $this->belongsTo(NivelesEstudio::class, 'id_nivel_estudio');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_modalidad_estudio');
    }
}
