<?php
// app/Http/Controllers/ReporteController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReporteMejorasExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function exportMejoras(Request $request)
    {
        // Opcional: filtrar por ids = ?ids=1,2,3
        $ids = $request->filled('ids')
            ? array_map('intval', explode(',', $request->query('ids')))
            : null;

        $fname = 'reporte_mejoras_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new ReporteMejorasExport($ids), $fname);
    }
}
