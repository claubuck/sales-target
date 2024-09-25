<?php

namespace App\Http\Controllers\Objetive;

use Inertia\Inertia;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\SellOutDetail;
use App\Imports\SellOutImport;
use App\Http\Controllers\Controller;
use App\Models\Objetive;
use App\Models\ObjetiveDetail;
use App\Models\SellOut;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class SetObjetiveController extends Controller
{
    public function __invoke(Request $request, $id)
    {
        $objetive = Objetive::find($id);

        $brands = Brand::all();
        $filter = 'CAROLINA HERRERA'; //tab activa por defecto

        if ($request->has('filter')) {
            $filter = $request->input('filter');
        }

        // Cargar los porcentajes para la marca especificada
        $objetive->load(['percentages' => function ($query) use ($filter) {
            $query->where('brand', $filter);
        }]);

        $sellOut = $this->getSellOut($objetive, $filter);

        return Inertia::render('Objetive/Index', [
            'brands' => $brands,
            'sellout' => $sellOut ?? null,
            'objetive' => $objetive,
            'filter' => $filter,
        ]);
    }

    public function getSellOut($objetive, $filter)
    {
        $objetiveDetails = $objetive->objetiveDetails()
            ->where('brand', $filter)
            ->orderBy('client', 'desc')
            ->get();

        return $objetiveDetails;
    }
}
