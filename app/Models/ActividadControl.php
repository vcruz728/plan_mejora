<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;
use Carbon\Carbon;

class ActividadControl extends Model
{
    use SoftDeletes;

    protected $table = 'actividades_control';

    protected $fillable = [
        'id_plan',
        'actividad',
        'producto_resultado',
        'responsable',
        'fecha_inicio',
        'fecha_fin',
        'id_usuario',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:d/m/Y',
        'fecha_fin'    => 'date:d/m/Y',
        'created_at'   => 'immutable_datetime',
        'updated_at'   => 'immutable_datetime',
        'deleted_at'   => 'immutable_datetime',
    ];

    // === Mutators: guardan SIEMPRE como Y-m-d en la BD ===
    public function setFechaInicioAttribute($value): void
    {
        $this->attributes['fecha_inicio'] = $this->toSqlDate($value);
    }

    public function setFechaFinAttribute($value): void
    {
        $this->attributes['fecha_fin'] = $this->toSqlDate($value);
    }

    protected function toSqlDate($value): ?string
    {
        if (empty($value)) return null;

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString(); // Y-m-d
        }

        // d/m/Y
        if (preg_match('#^\d{1,2}/\d{1,2}/\d{4}$#', (string) $value)) {
            return Carbon::createFromFormat('d/m/Y', $value)->toDateString();
        }

        // Fallback (ISO, etc.)
        return Carbon::parse($value)->toDateString();
    }

    // ¡OJO! Eliminamos $appends y los accessors Attribute::make duplicados

    // Esto sigue aplicando solo a created_at/updated_at/deleted_at
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->setTimezone(new \DateTimeZone(config('app.timezone')))
            ->format('d-m-Y H:i');
    }

    public function mejora()
    {
        return $this->belongsTo(Mejora::class, 'id_plan');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
