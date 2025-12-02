<?php

namespace App\Imports;

use App\Models\EquivalenceDoors;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SellOutCommercialImport implements ToCollection, WithHeadingRow
{
    protected $data = [];
    protected $sellOut;

    protected $monthMap = [
        'ene' => 'Jan',
        'feb' => 'Feb',
        'mar' => 'Mar',
        'abr' => 'Apr',
        'may' => 'May',
        'jun' => 'Jun',
        'jul' => 'Jul',
        'ago' => 'Aug',
        'sep' => 'Sep',
        'oct' => 'Oct',
        'nov' => 'Nov',
        'dic' => 'Dec',
    ];

    public function __construct($sellOut)
    {
        $this->sellOut = $sellOut;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $periodYear = $this->sellOut->period->year; // Obtiene el año del periodo
        $periodMonth = $this->sellOut->period->format('M'); // Obtiene el mes en formato abreviado (e.g., Jan, Feb, etc.)

        // Almacena los datos en un array
        foreach ($collection as $row) {
            $excelYear = $row['ano'];
            $excelMonth = strtolower($row['mes']); // Convierte a minúsculas
            $convertedMonth = isset($this->monthMap[$excelMonth]) ? $this->monthMap[$excelMonth] : null;

            // Solo procesa los registros que coincidan con el mes y el año del periodo
            if ($excelYear == $periodYear && $convertedMonth == $periodMonth) {
                $key = $row['cliente'] . '|' . $row['sucursal'] . '|' . $row['marca'];

                // Obtener punto de venta o null si no existe equivalencia
                $pointOfSale = $this->getPointOfSale($row['sucursal'], $row['cliente']);

                // Si no se encontró equivalencia, omitir el registro
                if ($pointOfSale === null) {
                    continue;
                }

                $key = $row['cliente'] . '|' . $pointOfSale . '|' . strtoupper($row['marca']);

                // Si el registro ya existe, suma la cantidad al registro existente
                if (isset($this->data[$key])) {
                    $this->data[$key]['quantity'] += $row['sumunidades'];
                } else {
                    // Si no existe, crea el registro
                    $this->data[$key] = [
                        'client' => $row['cliente'],
                        'brand' => strtoupper($row['marca']),
                        'point_of_sale' => $pointOfSale,
                        'quantity' => $row['sumunidades'],
                    ];
                }
            }
        }

        // Guarda los datos en la base de datos
        $this->saveData();
    }

    protected function saveData()
    {
        try {
            foreach ($this->data as $item) {
                $this->sellOut->sellOutDetails()->create($item);
            }
        } catch (\Exception $e) {
            Log::error('Error al guardar los datos del sellOut: ' . $e->getMessage());
        }
    }

    protected function getPointOfSale($pointOfSale, $client)
    {
        // Define los puntos de venta que quieres consolidar en un solo nombre
        $consolidatedPointsOfSale = [
            'ISLA ALTO ROSARIO' => 'ALTO ROSARIO',
            'ISLA FRAGANCIAS P.OLMOS' => 'PATIO OLMOS',
            'ISLA NVO.CENTRO' => 'NUEVOCENTRO SHOPING',
            'ISLA PASEO DEL SIGLO' => 'PASEO DEL SIGLO SHOPPING',
        ];

        // Si el punto de venta está en la lista, lo reemplaza con el consolidado
        if (isset($consolidatedPointsOfSale[$pointOfSale])) {
            $pointOfSale = $consolidatedPointsOfSale[$pointOfSale];
        }

        $equivalence = EquivalenceDoors::where('client', $client)
            ->where('sucursal', $pointOfSale)
            ->first();

        if (!$equivalence) {
            return null;
        }

        return $equivalence->sucursal_objetivo_ba;
    }
}
