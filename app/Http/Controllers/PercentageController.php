<?php

namespace App\Http\Controllers;

use App\Models\Objetive;
use App\Models\Percentage;
use App\Http\Requests\StorePercentageRequest;
use App\Http\Requests\UpdatePercentageRequest;

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
        $objetiveId = $request->input('objetive_id');
        $brand = $request->input('brand'); 

        // Usa updateOrCreate para buscar por el objetivo y la marca
        $objetive = Objetive::find($objetiveId);

        $objetive->percentages()->updateOrCreate(
            ['brand' => $brand], // Criterio de búsqueda
            $request->all() // Datos a actualizar o crear
        );

        return redirect()->back()->with('success', 'Porcentaje modificado correctamente');
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
