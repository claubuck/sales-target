<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\CommercialRaw;
use App\Models\Objetive;
use Illuminate\Http\Request;
use App\Models\SellOutDetail;
use Illuminate\Support\Facades\Log;
use App\Services\NormalizeSellOutService;
use App\Http\Requests\StoreObjetiveRequest;
use App\Http\Requests\UpdateObjetiveRequest;

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
        try {
            $errors = $this->validatePeriod($request);

            // Si hay errores, redirigir con todos los errores acumulados
            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors)->withInput();
            }

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
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el periodo de objetivo');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Objetive $objetive)
    {
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

        // Verificar si hay datos en commercial_raw para compare_period
        $dateCompare = Carbon::parse($request->input('compare_period'));
        $mesAbbr = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'][$dateCompare->month - 1] ?? null;
        $existsComparePeriod = $mesAbbr && CommercialRaw::where('ano', $dateCompare->year)
            ->whereRaw('LOWER(mes) = ?', [strtolower($mesAbbr)])
            ->exists();

        if (!$existsComparePeriod) {
            $errors['compare_period'] = 'Para el periodo seleccionado no hay datos en crudo comercial. Ejecute "commercial:sync-from-drive" si es necesario.';
        }

        // Verificar si hay datos en commercial_raw para compare_period_secondary
        $dateCompareSecondary = Carbon::parse($request->input('compare_period_secondary'));
        $mesAbbrSecondary = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'][$dateCompareSecondary->month - 1] ?? null;
        $existsComparePeriodSecondary = $mesAbbrSecondary && CommercialRaw::where('ano', $dateCompareSecondary->year)
            ->whereRaw('LOWER(mes) = ?', [strtolower($mesAbbrSecondary)])
            ->exists();

        if (!$existsComparePeriodSecondary) {
            $errors['compare_period_secondary'] = 'Para el periodo seleccionado no hay datos en crudo comercial. Ejecute "commercial:sync-from-drive" si es necesario.';
        }

        return $errors;
    }
}
