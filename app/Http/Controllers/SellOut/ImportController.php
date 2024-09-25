<?php

namespace App\Http\Controllers\SellOut;

use Carbon\Carbon;
use App\Models\SellOut;
use Illuminate\Http\Request;
use App\Imports\SellOutImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        try {
            // Validar el archivo
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
                'month' => 'required|date_format:m',
                'year' => 'required|date_format:Y',
                'type' => 'required|string',
            ]);

            // Crear la fecha del periodo
            $period = Carbon::createFromFormat('Y-m', "{$request->input('year')}-{$request->input('month')}");

            $sellOut = SellOut::create([
                'period' => $period,
                'type' => $request->input('type'),
                'status' => 'active',
            ]);
            
            // Obtener el archivo subido
            $file = $request->file('file');

            // Crear la ruta de destino para la carpeta temp
            $destinationPath = storage_path('app/public/temp');

            // Generar un nombre único para el archivo
            $filename = uniqid() . '_' . $file->getClientOriginalName();

            // Mover el archivo a la carpeta temp
            $file->move($destinationPath, $filename);

            // Importar el archivo
            Excel::import(new SellOutImport($sellOut), storage_path('app/public/temp/' . $filename));

            return redirect()->back()->with('success', 'Sell Out importado exitosamente!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar el archivo: ' . $e->getMessage());
        }
    }
}
