<?php
// app/Exports/ReporteMejorasExport.php
namespace App\Exports;

use App\Models\Mejora;
use App\Exports\Sheets\MejorasSheet;
use App\Exports\Sheets\AccionesSheet;
use App\Exports\Sheets\ActividadesSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReporteMejorasExport implements WithMultipleSheets
{
    public function __construct(public ?array $planIds = null) {}

    public function sheets(): array
    {
        $mejorasQuery = Mejora::query()
            ->select([
                'id',
                'tipo',
                'procedencia',
                'plan_no',
                'fecha_creacion',
                'unidad_academica',
                'dependencia_administrativa',
                'recomendacion_meta',
                'fecha_vencimiento',
                'activo',
                'created_at',
                'updated_at'
            ])
            ->when($this->planIds, fn($q) => $q->whereIn('id', $this->planIds))
            ->withCount(['acciones as acciones_count', 'actividades as actividades_count'])
            ->orderBy('id', 'desc');

        $mejoras = $mejorasQuery->get();

        // 👇 garantizamos un array, aunque this->planIds sea null
        $idsForDetails = $this->planIds ?? [];

        return [
            new Sheets\MejorasSheet($mejoras),
            new Sheets\AccionesSheet($idsForDetails),
            new Sheets\ActividadesSheet($idsForDetails),
        ];
    }
}
