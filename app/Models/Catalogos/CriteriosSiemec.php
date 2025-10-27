<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class CriteriosSiemec extends Model
{
    protected $table = 'cat_criterios_siemec';
    public $timestamps = false;

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'criterio_siemec');
    }
}
