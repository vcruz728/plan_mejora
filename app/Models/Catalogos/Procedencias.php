<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;


use App\Models\Catalogos\Des;
use App\Models\Catalogos\UnidadAcademica;
use App\Models\Catalogos\Sede;
use App\Models\Catalogos\Programa;
use App\Models\Catalogos\Nivel;
use App\Models\Catalogos\Modalidad;
use App\Models\ComplementoPlan;
use App\Models\User;

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

    protected $appends = ['ruta'];

    public function des()
    {
        return $this->belongsTo(Des::class, 'id_des');
    }
    public function unidadAcademica()
    {
        return $this->belongsTo(UnidadesAcademicas::class, 'id_ua');
    }
    public function sede()
    {
        return $this->belongsTo(Sedes::class, 'id_sede');
    }
    public function programa()
    {
        return $this->belongsTo(ProgramasEducativos::class, 'id_programa');
    }
    public function nivel()
    {
        return $this->belongsTo(NivelesEstudio::class, 'id_nivel');
    }
    public function modalidad()
    {
        return $this->belongsTo(Modalidad::class, 'id_modalidad');
    }

    public function complementosCapturados()
    {
        return $this->hasMany(ComplementoPlan::class, 'id_usuario');
    }

    public function mejoras()
    {
        return $this->hasMany(Mejora::class, 'procedencia');
    }

    // Opción B (si agregas FK directa al responsable):
    public function responsable()
    {
        return $this->hasMany(User::class, 'responsable_user_id');
    }

    public function getRutaAttribute(): string
    {
        $p = [];
        if ($this->des?->nombre)      $p[] = $this->des->nombre;
        if ($this->ua?->nombre)       $p[] = $this->ua->nombre;
        if ($this->sede?->nombre)     $p[] = $this->sede->nombre;
        if ($this->programa?->nombre) $p[] = $this->programa->nombre;
        if ($this->nivelEst?->nombre) $p[] = $this->nivelEst->nombre;
        if ($this->modalidad?->nombre) $p[] = $this->modalidad->nombre;
        return implode(' › ', $p);
    }
}
