<?php

namespace App\Http\Controllers;

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
        $objetiveId = $objetiveDetail->objetive_id;
        $field = $request->field;
        $percentage = $this->getPercentage($objetiveId, $objetiveDetail->brand);

        $objetiveDetail->$field = $request->quantity;
        $objetiveDetail->quantity_with_percentage =  $request->quantity + ($request->quantity * $percentage / 100);
        $objetiveDetail->save();

        return redirect()->back()->with('success', 'Cantidad actualizada correctamente');
    }

    public function getPercentage($objetiveId, $brand)
    {
        $percentage = Percentage::where('objetive_id', $objetiveId)
            ->where('brand', $brand)
            ->first();
        return $percentage->percentage;
    }
}