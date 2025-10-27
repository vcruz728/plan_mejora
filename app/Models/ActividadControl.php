<?php
// app/Models/ActividadControl.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadControl extends Model
{
    protected $table = 'actividades_control';
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];
}
