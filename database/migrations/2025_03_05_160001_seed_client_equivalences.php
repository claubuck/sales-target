<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed initial client equivalences (commercial name -> display name).
     */
    public function up(): void
    {
        $rows = [
            ['cliente_comercial' => 'CORTASSA', 'cliente_display' => 'PARFUMERIE'],
            ['cliente_comercial' => 'FARMACITY', 'cliente_display' => 'GTL'],
            ['cliente_comercial' => 'GRUPO ROUGE', 'cliente_display' => 'ROUGE'],
            ['cliente_comercial' => 'FREE SHOP', 'cliente_display' => 'FIORANI'],
            ['cliente_comercial' => 'PLEYADE', 'cliente_display' => 'EL BALCON'],
            ['cliente_comercial' => 'SALVADO HNOS', 'cliente_display' => 'SALVADO'],
            ['cliente_comercial' => 'PERFUGROUP', 'cliente_display' => 'PIGMENTO'],
            ['cliente_comercial' => 'JULERIAQUE', 'cliente_display' => 'JULERIAQUE'],
            ['cliente_comercial' => 'DUTY PAID', 'cliente_display' => 'DUTY PAID'],
        ];

        foreach ($rows as $row) {
            DB::table('client_equivalences')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('client_equivalences')->truncate();
    }
};
