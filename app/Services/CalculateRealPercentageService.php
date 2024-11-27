<?php

namespace App\Services;

use App\Models\Percentage;
use Carbon\Carbon;

class CalculateRealPercentageService
{
    /**
     * Calcula el porcentaje real basado en los detalles del objetivo.
     *
     * @param  mixed  $objetive
     * @param  mixed  $objetiveDetail
     * @param  mixed  $brand
     * @return void
     */
    public function calculate($objetive, $objetiveDetail, $brand)
    {
        $compare_period = Carbon::parse($objetive->compare_period)->format('Y-m');
        $comparison_period = Carbon::parse($objetive->comparison_period)->format('Y-m');

        // Determinar el campo a utilizar
        $field = $compare_period === $comparison_period ? 'quantity' : 'quantity_secondary';

        // Obtener los totales
        $totals = $objetiveDetail->where('brand', $brand->name)
            ->where('objetive_id', $objetive->id)
            ->selectRaw("
                SUM($field) as total_field,
                SUM(quantity_with_percentage) as total_quantity_with_percentage
            ")
            ->first();

        $variationPercentage = null;

        // Calcular la variación porcentual si los totales son válidos
        if ($totals && $totals->total_quantity_with_percentage != 0) {
            $variationPercentage = (($totals->total_quantity_with_percentage - $totals->total_field) / $totals->total_field) * 100;
            $variationPercentage = number_format($variationPercentage, 2);
        }

        // Actualizar el porcentaje en la tabla Percentage
        $percentage = Percentage::where('objetive_id', $objetive->id)
            ->where('brand', $brand->name)
            ->first();

        if ($percentage) {
            $percentage->real_percentage = $variationPercentage;
            $percentage->save();
        }
    }
}
