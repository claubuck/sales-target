<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Objetive;
use App\Models\ObjetiveDetail;
use App\Models\Percentage;
use App\Services\CalculateRealPercentageService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DedupeObjetiveDetails extends Command
{
    protected $signature = 'objetives:dedupe-details {id : ID del objetivo} {--dry-run : Mostrar cambios sin aplicarlos}';

    protected $description = 'Une filas duplicadas por marca+cliente+PDV y recalcula quantity_with_percentage y price.';

    public function __construct(
        protected CalculateRealPercentageService $calculateRealPercentageService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $objetive = Objetive::findOrFail($this->argument('id'));
        $dryRun = (bool) $this->option('dry-run');

        if (!$objetive->comparison_period) {
            $this->error('El objetivo no tiene comparison_period configurado.');

            return self::FAILURE;
        }

        $column = $this->getColumn($objetive);
        $this->info("Objetivo ID: {$objetive->id} | Periodo: {$objetive->period}");
        $this->info("Columna base para calculo: {$column}");
        $this->line($dryRun ? 'Modo dry-run (sin cambios).' : 'Aplicando cambios...');

        $details = $objetive->objetiveDetails()->orderBy('id')->get();
        $groups = $details->groupBy(fn ($d) => "{$d->brand}|{$d->client}|{$d->point_of_sale}");

        $duplicateGroups = $groups->filter(fn ($group) => $group->count() > 1);
        $rowsToDelete = $duplicateGroups->sum(fn ($group) => $group->count() - 1);

        $this->info("Grupos duplicados: {$duplicateGroups->count()}");
        $this->info("Filas a eliminar: {$rowsToDelete}");

        if ($dryRun) {
            $this->warn('Dry-run finalizado. Ejecuta sin --dry-run para aplicar.');

            return self::SUCCESS;
        }

        $mergedGroups = 0;
        $deletedRows = 0;
        $recalculatedRows = 0;

        DB::transaction(function () use (
            $objetive,
            $groups,
            $column,
            &$mergedGroups,
            &$deletedRows,
            &$recalculatedRows
        ) {
            foreach ($groups as $group) {
                if ($group->count() <= 1) {
                    continue;
                }

                $keeper = $group->sortBy('id')->first();
                $mergedQuantity = $group->max(fn ($d) => (float) ($d->quantity ?? 0));
                $mergedQuantitySecondary = $group->max(fn ($d) => (float) ($d->quantity_secondary ?? 0));

                $keeper->quantity = $mergedQuantity ?: null;
                $keeper->quantity_secondary = $mergedQuantitySecondary ?: null;
                $keeper->save();

                $idsToDelete = $group->pluck('id')->filter(fn ($id) => $id !== $keeper->id);
                $deletedRows += ObjetiveDetail::whereIn('id', $idsToDelete)->delete();
                $mergedGroups++;
            }

            $percentagesByBrand = Percentage::where('objetive_id', $objetive->id)
                ->get()
                ->keyBy('brand');

            $brandsByName = Brand::all()->keyBy('name');

            $objetive->objetiveDetails()->each(function ($detail) use (
                $objetive,
                $column,
                $percentagesByBrand,
                $brandsByName,
                &$recalculatedRows
            ) {
                $brandName = $detail->brand;

                if (empty($brandName) || !$brandsByName->has($brandName)) {
                    return;
                }

                $brand = $brandsByName->get($brandName);
                $percentageRecord = $percentagesByBrand->get($brandName);
                $percentageFactor = ($percentageRecord?->percentage ?? 0) / 100;

                $baseQuantity = (float) ($detail->{$column} ?? 0);
                $calculatedQuantity = $baseQuantity * (1 + $percentageFactor);

                $detail->quantity_with_percentage = $this->calculateQuantityWithMinimum(
                    $brandName,
                    $calculatedQuantity
                );
                $detail->price = $detail->quantity_with_percentage * (float) ($brand->weighted_price ?? 0);
                $detail->save();
                $recalculatedRows++;
            });

            $sampleDetail = $objetive->objetiveDetails()->first();

            foreach ($brandsByName as $brand) {
                if ($objetive->objetiveDetails()->where('brand', $brand->name)->exists()) {
                    $this->calculateRealPercentageService->calculate($objetive, $sampleDetail, $brand);
                }
            }
        });

        $totalFacturacion = $objetive->fresh()->objetiveDetails()->sum('price');

        $this->info("Grupos unidos: {$mergedGroups}");
        $this->info("Filas eliminadas: {$deletedRows}");
        $this->info("Filas recalculadas: {$recalculatedRows}");
        $this->info('Total facturacion: $' . number_format($totalFacturacion, 0, ',', '.'));

        return self::SUCCESS;
    }

    private function getColumn(Objetive $objetive): string
    {
        $comparisonPeriod = Carbon::parse($objetive->comparison_period)->format('Y-m');
        $comparePeriod = Carbon::parse($objetive->compare_period)->format('Y-m');

        return $comparisonPeriod === $comparePeriod ? 'quantity' : 'quantity_secondary';
    }

    private function calculateQuantityWithMinimum(string $brand, float $quantity): float
    {
        if ($quantity < 0) {
            return 0;
        }

        $minimumQuantities = [
            'AGATHA RUIZ DE LA PR' => 5,
            'AGATHA RUIZ DE LA PRADA' => 5,
            'JEAN PAUL GAULTIER' => 15,
            'NINA RICCI' => 10,
        ];

        if (isset($minimumQuantities[$brand])) {
            return max($quantity, $minimumQuantities[$brand]);
        }

        return $quantity;
    }
}
