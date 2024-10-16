<?php

namespace App\Imports;

use App\Models\EquivalenceDoors;
use Illuminate\Support\Collection;
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
        
        return new EquivalenceDoors([
            'client' => $row['cliente'],
            'sucursal' => $row['sucursal'],
            'sucursal_objetivo_ba' => $row['sucursal_objetivo_ba'],
        ]);
    }
}
