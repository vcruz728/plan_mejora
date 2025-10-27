<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class ProgramasEducativos extends Model
{
    protected $table = 'cat_programas_educativos_dos';
    public $timestamps = false;

    public function sede()
    {
        return $this->belongsTo(Sedes::class, 'id_sede');
    }

    public function niveles()
    {
        return $this->hasMany(NivelesEstudio::class, 'id_programa_estudio');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_programa_educativo');
    }
}
