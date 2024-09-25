<?php

namespace App\Services;

use App\Models\SellOut;
use App\Models\ObjetiveDetail;

class NormalizeSellOutService
{
    public function normalizeSellOut($comparePeriod, $comparePeriodSecondary, $objetive)
    {
        $sellOut1 = SellOut::find(1);
        $sellOut2 = SellOut::find(2);

        $sellOutDetail1 = $sellOut1->sellOutDetails;
        $sellOutDetail2 = $sellOut2->sellOutDetails;

        // Inicializa un array para combinar los detalles
        $combinedDetails = [];

        // Combina los detalles de sellOut1
        foreach ($sellOutDetail1 as $detail) {
            $key = $detail->brand . '|' . $detail->point_of_sale . '|' . $detail->client; // Crear una clave única
            $combinedDetails[$key] = [
                'brand' => $detail->brand,
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
                    'brand' => $detail->brand,
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
                'client' => $detail['client'],
                'quantity' => $detail['quantity1'],
                'quantity_secondary' => $detail['quantity2'],
            ]);
        }
    }
}
