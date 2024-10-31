<?php

namespace App\Http\Controllers\SellOut;

use Carbon\Carbon;
use App\Models\SellOut;
use Illuminate\Http\Request;
use App\Imports\SellOutImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SellOutCommercialImport;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            // Validar el archivo
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
                'month' => 'required|date_format:m',
                'year' => 'required|date_format:Y',
                'type' => 'required|string',
            ]);

            // Crear la fecha del periodo
            $period = Carbon::createFromFormat('Y-m-d', "{$request->input('year')}-{$request->input('month')}-01");


            // Guardar el sell out
            $sellOut = $this->saveSellOut($request, $period);

            // Obtener el archivo subido
            $fileName = $this->uploadFile($request->file('file'));

            // Importar el archivo
            Excel::import(new SellOutImport($sellOut), storage_path('app/public/temp/' . $fileName));

            DB::commit();

            return redirect()->back()->with('success', 'Sell Out importado exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al importar el archivo: ' . $e->getMessage());
        }
    }

    public function importSellOutCommercial(Request $request)
    {
        try {
            DB::beginTransaction();
            // Validar el archivo
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
                'month' => 'required|integer|between:0,12',
                'year' => 'required|date_format:Y',
                'type' => 'required|string',
            ]);

            // Crear la fecha del periodo
            $year = $request->input('year');
            $month = $request->input('month');

            $period = Carbon::createFromFormat('Y-m-d', "{$year}-{$month}-01");

            // Guardar el sell out
            $sellOut = $this->saveSellOut($request, $period);

            // Obtener el archivo subido
            $fileName = $this->uploadFile($request->file('file'));

            // Importar el archivo
            Excel::import(new SellOutCommercialImport($sellOut), storage_path('app/public/temp/' . $fileName));

            DB::commit();

            return redirect()->back()->with('success', 'Sell Out importado exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al importar el archivo: ' . $e->getMessage());
        }
    }

    private function uploadFile($file)
    {
        // Crear la ruta de destino para la carpeta temp
        $destinationPath = storage_path('app/public/temp');

        // Generar un nombre único para el archivo
        $fileName = uniqid() . '_' . $file->getClientOriginalName();

        // Mover el archivo a la carpeta temp
        $file->move($destinationPath, $fileName);

        return $fileName;
    }

    private function saveSellOut($request, $period)
    {
        return SellOut::create([
            'period' => $period,
            'type' => $request->input('type'),
            'status' => 'active',
        ]);
    }
}
