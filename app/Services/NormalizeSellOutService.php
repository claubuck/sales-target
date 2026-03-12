<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\CommercialRaw;
use App\Traits\ClientNameTrait;
use App\Models\ClientEquivalence;
use App\Models\EquivalenceDoors;

class NormalizeSellOutService
{
    use ClientNameTrait;

    /** Month number to commercial_raw mes (Spanish abbr). */
    protected static array $monthToMes = [
        1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun',
        7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic',
    ];

    public function normalizeSellOut($comparePeriod, $comparePeriodSecondary, $objetive)
    {
        $objetive->compare_period_sellout_type = 'sellout_commercial';
        $objetive->compare_period_secondary_sellout_type = 'sellout_commercial';
        $objetive->save();

        $date1 = Carbon::parse($comparePeriod);
        $date2 = Carbon::parse($comparePeriodSecondary);
        $ano1 = (int) $date1->format('Y');
        $ano2 = (int) $date2->format('Y');
        $mes1 = self::$monthToMes[(int) $date1->format('n')] ?? null;
        $mes2 = self::$monthToMes[(int) $date2->format('n')] ?? null;

        if (! $mes1 || ! $mes2) {
            $this->saveDetails(collect(), $objetive);
            $this->completeMissingPointOfSale($objetive);
            return;
        }

        $rows1 = CommercialRaw::where('ano', $ano1)->whereRaw('LOWER(mes) = ?', [strtolower($mes1)])->get();
        $rows2 = CommercialRaw::where('ano', $ano2)->whereRaw('LOWER(mes) = ?', [strtolower($mes2)])->get();

        // Una sola consulta: mapa cliente_comercial -> cliente_display (evita N+1)
        $clientDisplayMap = ClientEquivalence::all()->keyBy('cliente_comercial')->map->cliente_display->toArray();

        $toClientDisplay = function ($cliente) use ($clientDisplayMap) {
            return $clientDisplayMap[$cliente] ?? $cliente;
        };

        // Preagregar por (client_display, sucursal_upper, brand) -> suma en un solo recorrido por periodo
        $agg1 = $this->aggregateByClientSucursalBrand($rows1, $toClientDisplay);
        $agg2 = $this->aggregateByClientSucursalBrand($rows2, $toClientDisplay);

        $combinedDetails = [];
        $equivalences = EquivalenceDoors::all();

        foreach ($equivalences as $equivalence) {
            $clientEquivalence = trim((string) $equivalence->client);
            $clientDisplay = $toClientDisplay($clientEquivalence);
            $sucursalesUpper = array_filter(array_map('mb_strtoupper', array_map('trim', explode(',', $equivalence->sucursal ?? ''))));
            $pointOfSale = $equivalence->sucursal_objetivo_ba;

            if ($pointOfSale === null || $pointOfSale === '') {
                continue;
            }

            $byBrand1 = [];
            $byBrand2 = [];
            foreach ($sucursalesUpper as $suc) {
                foreach ($agg1[$clientDisplay][$suc] ?? [] as $brand => $sum) {
                    $byBrand1[$brand] = ($byBrand1[$brand] ?? 0) + $sum;
                }
                foreach ($agg2[$clientDisplay][$suc] ?? [] as $brand => $sum) {
                    $byBrand2[$brand] = ($byBrand2[$brand] ?? 0) + $sum;
                }
            }

            $brandsFound = array_unique(array_merge(array_keys($byBrand1), array_keys($byBrand2)));
            foreach ($brandsFound as $brand) {
                $q1 = $byBrand1[$brand] ?? 0;
                $q2 = $byBrand2[$brand] ?? 0;
                if ($q1 == 0 && $q2 == 0) {
                    continue;
                }
                $key = $brand . '|' . $pointOfSale . '|' . $clientDisplay;
                if (! isset($combinedDetails[$key])) {
                    $combinedDetails[$key] = [
                        'brand' => $brand,
                        'point_of_sale' => $pointOfSale,
                        'client' => $clientEquivalence,
                        'quantity1' => $q1,
                        'quantity2' => $q2,
                    ];
                } else {
                    $combinedDetails[$key]['quantity1'] += $q1;
                    $combinedDetails[$key]['quantity2'] += $q2;
                }
            }
        }

        $this->saveDetails(collect($combinedDetails)->values(), $objetive);
        $this->completeMissingPointOfSale($objetive);
    }

    /**
     * Agrega filas de commercial_raw por (client_display, sucursal_upper, brand) -> suma unidades.
     * Un solo recorrido, sin consultas a BD.
     */
    protected function aggregateByClientSucursalBrand($rows, callable $toClientDisplay): array
    {
        $agg = [];
        foreach ($rows as $r) {
            if ($r->sucursal === null || $r->sucursal === '' || $r->marca === null || $r->marca === '') {
                continue;
            }
            $clientDisplay = $toClientDisplay($r->cliente);
            $sucursalUpper = mb_strtoupper(trim($r->sucursal));
            $brand = $this->brandName($r->marca);
            if (! isset($agg[$clientDisplay][$sucursalUpper][$brand])) {
                $agg[$clientDisplay][$sucursalUpper][$brand] = 0;
            }
            $agg[$clientDisplay][$sucursalUpper][$brand] += (float) $r->unidades;
        }
        return $agg;
    }

    /**
     * Resuelve sucursal comercial + cliente al punto de venta a mostrar (sucursal_objetivo_ba).
     * Usa solo la tabla equivalence_doors: las sucursales que suman a una misma puerta están en la
     * columna sucursal separadas por coma (ej. "PASEO DEL SIGLO SHOPPING, ISLA PASEO DEL SIGLO").
     * Si la sucursal del comercial está en esa lista, se devuelve el sucursal_objetivo_ba de esa fila.
     */
    protected function resolvePointOfSale(string $sucursal, string $client): ?string
    {
        $sucursal = trim($sucursal);
        if ($sucursal === '') {
            return null;
        }

        $sucursalUpper = mb_strtoupper($sucursal);

        // 1) Coincidencia exacta: una sola sucursal por fila (client, sucursal, sucursal_objetivo_ba)
        $equivalence = EquivalenceDoors::where('client', $client)
            ->where('sucursal', $sucursal)
            ->first();

        if ($equivalence) {
            return $equivalence->sucursal_objetivo_ba;
        }

        // Si no hay filas por cliente comercial, probar por nombre a mostrar
        $rows = EquivalenceDoors::where('client', $client)->get();
        if ($rows->isEmpty()) {
            $clientDisplay = $this->nameClient($client);
            if ($clientDisplay !== $client) {
                $rows = EquivalenceDoors::where('client', $clientDisplay)->get();
            }
        }

        // 2) Buscar en filas donde sucursal es lista separada por coma ("Ecommerce, ARCOS", "PASEO DEL SIGLO SHOPPING, ISLA PASEO DEL SIGLO", etc.)
        foreach ($rows as $row) {
            $sucursalesEnFila = array_map('trim', explode(',', $row->sucursal ?? ''));
            foreach ($sucursalesEnFila as $s) {
                if ($s !== '' && mb_strtoupper($s) === $sucursalUpper) {
                    return $row->sucursal_objetivo_ba;
                }
            }
        }

        return null;
    }

    public function saveDetails($details, $objetive)
    {
        // Guarda los detalles en la base de datos
        foreach ($details as $detail) {
            $brand = $detail['brand'] ?? null;
            if ($brand === null || $brand === '') {
                continue;
            }
            // Verifica si el punto de venta existe en EquivalenceDoors
            $exists = EquivalenceDoors::where('sucursal_objetivo_ba', $detail['point_of_sale'])->exists();

            // Si el punto de venta no existe, se omite la creación
            if (!$exists) {
                continue;
            }

            $objetive->objetiveDetails()->create([
                'brand' => $brand,
                'point_of_sale' => $detail['point_of_sale'],
                'client' => $this->nameClient($detail['client']),
                'quantity' => $detail['quantity1'],
                'quantity_secondary' => $detail['quantity2'],
            ]);
        }
    }

    public function brandName($brand)
    {
        $replacements = [
            'AGATHA RUIZ DE LA PR' => 'AGATHA RUIZ DE LA PRADA',
            // Agrega más pares de 'parcial' => 'completo' según necesites
        ];

        // Si el nombre parcial existe en el array, lo reemplaza; de lo contrario, lo deja igual
        return $replacements[$brand] ?? $brand;
    }

    public function completeMissingPointOfSale($objetive)
    {
        $equivalences = ClientEquivalence::all();

        foreach ($equivalences as $row) {
            $client = $row->cliente_display;
            $equivalenceClient = $row->cliente_comercial;
            // Obtén las marcas asociadas al cliente
            $brands = Brand::pluck('name')->toArray();

            foreach ($brands as $brand) {
                // Obtiene los pointOfSale existentes en los detalles del objetivo
                $existingPointOfSales = $objetive->objetiveDetails()
                    ->where('client', $client)
                    ->where('brand', $brand)
                    ->pluck('point_of_sale')
                    ->toArray();

                // Obtiene los pointOfSale disponibles en EquivalenceDoors
                $availablePointOfSales = EquivalenceDoors::where('client', $equivalenceClient)
                    ->pluck('sucursal_objetivo_ba')
                    ->toArray();

                // Determina los pointOfSale faltantes y elimina duplicados
                $missingPointOfSales = array_unique(array_diff($availablePointOfSales, $existingPointOfSales));

                // Por cada pointOfSale faltante, agrega un nuevo detalle
                foreach ($missingPointOfSales as $pointOfSale) {
                    $objetive->objetiveDetails()->create([
                        'brand' => $brand,
                        'point_of_sale' => $pointOfSale,
                        'client' => $client,
                        'quantity' => 0,
                        'quantity_secondary' => 0,
                    ]);
                }
            }
        }
    }
}
