<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SellOut;
use Illuminate\Http\Request;
use App\Imports\SellOutImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreSellOutRequest;
use App\Http\Requests\UpdateSellOutRequest;

class SellOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellOuts = SellOut::where('type', 'sellout')->get();

        return Inertia::render('SellOut/Index', [
            'sellouts' => $sellOuts,
        ]);
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
    public function store(StoreSellOutRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SellOut $sellOut)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SellOut $sellOut)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSellOutRequest $request, SellOut $sellOut)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sellOut = SellOut::find($id);
        $sellOut->delete();

        return redirect()->back()->with('success', 'Registro eliminado correctamente');
    }
}
