<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Mejora extends Model
{
    use SoftDeletes;

    protected $table = 'mejoras';

    protected $guarded = [];

    // Usa 'date' sin formato; mostraremos d/m/Y vía accessors
    protected $casts = [
        'fecha_creacion'    => 'date:d/m/Y',
        'fecha_vencimiento' => 'date:d/m/Y',
        'created_at'        => 'immutable_datetime',
        'updated_at'        => 'immutable_datetime',
        'deleted_at'        => 'immutable_datetime',
    ];

    // Mutators: normalizan a Y-m-d al guardar
    public function setFechaCreacionAttribute($value): void
    {
        $this->attributes['fecha_creacion'] = $this->toSqlDate($value);
    }

    public function setFechaVencimientoAttribute($value): void
    {
        $this->attributes['fecha_vencimiento'] = $this->toSqlDate($value);
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


    // Esto sigue aplicando solo a created_at/updated_at/deleted_at
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->setTimezone(new \DateTimeZone(config('app.timezone')))
            ->format('d-m-Y H:i');
    }

    // Relaciones
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
    public function ejePdi()
    {
        return $this->belongsTo(Catalogos\EjesPDI::class, 'eje_pdi');
    }
    public function objetivoPdi()
    {
        return $this->belongsTo(Catalogos\ObjetivosEspecificos::class, 'objetivo_pdi');
    }
    public function ambitoSiemec()
    {
        return $this->belongsTo(Catalogos\AmbitosSiemec::class, 'ambito_siemec');
    }
    public function criterioSiemec()
    {
        return $this->belongsTo(Catalogos\CriteriosSiemec::class, 'criterio_siemec');
    }
}
