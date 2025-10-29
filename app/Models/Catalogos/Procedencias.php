<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Procedencias extends Model
{
    protected $table = 'cat_procedencias';

    protected $fillable = [
        'descripcion',
        'siglas',
        'tipo_mejora',
        'nivel',
        'id_des',
        'id_ua',
        'id_sede',
        'id_programa',
        'id_nivel',
        'id_modalidad',
        'responsable_user_id',
    ];
    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'procedencia');
    }

    // Opción B (si agregas FK directa al responsable):
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_user_id');
    }
}
