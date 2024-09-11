<?php

namespace App\Http\Controllers;

use App\Models\Objetive;
use App\Http\Requests\StoreObjetiveRequest;
use App\Http\Requests\UpdateObjetiveRequest;
use Inertia\Inertia;

class ObjetiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Objetive/Index');
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
    public function store(StoreObjetiveRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Objetive $objetive)
    {
        dd($objetive);
        Inertia::render('Objetive/Show', [
            'objetive' => $objetive
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Objetive $objetive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateObjetiveRequest $request, Objetive $objetive)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Objetive $objetive)
    {
        //
    }
}
