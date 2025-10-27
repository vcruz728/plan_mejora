<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
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

class MejorasSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStrictNullComparison
{
    public function __construct(private $mejoras) {}

    public function collection()
    {
        return $this->mejoras;
    }

    public function headings(): array
    {
        return [
            'id',
            'plan_no',
            'tipo',
            'procedencia',
            'unidad_academica',
            'fecha_creacion',
            'fecha_vencimiento',
            'activo',
            'cantidad_acciones',
            'cantidad_actividades',
            'ver_acciones',
            'ver_actividades',
            'dependencia_administrativa',
            'recomendacion_meta',
            'fecha_actualización',
        ];
    }

    public function map($m): array
    {
        $id   = $m->id;
        $accC = (int)($m->acciones_count ?? 0);
        $actC = (int)($m->actividades_count ?? 0);

        $linkAcc = $accC > 0 ? "=HYPERLINK(\"#'Acciones'!A\"&MATCH($id,Acciones!A:A,0),\"ver acciones\")" : '';
        $linkAct = $actC > 0 ? "=HYPERLINK(\"#'Actividades'!A\"&MATCH($id,Actividades!A:A,0),\"ver actividades\")" : '';

        $fc = $m->fecha_creacion    ? ExcelDate::dateTimeToExcel(Carbon::parse($m->fecha_creacion)->startOfDay())    : null;
        $fv = $m->fecha_vencimiento ? ExcelDate::dateTimeToExcel(Carbon::parse($m->fecha_vencimiento)->startOfDay()) : null;
        $fu = $m->updated_at        ? ExcelDate::dateTimeToExcel(Carbon::parse($m->updated_at))                       : null;

        return [
            $id,
            $m->plan_no,
            $m->tipo,
            $m->procedencia,
            $m->unidad_academica,
            $fc, // F
            $fv, // G
            $m->activo,
            $accC,
            $actC,
            $linkAcc,
            $linkAct,
            $m->dependencia_administrativa,
            $m->recomendacion_meta,
            $fu, // O
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => 'dd/mm/yyyy',          // fecha_creacion
            'G' => 'dd/mm/yyyy',          // fecha_vencimiento
            'O' => 'dd/mm/yyyy hh:mm',    // updated_at
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $sh = $e->sheet->getDelegate();
                $sh->freezePane('A2');
                $sh->setAutoFilter($sh->calculateWorksheetDimension());
                foreach (['M', 'N'] as $col) {
                    $sh->getStyle("{$col}2:{$col}{$sh->getHighestRow()}")->getAlignment()->setWrapText(true);
                }
                // doble seguro de formato
                $last = $sh->getHighestRow();
                $sh->getStyle("F2:F{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sh->getStyle("G2:G{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                $sh->getStyle("O2:O{$last}")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm');
            },
        ];
    }

    public function title(): string
    {
        return 'Mejoras';
    }
}
