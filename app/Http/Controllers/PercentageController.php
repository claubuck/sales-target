<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Objetive;
use App\Models\Percentage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StorePercentageRequest;
use App\Http\Requests\UpdatePercentageRequest;
use App\Models\Brand;

class PercentageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePercentageRequest $request)
    {
        try {
            $objetiveId = $request->input('objetive_id');
            $brand = $request->input('brand');
            $percentage = $request->input('percentage') / 100; // Dividimos para aplicar el porcentaje

            // Encuentra el objetivo
            $objetive = Objetive::find($objetiveId);

            $objetive->percentages()->updateOrCreate(
                ['brand' => $brand], // Criterio de búsqueda
                array_merge($request->all(), [
                    'real_percentage' => $request->input('percentage'), // Agregar el valor manual aquí
                ])
            );


            $column = $this->getColumn($objetive);

            // Aplica el porcentaje a todos los registros con la misma marca
            $objetive->objetiveDetails()
                ->where('brand', $brand)
                ->each(function ($detail) use ($percentage, $column) {
                    $detail->quantity_with_percentage = $detail->$column * (1 + $percentage);
                    $detail->save();
                });

            $this->updatePrices($objetive, $brand, $column);

            return redirect()->back()->with('success', 'Porcentaje creado correctamente');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el porcentaje');
        }
    }

    /**
     * Obtiene la columna a la que se le aplicará el porcentaje
     */

    public function getColumn(Objetive $objetive)
    {
            // Convierte los periodos en instancias de Carbon y compara solo el mes y el año
            $comparisonPeriod = Carbon::parse($objetive->comparison_period)->format('Y-m');
            $comparePeriod = Carbon::parse($objetive->compare_period)->format('Y-m');

            // Si el mes y el año son iguales, retorna 'quantity', de lo contrario 'quantity_secondary'
            return $comparisonPeriod === $comparePeriod ? 'quantity' : 'quantity_secondary';

    }

    /**
     * Actualiza los precios de los detalles del objetivo
     */

    public function updatePrices(Objetive $objetive, $brandName, $column)
    {
        $brand = Brand::where('name', $brandName)->first();
        $objetive->objetiveDetails()
            ->where('brand', $brandName)
            ->each(function ($detail) use ($brand, $column) {
                $detail->price = $detail->$column * $brand->weighted_price;
                $detail->save();
            });
    }

    /**
     * Display the specified resource.
     */
    public function show(Percentage $percentage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Percentage $percentage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePercentageRequest $request, Percentage $percentage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Percentage $percentage)
    {
        //
    }
}
