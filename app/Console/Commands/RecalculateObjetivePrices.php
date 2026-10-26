<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Objetive;
use Illuminate\Console\Command;

class RecalculateObjetivePrices extends Command
{
    protected $signature = 'objetives:recalculate-prices {id : ID del objetivo a recalcular}';

    protected $description = 'Recalcula objetive_details.price usando quantity_with_percentage * weighted_price actual de cada marca.';

    public function handle(): int
    {
        $objetive = Objetive::findOrFail($this->argument('id'));

        $updated = 0;
        $skipped = 0;

        $objetive->objetiveDetails()->each(function ($detail) use (&$updated, &$skipped) {
            $brand = Brand::where('name', $detail->brand)->first();

            if (!$brand) {
                $skipped++;
                return;
            }

            $detail->price = (float) ($detail->quantity_with_percentage ?? 0) * (float) ($brand->weighted_price ?? 0);
            $detail->save();
            $updated++;
        });

        $totalFacturacion = $objetive->objetiveDetails()->sum('price');

        $this->info("Objetivo ID: {$objetive->id}");
        $this->info("Periodo: {$objetive->period}");
        $this->info("Detalles actualizados: {$updated}");
        $this->info("Detalles omitidos (marca no encontrada): {$skipped}");
        $this->info('Total facturacion: $' . number_format($totalFacturacion, 0, ',', '.'));

        return self::SUCCESS;
    }
}
