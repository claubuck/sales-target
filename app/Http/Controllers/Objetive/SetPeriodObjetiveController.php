<?php

namespace App\Http\Controllers\Objetive;

use App\Models\Objetive;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class SetPeriodObjetiveController extends Controller
{
    public function __invoke(Request $request)
    {
        $objetive = Objetive::find($request->input('objetive_id'));
        $compare_period = Carbon::parse($objetive->compare_period)->format('Y-m');
        $comparison_period = Carbon::parse($request->input('comparison_period'))->format('Y-m');

        $field = $compare_period === $comparison_period ? 'compare_period' : 'compare_period_secondary';

        $value = $objetive[$field];

        $objetive->update([
            "comparison_period" => $value,
        ]);
        return redirect()->back()->with('success', 'Periodo modificado correctamente');
    }
}
