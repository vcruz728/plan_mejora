<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;


class Mejora extends Model
{
    use SoftDeletes;

    protected $table = 'mejoras';

    // Si prefieres control fino, define fillable; si no, deja guarded=[] tras validar en FormRequest.
    protected $guarded = [];

    protected $casts = [
        'fecha_creacion'    =>  'date:Y-m-d',
        'fecha_vencimiento' =>  'date:Y-m-d',
        'created_at'   => 'immutable_datetime',
        'updated_at'   => 'immutable_datetime',
        'deleted_at'   => 'immutable_datetime',
    ];
    protected function serializeDate(DateTimeInterface $date): string
    {

        return $date->setTimezone(new \DateTimeZone(config('app.timezone')))
            ->format('Y-m-d H:i');
    }

    protected function fechaCreacion(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            set: function ($value) {
                if (!$value) return null;
                // intenta d/m/Y, y si no, cae a parse genérico
                try {
                    return Carbon::createFromFormat('d/m/Y', $value)->toDateString();
                } catch (\Exception $e) {
                    return Carbon::parse($value)->toDateString();
                }
            }
        );
    }

    protected function fechaVencimiento(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            set: function ($value) {
                if (!$value) return null;
                try {
                    return Carbon::createFromFormat('d/m/Y', $value)->toDateString();
                } catch (\Exception $e) {
                    return Carbon::parse($value)->toDateString();
                }
            }
        );
    }

    // Hacia tablas operativas
    public function acciones()
    {
        return $this->hasMany(Accion::class, 'id_plan');
    }
    public function actividades()
    {
        return $this->hasMany(ActividadControl::class, 'id_plan');
    }
    public function complemento()
    {
        return $this->hasOne(ComplementoPlan::class, 'id_plan');
    }

    // Hacia catálogos
    public function des()
    {
        return $this->belongsTo(Catalogos\Des::class, 'id_des');
    }
    public function unidadAcademica()
    {
        return $this->belongsTo(Catalogos\UnidadesAcademicas::class, 'id_ua');
    }
    public function sede()
    {
        return $this->belongsTo(Catalogos\Sedes::class, 'id_sede');
    }
    public function programaEducativo()
    {
        return $this->belongsTo(Catalogos\ProgramasEducativos::class, 'id_programa_educativo');
    }
    public function nivelEstudio()
    {
        return $this->belongsTo(Catalogos\NivelesEstudio::class, 'id_nivel_estudio');
    }
    public function modalidad()
    {
        return $this->belongsTo(Catalogos\Modalidad::class, 'id_modalidad_estudio');
    }
    public function odsPdi()
    {
        return $this->belongsTo(Catalogos\OdsPDI::class, 'id_ods_pdi');
    }
    public function estrategia()
    {
        return $this->belongsTo(Catalogos\Estrategias::class, 'id_estrategia');
    }
    public function meta()
    {
        return $this->belongsTo(Catalogos\Metas::class, 'id_meta');
    }
}
