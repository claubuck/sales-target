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
        Schema::create('sell_out_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SellOut::class)->constrained()->cascadeOnDelete();
            $table->string('point_of_sale')->nullable();
            $table->string('brand')->nullable();
            $table->string('quantity')->nullable();
            $table->string('client')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_out_details');
    }
};
