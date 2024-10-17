<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\SellOut;
use App\Models\Objetive;
use Illuminate\Http\Request;
use App\Models\SellOutDetail;
use App\Http\Requests\StoreObjetiveRequest;
use App\Http\Requests\UpdateObjetiveRequest;
use App\Services\NormalizeSellOutService;

class ObjetiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $brands = Brand::all();
        $filter = 'CAROLINA HERRERA';

        if ($request->has('filter')) {
            $filter = $request->input('filter');
            $sellOut = SellOutDetail::where('brand', $request->input('filter'))->get();
        }

        return Inertia::render('Objetive/Index', [
            'brands' => $brands,
            'sellOut' => $sellOut ?? null,
            'filter' => $filter,
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
    public function store(StoreObjetiveRequest $request, NormalizeSellOutService $sellOutService)
    {
        $this->validatePeriod($request);

        // Crear un nuevo registro en la base de datos
        $objetive = Objetive::create([
            'period' => Carbon::parse($request->input('period'))->format('Y-m-d'),
            'compare_period' => Carbon::parse($request->input('compare_period'))->format('Y-m-d'),
            'compare_period_secondary' => Carbon::parse($request->input('compare_period_secondary'))->format('Y-m-d'),
            'status' => 'active',
        ]);

        // Normalizar los datos de Sell Out
        $sellOutService->normalizeSellOut(
            Carbon::parse($request->input('compare_period'))->format('Y-m-d'),
            Carbon::parse($request->input('compare_period_secondary'))->format('Y-m-d'),
            $objetive
        );

        return redirect()->back()->with('success', 'Periodo de objetivo creado correctamente');
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
        $objetive->delete();

        return redirect()->back()->with('success', 'Objetivo eliminado correctamente');
    }

    public function validatePeriod(Request $request)
    {
        $errors = [];

        // Verificar si el period ya existe
        $existsPeriod = Objetive::whereYear('period', Carbon::parse($request->input('period'))->year)
            ->whereMonth('period', Carbon::parse($request->input('period'))->month)
            ->exists();

        if ($existsPeriod) {
            $errors['period'] = 'Ya hay un objetivo con este perido.';
        }

        // Verificar si el compare_period ya existe
        $existsComparePeriod = SellOut::whereYear('period', Carbon::parse($request->input('compare_period'))->year)
            ->whereMonth('period', Carbon::parse($request->input('compare_period'))->month)
            ->exists();

        if (!$existsComparePeriod) {
            $errors['compare_period'] = 'Para el periodo seleccionado no hay un Sell Out cargado.';
        }

        // Verificar si el compare_period_secondary ya existe
        $existsComparePeriodSecondary = SellOut::whereYear('period', Carbon::parse($request->input('compare_period_secondary'))->year)
            ->whereMonth('period', Carbon::parse($request->input('compare_period_secondary'))->month)
            ->exists();

        if (!$existsComparePeriodSecondary) {
            $errors['compare_period_secondary'] = 'Para el periodo seleccionado no hay un Sell Out cargado.';
        }

        // Si hay errores, redirigir con todos los errores acumulados
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }
    }
}
