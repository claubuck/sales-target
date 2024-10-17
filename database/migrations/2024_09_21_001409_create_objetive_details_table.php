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
        Schema::create('objetive_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Objetive::class)->constrained()->cascadeOnDelete();
            $table->string('brand');
            $table->string('point_of_sale');
            $table->string('client');
            $table->integer('quantity')->nullable();
            $table->integer('quantity_secondary')->nullable();
            $table->integer('quantity_with_percentage')->nullable();
            $table->string('percentage')->nullable();
            $table->string('price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objetive_details');
    }
};
