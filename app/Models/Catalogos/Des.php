<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Des extends Model
{
    protected $table = 'cat_des';
    public $timestamps = false;

    // Árbol académico
    public function unidadesAcademicas()
    {
        return $this->hasMany(UnidadesAcademicas::class, 'id_des');
    }

    // Inverso desde Mejora
    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_des');
    }
}
