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
                $key = $row['cliente'] . '|' . $row['sucursal'];

                // Obtener punto de venta o null si no existe equivalencia
                $pointOfSale = $this->getPointOfSale($row['sucursal']);

                // Si no se encontró equivalencia, omitir el registro
                if ($pointOfSale === null) {
                    continue;
                }

                if (!isset($this->data[$key])) {
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

    protected function getPointOfSale($pointOfSale)
    {
        $equivalence = EquivalenceDoors::where('sucursal', $pointOfSale)
            ->first();
        return $equivalence ? $equivalence->sucursal_objetivo_ba : null;
    }

}
