<?php

namespace App\Exports\Sheets;

use App\Models\ActividadControl;
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
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ActividadesSheet implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStrictNullComparison
{
    public function __construct(private array $planIds = []) {}

    public function query()
    {
        $q = ActividadControl::query()
            ->select([
                'id_plan',
                'id',
                'actividad',
                'producto_resultado',
                'responsable',
                'fecha_inicio',
                'fecha_fin',
                'created_at',
                'updated_at'
            ]);

        if (count($this->planIds) > 0) {
            if (count($this->planIds) <= 2000) {
                $q->whereIn('id_plan', $this->planIds);
            } else {
                $csv = implode(',', array_map('intval', $this->planIds));
                $q->whereRaw("id_plan IN (SELECT TRY_CAST([value] AS int) FROM STRING_SPLIT(?, ','))", [$csv]);
            }
        }

        return $q->orderBy('id_plan')->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'id_plan',
            '#actividad',
            'actividad',
            'producto_resultado',
            'responsable',
            'fecha_inicio',
            'fecha_fin',
            'fecha_creación',
            'fecha_actualización',
        ];
    }

    public function map($a): array
    {
        $fi = $a->fecha_inicio ? ExcelDate::dateTimeToExcel(Carbon::parse($a->fecha_inicio)->startOfDay()) : null;
        $ff = $a->fecha_fin    ? ExcelDate::dateTimeToExcel(Carbon::parse($a->fecha_fin)->startOfDay())    : null;
        $fc = $a->created_at   ? ExcelDate::dateTimeToExcel(Carbon::parse($a->created_at)) : null;
        $fu = $a->updated_at   ? ExcelDate::dateTimeToExcel(Carbon::parse($a->updated_at)) : null;

        return [
            $a->id_plan,
            $a->id,
            $a->actividad,
            $a->producto_resultado,
            $a->responsable,
            $fi, // F
            $ff, // G
            $fc, // H
            $fu, // I
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => 'dd/mm/yyyy',          // fecha_inicio
            'G' => 'dd/mm/yyyy',          // fecha_fin
            'H' => 'dd/mm/yyyy hh:mm',    // created_at
            'I' => 'dd/mm/yyyy hh:mm',    // updated_at
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $sh = $e->sheet->getDelegate();
                $sh->freezePane('A2');
                $sh->setAutoFilter($sh->calculateWorksheetDimension());
                foreach (['C', 'D'] as $col) {
                    $sh->getStyle("{$col}2:{$col}{$sh->getHighestRow()}")->getAlignment()->setWrapText(true);
                }
                // doble seguro de formato
                $last = $sh->getHighestRow();
                $sh->getStyle("F2:F{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sh->getStyle("G2:G{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sh->getStyle("H2:H{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm');
                $sh->getStyle("I2:I{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm');
            },
        ];
    }

    public function title(): string
    {
        return 'Actividades';
    }
}
