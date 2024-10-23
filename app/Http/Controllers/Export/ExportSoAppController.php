<?php

namespace App\Http\Controllers\Export;

use App\Exports\PreSoAppExport;
use App\Exports\SoAppExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportSoAppController extends Controller
{
    public function export($id)
    {
        return Excel::download(new SoAppExport($id), 'objetivos_so_app.xlsx');
    }

    public function exportBorrador($id)
    {
        return Excel::download(new PreSoAppExport($id), 'borrador_so_app.xlsx');
    }
}
