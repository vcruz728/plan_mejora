<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class NivelesEstudio extends Model
{
    protected $table = 'cat_niveles_estudio';
    public $timestamps = false;

    public function programaEducativo()
    {
        return $this->belongsTo(ProgramasEducativos::class, 'id_programa_estudio');
    }

    public function modalidades()
    {
        return $this->hasMany(Modalidad::class, 'id_nivel_estudio');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_nivel_estudio');
    }
}
