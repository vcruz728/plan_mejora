<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class EjesPDI extends Model
{
    protected $table = 'cat_ejes_pdi';
    public $timestamps = false;

    public function ods()
    {
        return $this->hasMany(OdsPDI::class, 'id_eje');
    }

    public function mejoras()
    {
        return $this->hasMany(\App\Models\Mejora::class, 'eje_pdi');
    }
}
