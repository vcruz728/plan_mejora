<?php
// app/Exports/Sheets/AccionesSheet.php
namespace App\Exports\Sheets;

use App\Models\Accion;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromQuery,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStrictNullComparison
};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate; // 👈 serial de Excel

class AccionesSheet implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStrictNullComparison // 👈 nulls reales
{
    public function __construct(private array $planIds = []) {}

    public function query()
    {
        $q = Accion::query()
            ->select([
                'id_plan',
                'id',
                'unidad_academica',
                'accion',
                'producto_resultado',
                'responsable',
                'fecha_inicio',
                'fecha_fin',
                'evidencia',
                'created_at',
                'updated_at'
            ])
            ->whereNull('deleted_at');

        if (count($this->planIds) > 0) {
            if (count($this->planIds) <= 2000) {
                $q->whereIn('id_plan', $this->planIds);
            } else {
                $csv = implode(',', array_map('intval', $this->planIds));
                $q->whereRaw(
                    "id_plan IN (SELECT TRY_CAST([value] AS int) FROM STRING_SPLIT(?, ','))",
                    [$csv]
                );
            }
        }

        return $q->orderBy('id_plan')->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'id_plan',
            '#accion',
            'unidad_academica',
            'accion',
            'producto_resultado',
            'responsable',
            'fecha_inicio',
            'fecha_fin',
            'evidencia',
            'fecha_creación',
            'fecha_actualización',
        ];
    }

    public function map($a): array
    {
        // Convertimos a serial de Excel (número), no string
        $fi = $a->fecha_inicio ? ExcelDate::dateTimeToExcel(Carbon::parse($a->fecha_inicio)->startOfDay()) : null;
        $ff = $a->fecha_fin    ? ExcelDate::dateTimeToExcel(Carbon::parse($a->fecha_fin)->startOfDay())    : null;

        $fc = $a->created_at ? ExcelDate::dateTimeToExcel(Carbon::parse($a->created_at)) : null;
        $fu = $a->updated_at ? ExcelDate::dateTimeToExcel(Carbon::parse($a->updated_at)) : null;

        return [
            $a->id_plan,
            $a->id,
            $a->unidad_academica,
            $a->accion,
            $a->producto_resultado,
            $a->responsable,
            $fi, // G
            $ff, // H
            $a->evidencia,
            $fc, // J
            $fu, // K
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => 'dd/mm/yyyy',          // fecha_inicio
            'H' => 'dd/mm/yyyy',          // fecha_fin
            'J' => 'dd/mm/yyyy hh:mm',    // created_at
            'K' => 'dd/mm/yyyy hh:mm',    // updated_at
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $sh = $e->sheet->getDelegate();
                $sh->freezePane('A2');
                $sh->setAutoFilter($sh->calculateWorksheetDimension());

                foreach (['D', 'E'] as $col) {
                    $sh->getStyle("{$col}2:{$col}{$sh->getHighestRow()}")
                        ->getAlignment()->setWrapText(true);
                }

                // Doble seguro (opcional): re-aplicar formato en rango
                $last = $sh->getHighestRow();
                $sh->getStyle("G2:G{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sh->getStyle("H2:H{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sh->getStyle("J2:J{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm');
                $sh->getStyle("K2:K{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm');
            },
        ];
    }

    public function title(): string
    {
        return 'Acciones';
    }
}
