<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class Accion extends Model
{
    use SoftDeletes;

    protected $table = 'acciones';

    protected $fillable = [
        'id_plan',
        'accion',
        'responsable',
        'fecha_inicio',
        'fecha_fin'
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_fin'    => 'date:Y-m-d',
        'created_at'   => 'immutable_datetime',
        'updated_at'   => 'immutable_datetime',
        'deleted_at'   => 'immutable_datetime',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {

        return $date->setTimezone(new \DateTimeZone(config('app.timezone')))
            ->format('Y-m-d H:i');
    }

    public function mejora()
    {
        return $this->belongsTo(Mejora::class, 'id_plan');
    }
}
