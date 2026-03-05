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
        Schema::create('commercial_raw', function (Blueprint $table) {
            $table->id();
            $table->integer('ano')->nullable();
            $table->string('mes')->nullable();
            $table->string('marca')->nullable();
            $table->string('cliente')->nullable();
            $table->string('sucursal')->nullable();
            $table->decimal('unidades', 15, 2)->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
            $table->index(['ano', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_raw');
    }
};
