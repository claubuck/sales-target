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
        Schema::create('equivalence_doors', function (Blueprint $table) {
            $table->id();
            $table->string('client');
            $table->string('sucursal');
            $table->string('sucursal_objetivo_ba');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equivalence_doors');
    }
};
