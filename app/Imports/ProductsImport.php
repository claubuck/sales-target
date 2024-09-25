<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Obtener el valor de 'ean'
        $ean = $row['ean'] ?? null;

        // Verificar si 'ean' es un número
        if (!is_numeric($ean)) {
            Log::warning('EAN no es un número válido, omitiendo fila.', $row);
            return null; // O simplemente no guardar nada si 'ean' no es válido
        }

        try {
            // Crear el producto solo si 'ean' es válido
            Product::create([
                'ean' => $ean,
                'division' => $row['division'] ?? null,
                'brand' => $row['brand'] ?? null,
                'universe' => $row['universe'] ?? null,
                'product_line' => $row['product_line'] ?? null,
                'cu_code' => $row['cu_code'] ?? null,
                'material' => $row['material'] ?? null,
                'presentacion' => $row['presentacion'] ?? null,
                'formato' => $row['formato'] ?? null,
                'category' => $row['category'] ?? null,
                'grupo_imputacion' => $row['grupo_de_imputacion_para_material'] ?? null,
                'valuation_class' => $row['valuation_class'] ?? null,
                'femenino_masculino' => $row['femeninomasculino'] ?? null,
                'xl' => $row['xl'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al importar producto: ' . $e->getMessage());
        }
    }
}
