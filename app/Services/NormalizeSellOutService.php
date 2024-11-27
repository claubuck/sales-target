<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\SellOut;
use App\Traits\ClientNameTrait;

class NormalizeSellOutService
{
    use ClientNameTrait;
    
    public function normalizeSellOut($comparePeriod, $comparePeriodSecondary, $objetive)
    {
        //Al buscar un sellout se toma como prioridad el sellout_comercial, luego el sellout
        $sellOut1 = SellOut::whereMonth('period', Carbon::parse($comparePeriod)->format('m'))
            ->whereYear('period', Carbon::parse($comparePeriod)->format('Y'))
            ->orderByRaw("CASE WHEN type = 'sellout_comercial' THEN 1 WHEN type = 'sellout' THEN 2 ELSE 3 END")
            ->first();

        $sellOut2 = SellOut::whereMonth('period', Carbon::parse($comparePeriodSecondary)->format('m'))
            ->whereYear('period', Carbon::parse($comparePeriodSecondary)->format('Y'))
            ->orderByRaw("CASE WHEN type = 'sellout_comercial' THEN 1 WHEN type = 'sellout' THEN 2 ELSE 3 END")
            ->first();

        $this->saveObjetiveSelloutType($sellOut1, $sellOut2, $objetive);

        $sellOutDetail1 = $sellOut1->sellOutDetails;
        $sellOutDetail2 = $sellOut2->sellOutDetails;

        // Inicializa un array para combinar los detalles
        $combinedDetails = [];

        // Combina los detalles de sellOut1
        foreach ($sellOutDetail1 as $detail) {
            $key = $detail->brand . '|' . $detail->point_of_sale . '|' . $detail->client; // Crear una clave única
            $combinedDetails[$key] = [
                'brand' => $this->brandName($detail->brand),
                'point_of_sale' => $detail->point_of_sale,
                'client' => $detail->client,
                'quantity1' => $detail->quantity,
                'quantity2' => null, // Inicializa quantity2
            ];
        }

        // Combina los detalles de sellOut2
        foreach ($sellOutDetail2 as $detail) {
            $key = $detail->brand . '|' . $detail->point_of_sale . '|' . $detail->client; // Crear una clave única

            if (isset($combinedDetails[$key])) {
                // Si ya existe, actualiza quantity2
                $combinedDetails[$key]['quantity2'] = $detail->quantity;
            } else {
                // Si no existe, agrega un nuevo registro
                $combinedDetails[$key] = [
                    'brand' => $this->brandName($detail->brand),
                    'point_of_sale' => $detail->point_of_sale,
                    'client' => $detail->client,
                    'quantity1' => null,
                    'quantity2' => $detail->quantity,
                ];
            }
        }

        // Convierte el array de nuevo a una colección y obtiene todos los valores
        $combinedDetails = collect($combinedDetails)->values();

        $this->saveDetails($combinedDetails, $objetive);
    }

    public function saveDetails($details, $objetive)
    {
        // Guarda los detalles en la base de datos
        foreach ($details as $detail) {
            $objetive->objetiveDetails()->create([
                'brand' => $detail['brand'],
                'point_of_sale' => $detail['point_of_sale'],
                'client' => $this->nameClient($detail['client']),
                'quantity' => $detail['quantity1'],
                'quantity_secondary' => $detail['quantity2'],
            ]);
        }
    }

    public function saveObjetiveSelloutType($sellOut1, $sellOut2, $objetive)
    {
        $objetive->compare_period_sellout_type = $sellOut1->type;
        $objetive->compare_period_secondary_sellout_type = $sellOut2->type;
        $objetive->save();

        return $objetive;
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
}
