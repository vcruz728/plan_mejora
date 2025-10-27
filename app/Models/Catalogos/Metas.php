<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Metas extends Model
{
    protected $table = 'cat_metas';
    public $timestamps = false;

    public function estrategia()
    {
        return $this->belongsTo(Estrategias::class, 'id_estrategia');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_meta');
    }
}
