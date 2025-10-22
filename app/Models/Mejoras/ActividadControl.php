<?php

namespace App\Models\Mejoras;

use Illuminate\Database\Eloquent\Model;

class ActividadControl extends Model
{
    protected $table = 'actividades_control';
    protected $fillable = [
        'id_plan',
        'actividad',
        'producto_resultado',
        'fecha_inicio',
        'fecha_fin',
        'responsable',
        'id_usuario'
    ];
}
