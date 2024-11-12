<?php

namespace App\Imports;

use App\Models\EquivalenceDoors;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EquivalenceDoorsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Verificar si todos los valores son nulos
        if (is_null($row['cliente']) || is_null($row['sucursal']) || is_null($row['sucursal_objetivo_ba'])) {
            return null; // Ignorar filas vacías
        }

        return new EquivalenceDoors([
            'client' => $row['cliente'],
            'sucursal' => $row['sucursal'],
            'sucursal_objetivo_ba' => $row['sucursal_objetivo_ba'],
        ]);
    }
}
