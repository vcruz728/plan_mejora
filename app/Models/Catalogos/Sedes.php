<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Sedes extends Model
{
    protected $table = 'cat_sedes';
    public $timestamps = false;

    public function unidadAcademica()
    {
        return $this->belongsTo(UnidadesAcademicas::class, 'id_ua');
    }

    public function programasEducativos()
    {
        return $this->hasMany(ProgramasEducativos::class, 'id_sede');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'id_sede');
    }
}
