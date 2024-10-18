<?php

namespace App\Http\Controllers;

use App\Models\Percentage;
use Illuminate\Http\Request;
use App\Models\SellOutDetail;
use App\Models\ObjetiveDetail;
use App\Http\Requests\StoreObjetiveDetailRequest;
use App\Http\Requests\UpdateObjetiveDetailRequest;
use App\Models\Brand;

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
        $brandPrice = Brand::where('name', $objetiveDetail->brand)->pluck('weighted_price')->first();

        $objetiveDetail->price = $request->quantity * $brandPrice;
        $objetiveDetail->quantity_with_percentage =  $request->quantity;
        $objetiveDetail->save();

        return redirect()->back()->with('success', 'Cantidad actualizada correctamente');
    }

}