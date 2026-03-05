<?php

namespace App\Imports;

use App\Models\CommercialRaw;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CommercialRawImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row): ?CommercialRaw
    {
        $ano = $this->getValue($row, ['año', 'ano', 'anio']);
        $mes = $this->getValue($row, ['mes']);
        $marca = $this->getValue($row, ['marca']);
        $cliente = $this->getValue($row, ['cliente']);
        $sucursal = $this->getValue($row, ['sucursal']);
        $unidades = $this->getValue($row, ['unidades']);

        if ($ano === null && $mes === null && $marca === null && $cliente === null && $sucursal === null && $unidades === null) {
            return null;
        }

        return new CommercialRaw([
            'ano' => is_numeric($ano) ? (int) $ano : null,
            'mes' => $mes !== null && $mes !== '' ? (string) $mes : null,
            'marca' => $marca !== null && $marca !== '' ? (string) $marca : null,
            'cliente' => $cliente !== null && $cliente !== '' ? (string) $cliente : null,
            'sucursal' => $sucursal !== null && $sucursal !== '' ? (string) $sucursal : null,
            'unidades' => is_numeric($unidades) ? (float) $unidades : 0,
            'imported_at' => now(),
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Get value from row trying multiple possible header names (Excel normalizes headers).
     */
    private function getValue(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                $v = $row[$key];
                if ($v !== null && $v !== '') {
                    return $v;
                }
            }
        }

        return null;
    }
}
