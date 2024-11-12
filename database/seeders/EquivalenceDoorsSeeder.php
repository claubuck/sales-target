<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EquivalenceDoorsImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EquivalenceDoorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpia la tabla antes de cargar nuevos datos
        DB::table('equivalence_doors')->truncate();
        
        // Especifica la ruta del archivo en el storage
        $path = storage_path('app/exels/equivalencias_puertas_objetivos.xlsx'); // Asegúrate de usar la extensión correcta

        // Importa los datos
        Excel::import(new EquivalenceDoorsImport, $path);
    }
}
