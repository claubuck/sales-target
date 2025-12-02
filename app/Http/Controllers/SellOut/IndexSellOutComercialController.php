<?php

namespace App\Http\Controllers\SellOut;

use Inertia\Inertia;
use App\Models\SellOut;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexSellOutComercialController extends Controller
{
    public function __invoke()
    {
        $sellOuts = SellOut::where('type', 'sellout_commercial')
            ->orderBy('period', 'desc')
            ->get();
        
        return Inertia::render('SellOutCommercial/Index', [
            'sellouts' => $sellOuts,
        ]);
    }
}
