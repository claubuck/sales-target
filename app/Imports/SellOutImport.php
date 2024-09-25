<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SellOutImport implements ToCollection, WithHeadingRow
{
    protected $data = [];
    protected $sellOut; 

    public function __construct($sellOut) 
    {
        $this->sellOut = $sellOut;
    }

    /**
     * @param Collection $collection
     *
     * @return void
     */
    public function collection(Collection $collection)
    {
        // Almacena los datos en un array
        foreach ($collection as $row) {
            $key = $row['cliente'] . '|' . $row['puerta'];
            
            if (!isset($this->data[$key])) {
                $this->data[$key] = [
                    'client' => $row['cliente'],
                    'brand' => explode('-', $row['marca'])[0], // Solo toma lo que está antes del guion
                    'point_of_sale' => $row['puerta'],
                    'quantity' => 0,
                ];
            }

            // Suma el campo venta
            $this->data[$key]['quantity'] += $row['venta'];
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
}
