<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Objetive;
use App\Models\Percentage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StorePercentageRequest;
use App\Http\Requests\UpdatePercentageRequest;
use App\Services\CalculateRealPercentageService;

class PercentageController extends Controller
{
    protected $calculateRealPercentageService;

    public function __construct(CalculateRealPercentageService $calculateRealPercentageService)
    {
        $this->calculateRealPercentageService = $calculateRealPercentageService;
    }

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
                ->each(function ($detail) use ($percentage, $column, $brand) {
                    // Aplica el porcentaje y valida el mínimo en un solo paso
                    $calculatedQuantity = $detail->$column * (1 + $percentage);
                    $detail->quantity_with_percentage = $this->calculateQuantityWithMinimum($brand, $calculatedQuantity);

                    $detail->save();
                });

            $this->updatePrices($objetive, $brand);

            $objetiveDetail = $objetive->objetiveDetails()->first();

            $brand = Brand::where('name', $brand)->first();

            $this->calculateRealPercentageService->calculate($objetive, $objetiveDetail, $brand);

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

    public function updatePrices(Objetive $objetive, $brandName)
    {
        $brand = Brand::where('name', $brandName)->first();
        $objetive->objetiveDetails()
            ->where('brand', $brandName)
            ->each(function ($detail) use ($brand) {
                $detail->price = $detail->quantity_with_percentage * $brand->weighted_price;
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

    private function calculateQuantityWithMinimum($brand, $quantity)
    {
        // Si la cantidad es negativa, retorna 0
        if ($quantity < 0) {
            return 0;
        }

        // Define los mínimos de unidades solo para ciertas marcas
        $minimumQuantities = [
            'AGATHA RUIZ DE LA PR' => 5,
            'AGATHA RUIZ DE LA PRADA' => 5,
            'JEAN PAUL GAULTIER' => 15,
            'NINA RICCI' => 10,
            // Agrega más marcas con mínimos aquí si es necesario
        ];

        // Obtén el nombre estándar de la marca
        $standardBrand = $brand;

        // Si la marca tiene un mínimo definido, verifica el valor
        if (isset($minimumQuantities[$standardBrand])) {
            $minimum = $minimumQuantities[$standardBrand];
            // Devuelve el mayor valor entre el mínimo y el valor actual
            return max($quantity, $minimum);
        }

        // Si no tiene un mínimo definido, devuelve el valor original
        return $quantity;
    }
}
