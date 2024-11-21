<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Percentage;
use Illuminate\Http\Request;
use App\Models\SellOutDetail;
use App\Models\ObjetiveDetail;
use App\Http\Requests\StoreObjetiveDetailRequest;
use App\Http\Requests\UpdateObjetiveDetailRequest;

class ObjetiveDetailController extends Controller
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
    public function store(StoreObjetiveDetailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ObjetiveDetail $objetiveDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ObjetiveDetail $objetiveDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateObjetiveDetailRequest $request, ObjetiveDetail $objetiveDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ObjetiveDetail $objetiveDetail)
    {
        //
    }

    public function editQuantity(Request $request)
    {
        $objetiveDetail = ObjetiveDetail::find($request->sellout_detail_id);
        $brand = Brand::where('name', $objetiveDetail->brand)->first();

        $objetive = $objetiveDetail->objetive;

        $objetiveDetail->price = $request->quantity * $brand->weighted_price;
        $objetiveDetail->quantity_with_percentage =  $request->quantity;
        $objetiveDetail->save();

        $this->calculateRealPercentage($objetive, $objetiveDetail, $brand);

        return redirect()->back()->with('success', 'Cantidad actualizada correctamente');
    }

    public function calculateRealPercentage($objetive, $objetiveDetail, $brand)
    {
        $compare_period = Carbon::parse($objetive->compare_period)->format('Y-m');
        $comparison_period = Carbon::parse($objetive->comparison_period)->format('Y-m');

        $field = $compare_period === $comparison_period ? 'quantity' : 'quantity_secondary';

        // Obtener el total del campo `field` y `quantity_with_percentage` donde el brand coincida
        $totals = $objetiveDetail->where('brand', $brand->name)->where('objetive_id', $objetive->id) // Filtrar por brand
            ->selectRaw("
        SUM($field) as total_field,
        SUM(quantity_with_percentage) as total_quantity_with_percentage
        ")->first();

        // Verifica si se obtuvieron totales
        if ($totals && $totals->total_quantity_with_percentage != 0) {
            // Cálculo de la variación porcentual
            $variationPercentage = (($totals->total_quantity_with_percentage - $totals->total_field) / $totals->total_field) * 100;
            // Formatear a dos decimales
            $variationPercentage = number_format($variationPercentage, 2);
        } else {
            // Si no se puede calcular la variación, puedes asignar un valor por defecto
            $variationPercentage = null; // O 0, o cualquier otro valor que tenga sentido en tu contexto
        }

        $percentage = Percentage::where('objetive_id', $objetive->id)
            ->where('brand', $brand->name)
            ->first();
        $percentage->real_percentage = $variationPercentage;
        $percentage->save();
    }
}
