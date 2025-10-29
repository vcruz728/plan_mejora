<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Estrategias extends Model
{
    protected $table = 'cat_estrategias';
    public $timestamps = false;

    public function objetivo()
    {
        return $this->belongsTo(ObjetivosEspecificos::class, 'id_objetivo');
    }

    public function metas()
    {
        return $this->hasMany(Metas::class, 'id_estrategia');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_estrategia');
    }
}
