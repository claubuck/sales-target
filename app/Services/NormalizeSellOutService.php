<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\CommercialRaw;
use App\Traits\ClientNameTrait;
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

        $combinedDetails = [];

        foreach ($rows1 as $row) {
            if ($row->sucursal === null || $row->sucursal === '') {
                continue;
            }
            $pointOfSale = $this->resolvePointOfSale($row->sucursal, $row->cliente);
            if ($pointOfSale === null) {
                continue;
            }
            $brand = $this->brandName($row->marca);
            $clientShort = $this->nameClient($row->cliente);
            $key = $brand . '|' . $pointOfSale . '|' . $clientShort;
            if (! isset($combinedDetails[$key])) {
                $combinedDetails[$key] = [
                    'brand' => $brand,
                    'point_of_sale' => $pointOfSale,
                    'client' => $row->cliente,
                    'quantity1' => 0,
                    'quantity2' => null,
                ];
            }
            $combinedDetails[$key]['quantity1'] += (float) $row->unidades;
        }

        foreach ($rows2 as $row) {
            if ($row->sucursal === null || $row->sucursal === '') {
                continue;
            }
            $pointOfSale = $this->resolvePointOfSale($row->sucursal, $row->cliente);
            if ($pointOfSale === null) {
                continue;
            }
            $brand = $this->brandName($row->marca);
            $clientShort = $this->nameClient($row->cliente);
            $key = $brand . '|' . $pointOfSale . '|' . $clientShort;
            if (! isset($combinedDetails[$key])) {
                $combinedDetails[$key] = [
                    'brand' => $brand,
                    'point_of_sale' => $pointOfSale,
                    'client' => $row->cliente,
                    'quantity1' => null,
                    'quantity2' => 0,
                ];
            }
            $combinedDetails[$key]['quantity2'] = ($combinedDetails[$key]['quantity2'] ?? 0) + (float) $row->unidades;
        }

        $this->saveDetails(collect($combinedDetails)->values(), $objetive);
        $this->completeMissingPointOfSale($objetive);
    }

    protected function resolvePointOfSale(string $sucursal, string $client): ?string
    {
        $consolidatedPointsOfSale = [
            'ISLA ALTO ROSARIO' => 'ALTO ROSARIO',
            'ISLA FRAGANCIAS P.OLMOS' => 'PATIO OLMOS',
            'ISLA NVO.CENTRO' => 'NUEVOCENTRO SHOPING',
            'ISLA PASEO DEL SIGLO' => 'PASEO DEL SIGLO SHOPPING',
        ];
        if (isset($consolidatedPointsOfSale[$sucursal])) {
            $sucursal = $consolidatedPointsOfSale[$sucursal];
        }
        $equivalence = EquivalenceDoors::where('client', $client)
            ->where('sucursal', $sucursal)
            ->first();

        return $equivalence?->sucursal_objetivo_ba;
    }

    public function saveDetails($details, $objetive)
    {
        // Guarda los detalles en la base de datos
        foreach ($details as $detail) {
            // Verifica si el punto de venta existe en EquivalenceDoors
            $exists = EquivalenceDoors::where('sucursal_objetivo_ba', $detail['point_of_sale'])->exists();

            // Si el punto de venta no existe, se omite la creación
            if (!$exists) {
                continue;
            }

            $objetive->objetiveDetails()->create([
                'brand' => $detail['brand'],
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
        // Define las relaciones cliente -> equivalencia de cliente
        $clients = [
            'ROUGE' => 'GRUPO ROUGE',
            'JULERIAQUE' => 'JULERIAQUE',
            'PIGMENTO' => 'PERFUGRUP',
            'PARFUMERIE' => 'CORTASSA',
            'DUTY PAID' => 'DUTY PAID',
            'GTL' => 'FARMACITY',
            'FIORANI' => 'FREE SHOP',
            'EL BALCON' => 'PLEYADE',
            'SALVADO' => 'SALVADO HNOS',
            // Agrega más clientes y equivalencias aquí
        ];

        foreach ($clients as $client => $equivalenceClient) {
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
