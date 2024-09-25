<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellOutController;
use App\Http\Controllers\ObjetiveController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeightedPriceController;
use App\Http\Controllers\Objetive\SetObjetiveController;
use App\Http\Controllers\PercentageController;
use App\Http\Controllers\SellOut\ImportController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard',[DashboardController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Objetivos
    Route::resource('objetives', ObjetiveController::class);
    Route::get('set-objetive/{id}', SetObjetiveController::class)->name('set-objetive');

    //Productos
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');

    //SellOut
    Route::get('/sellout', [SellOutController::class, 'index'])->name('sellout.index');
    Route::post('/sellout/import', [ImportController::class, 'import'])->name('sellout.import');

    //Precio ponderado
    Route::get('/weighted-price', [WeightedPriceController::class, 'index'])->name('weighted-price.index');
    Route::put('/weighted-price/{id}', [WeightedPriceController::class, 'update'])->name('weighted-price.update');

    //Procentaje
    Route::resource('percentage', PercentageController::class);
});

require __DIR__.'/auth.php';
