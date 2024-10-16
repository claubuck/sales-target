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
        Schema::create('objetives', function (Blueprint $table) {
            $table->id();
            $table->dateTime('period')->nullable();
            $table->dateTime('compare_period')->nullable();
            $table->dateTime('compare_period_secondary')->nullable();
            $table->date('comparison_period')->nullable(); //Periodo de comparación seleccionado
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objetives');
    }
};
