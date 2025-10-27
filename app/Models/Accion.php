<?php
// app/Models/Accion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
    protected $table = 'acciones';


    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];
}
