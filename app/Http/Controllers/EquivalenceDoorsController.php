<?php

namespace App\Http\Controllers;

use App\Models\CommercialRaw;
use App\Models\EquivalenceDoors;
use App\Http\Requests\StoreEquivalenceDoorsRequest;
use App\Http\Requests\UpdateEquivalenceDoorsRequest;
use Illuminate\Http\RedirectResponse;

class EquivalenceDoorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $equivalenceDoors = EquivalenceDoors::all();

        return inertia('EquivalenceDoors/Index', [
            'equivalenceDoors' => $equivalenceDoors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sucursales = CommercialRaw::select('sucursal')
            ->whereNotNull('sucursal')
            ->where('sucursal', '!=', '')
            ->distinct()
            ->orderBy('sucursal')
            ->pluck('sucursal');

        $clientes = CommercialRaw::select('cliente')
            ->whereNotNull('cliente')
            ->where('cliente', '!=', '')
            ->distinct()
            ->orderBy('cliente')
            ->pluck('cliente');

        return inertia('EquivalenceDoors/Create', [
            'sucursales' => $sucursales,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquivalenceDoorsRequest $request): RedirectResponse
    {
        EquivalenceDoors::create([
            'client' => $request->input('client'),
            'sucursal' => $request->input('sucursal'),
            'sucursal_objetivo_ba' => $request->input('sucursal_objetivo_ba'),
        ]);

        return redirect()->route('doors.index')->with('success', 'Puerta creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EquivalenceDoors $equivalenceDoors)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquivalenceDoors $equivalenceDoors)
    {
        $sucursales = CommercialRaw::select('sucursal')
            ->whereNotNull('sucursal')
            ->where('sucursal', '!=', '')
            ->distinct()
            ->orderBy('sucursal')
            ->pluck('sucursal');

        $clientes = CommercialRaw::select('cliente')
            ->whereNotNull('cliente')
            ->where('cliente', '!=', '')
            ->distinct()
            ->orderBy('cliente')
            ->pluck('cliente');

        return inertia('EquivalenceDoors/Edit', [
            'equivalenceDoors' => $equivalenceDoors,
            'sucursales' => $sucursales,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquivalenceDoorsRequest $request, EquivalenceDoors $equivalenceDoors): RedirectResponse
    {
        $equivalenceDoors->update([
            'client' => $request->input('client'),
            'sucursal' => $request->input('sucursal'),
            'sucursal_objetivo_ba' => $request->input('sucursal_objetivo_ba'),
        ]);

        return redirect()->route('doors.index')->with('success', 'Puerta actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquivalenceDoors $equivalenceDoors): RedirectResponse
    {
        $equivalenceDoors->delete();

        return redirect()->route('doors.index')->with('success', 'Puerta eliminada correctamente.');
    }
}
