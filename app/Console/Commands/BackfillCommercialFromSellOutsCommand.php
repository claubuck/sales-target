<?php

namespace App\Console\Commands;

use App\Models\ClientEquivalence;
use App\Models\CommercialRaw;
use App\Models\EquivalenceDoors;
use App\Models\SellOut;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillCommercialFromSellOutsCommand extends Command
{
    protected $signature = 'commercial:backfill-from-sellouts {--dry-run : Mostrar lo que se cargaria sin escribir}';

    protected $description = 'Carga en commercial_raw el historial de los Sell Out ya subidos, solo para los meses que no estan cargados.';

    protected array $monthToMes = [
        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
        7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $this->line($dryRun ? 'Modo dry-run (sin cambios).' : 'Cargando historial...');

        $clientDisplayMap = ClientEquivalence::pluck('cliente_display', 'cliente_comercial')->toArray();
        $toClientDisplay = fn ($client) => $clientDisplayMap[trim((string) $client)] ?? trim((string) $client);

        // Puerta BA -> cliente comercial y una de sus sucursales, para que NormalizeSellOutService la vuelva a mapear igual
        $doors = [];
        foreach (EquivalenceDoors::orderBy('id')->get() as $equivalence) {
            $key = $toClientDisplay($equivalence->client) . '|' . mb_strtoupper(trim((string) $equivalence->sucursal_objetivo_ba));
            $sucursal = trim(explode(',', (string) $equivalence->sucursal)[0]);

            if ($sucursal !== '' && ! isset($doors[$key])) {
                $doors[$key] = ['cliente' => trim((string) $equivalence->client), 'sucursal' => $sucursal];
            }
        }

        $loadedMonths = CommercialRaw::select('ano', 'mes')->distinct()->get()
            ->map(fn ($row) => $row->ano . '-' . mb_strtolower((string) $row->mes))
            ->flip();

        $periods = SellOut::selectRaw('YEAR(period) as y, MONTH(period) as m, COUNT(*) as total')
            ->groupByRaw('YEAR(period), MONTH(period)')
            ->orderByRaw('YEAR(period), MONTH(period)')
            ->get();

        $totalRows = 0;

        foreach ($periods as $period) {
            $mes = $this->monthToMes[(int) $period->m];
            $label = sprintf('%d-%02d', $period->y, $period->m);

            if ($loadedMonths->has($period->y . '-' . mb_strtolower($mes))) {
                $this->line("{$label}: ya esta en commercial_raw, se omite.");
                continue;
            }

            // Mismo criterio que usaba NormalizeSellOutService antes del refactor
            $sellOut = SellOut::whereYear('period', $period->y)
                ->whereMonth('period', $period->m)
                ->orderByRaw("CASE WHEN type = 'sellout_commercial' THEN 1 WHEN type = 'sellout' THEN 2 ELSE 3 END")
                ->first();

            if ($period->total > 1) {
                $this->warn("{$label}: hay {$period->total} Sell Out cargados para este mes; se usa el #{$sellOut->id} ({$sellOut->type}), como antes del refactor. Revisar.");
            } elseif ($sellOut->type !== 'sellout_commercial') {
                $this->warn("{$label}: el Sell Out #{$sellOut->id} es de tipo {$sellOut->type}, no comercial. Revisar.");
            }

            $rows = [];
            $skipped = 0;

            foreach ($sellOut->sellOutDetails()->get() as $detail) {
                if ($detail->brand === null || $detail->brand === '' || (float) $detail->quantity == 0) {
                    continue;
                }

                $door = $doors[$toClientDisplay($detail->client) . '|' . mb_strtoupper(trim((string) $detail->point_of_sale))] ?? null;

                if ($door === null) {
                    $skipped++;
                    continue;
                }

                $rows[] = [
                    'ano' => (int) $period->y,
                    'mes' => $mes,
                    'marca' => $detail->brand,
                    'cliente' => $door['cliente'],
                    'sucursal' => $door['sucursal'],
                    'unidades' => $detail->quantity,
                    'imported_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $units = array_sum(array_column($rows, 'unidades'));
            $this->info("{$label}: Sell Out #{$sellOut->id} -> " . count($rows) . " filas, {$units} unidades" . ($skipped ? " ({$skipped} filas sin puerta en equivalence_doors, se omiten)" : ''));
            $totalRows += count($rows);

            if (! $dryRun && $rows) {
                DB::transaction(function () use ($rows) {
                    foreach (array_chunk($rows, 500) as $chunk) {
                        CommercialRaw::insert($chunk);
                    }
                });
            }
        }

        $this->info(($dryRun ? 'Se cargarian' : 'Se cargaron') . " {$totalRows} filas en total.");

        return self::SUCCESS;
    }
}
