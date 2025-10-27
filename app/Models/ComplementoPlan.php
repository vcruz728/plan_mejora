<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplementoPlan extends Model
{
    use SoftDeletes;

    protected $table = 'complemento_plan';

    protected $fillable = [
        'id_plan',
        'id_usuario',
        'id_ua',
        'indicador_clave',
        'logros',
        'impactos',
        'observaciones',
        'archivo',
        'nivel',
        'modalidad'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function mejora()
    {
        return $this->belongsTo(Mejora::class, 'id_plan');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function unidadAcademica()
    {
        return $this->belongsTo(Catalogos\UnidadesAcademicas::class, 'id_ua');
    }
}
