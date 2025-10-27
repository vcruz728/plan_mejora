<?php
// app/Models/Mejora.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mejora extends Model
{
    protected $table = 'mejoras';
    protected $casts = [
        'fecha_creacion'    => 'date',
        'fecha_vencimiento' => 'date',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function acciones()
    {
        return $this->hasMany(Accion::class, 'id_plan', 'id')->whereNull('deleted_at');
    }

    public function actividades()
    {
        return $this->hasMany(ActividadControl::class, 'id_plan', 'id');
    }

    public function complemento()
    {
        return $this->hasOne(ComplementoPlan::class, 'id_plan', 'id');
    }
}
