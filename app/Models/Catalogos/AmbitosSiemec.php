<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class AmbitosSiemec extends Model
{
    protected $table = 'cat_ambitos_siemec';
    public $timestamps = false;

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'ambito_siemec');
    }
}
