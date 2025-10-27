<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class UnidadesAcademicas extends Model
{
    protected $table = 'cat_unidades_academicas';
    public $timestamps = false;

    public function des()
    {
        return $this->belongsTo(Des::class, 'id_des');
    }

    public function sedes()
    {
        return $this->hasMany(Sedes::class, 'id_ua');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_ua');
    }
}
