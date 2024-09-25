<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Agrega una columna de ID primaria autoincrementable
            $table->string('ean')->unique()->nullable(); // EAN del producto
            $table->string('division')->nullable(); // División del producto
            $table->string('brand')->nullable(); // Marca del producto
            $table->string('universe')->nullable(); // Universo del producto
            $table->string('product_line')->nullable(); // Línea de producto
            $table->string('cu_code')->nullable(); // Código CU
            $table->string('material')->nullable(); // Material
            $table->string('presentacion')->nullable(); // Presentación
            $table->string('formato')->nullable(); // Formato
            $table->string('category')->nullable(); // Categoría
            $table->string('grupo_imputacion')->nullable(); // Grupo de imputación para material
            $table->string('valuation_class')->nullable(); // Clase de valoración
            $table->string('femenino_masculino')->nullable(); // Femenino/Masculino
            $table->string('xl')->nullable(); // XL, opcional
            $table->timestamps(); // Timestamps para created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
