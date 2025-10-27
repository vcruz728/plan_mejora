<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Procedencias extends Model
{
    protected $table = 'cat_procedencias';
    public $timestamps = false;

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'procedencia');
    }
}
