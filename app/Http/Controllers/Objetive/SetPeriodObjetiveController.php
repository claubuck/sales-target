<?php

namespace App\Http\Controllers\Objetive;

use App\Models\Objetive;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetPeriodObjetiveController extends Controller
{
    public function __invoke(Request $request)
    {
        $objetive = Objetive::find($request->input('objetive_id'));
        $field = $request->input('comparison_period') === "quantity" ? 'compare_period' : 'compare_period_secondary';
        $value = $objetive->pluck($field)->first();

        $objetive->update([
            "comparison_period" => $value,
        ]);
        return redirect()->back()->with('success', 'Periodo modificado correctamente');
    }
}
