<?php

namespace App\Http\Controllers;

use App\Models\ClientEquivalence;
use App\Models\CommercialRaw;
use App\Http\Requests\StoreClientEquivalenceRequest;
use App\Http\Requests\UpdateClientEquivalenceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientEquivalenceController extends Controller
{
    /**
     * Display a listing of client equivalences.
     */
    public function index()
    {
        $clientEquivalences = ClientEquivalence::orderBy('cliente_display')->get();

        return inertia('Clients/Index', [
            'clientEquivalences' => $clientEquivalences,
        ]);
    }

    /**
     * Show the form for creating a new client equivalence.
     */
    public function create()
    {
        $clientes = CommercialRaw::select('cliente')
            ->whereNotNull('cliente')
            ->where('cliente', '!=', '')
            ->distinct()
            ->orderBy('cliente')
            ->pluck('cliente');

        return inertia('Clients/Create', [
            'clientes' => $clientes,
        ]);
    }

    /**
     * Store a newly created client equivalence.
     */
    public function store(StoreClientEquivalenceRequest $request): RedirectResponse
    {
        ClientEquivalence::create([
            'cliente_comercial' => $request->input('cliente_comercial'),
            'cliente_display' => $request->input('cliente_display'),
        ]);

        return redirect()->route('clients.index')->with('success', 'Equivalencia de cliente creada correctamente.');
    }

    /**
     * Show the form for editing the specified client equivalence.
     */
    public function edit(ClientEquivalence $client)
    {
        $clientes = CommercialRaw::select('cliente')
            ->whereNotNull('cliente')
            ->where('cliente', '!=', '')
            ->distinct()
            ->orderBy('cliente')
            ->pluck('cliente');

        return inertia('Clients/Edit', [
            'clientEquivalence' => $client,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Update the specified client equivalence.
     */
    public function update(UpdateClientEquivalenceRequest $request, ClientEquivalence $client): RedirectResponse
    {
        $client->update([
            'cliente_comercial' => $request->input('cliente_comercial'),
            'cliente_display' => $request->input('cliente_display'),
        ]);

        return redirect()->route('clients.index')->with('success', 'Equivalencia de cliente actualizada correctamente.');
    }

    /**
     * Remove the specified client equivalence.
     */
    public function destroy(ClientEquivalence $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Equivalencia de cliente eliminada correctamente.');
    }
}
