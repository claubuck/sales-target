<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Objetive;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $objetives = Objetive::query()
            ->where('status', 'active')
            ->orderBy('period', 'desc')
            ->paginate(10);  
            //paginate
        return Inertia::render('Dashboard', [
            'objetives' => $objetives
        ]);
    }
}
