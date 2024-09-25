<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->orderBy('brand')
            ->paginate(10);

        return Inertia::render('Products/Index', [
            'products' => $products,
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
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    public function import(Request $request)
    {
        try {
            // Validar el archivo
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);
            // Obtener el archivo subido
            $file = $request->file('file');

            // Crear la ruta de destino para la carpeta temp
            $destinationPath = storage_path('app/public/temp');

            // Generar un nombre único para el archivo
            $filename = uniqid() . '_' . $file->getClientOriginalName();
    
            // Mover el archivo a la carpeta temp
            $file->move($destinationPath, $filename);

            // Importar el archivo
            Excel::import(new ProductsImport, storage_path('app/public/temp/' . $filename));

            return redirect()->back()->with('success', 'Productos importados exitosamente!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar el archivo: ' . $e->getMessage());
        }
    }
}
