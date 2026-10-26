<?php

namespace App\Imports;

use App\Models\CommercialRaw;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CommercialRawImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
     * Marca comun para todas las filas de una importacion; permite saber que filas son nuevas.
     */
    public function __construct(protected ?Carbon $importedAt = null)
    {
    }

    public function model(array $row): ?CommercialRaw
    {
        $ano = $this->getValue($row, ['año', 'ano', 'anio']);
        $mes = $this->getValue($row, ['mes']);
        $marca = $this->getValue($row, ['marca']);
        $cliente = $this->getValue($row, ['cliente']);
        $sucursal = $this->getValue($row, ['sucursal']);
        // El reporte comercial trae la columna "Sum(Unidades)", que WithHeadingRow convierte en "sumunidades"
        $unidades = $this->getValue($row, ['unidades', 'sumunidades']);

        if ($ano === null && $mes === null && $marca === null && $cliente === null && $sucursal === null && $unidades === null) {
            return null;
        }

        return new CommercialRaw([
            'ano' => is_numeric($ano) ? (int) $ano : null,
            'mes' => $mes !== null && $mes !== '' ? (string) $mes : null,
            'marca' => $marca !== null && $marca !== '' ? (string) $marca : null,
            'cliente' => $cliente !== null && $cliente !== '' ? (string) $cliente : null,
            'sucursal' => $sucursal !== null && $sucursal !== '' ? (string) $sucursal : null,
            'unidades' => $this->parseUnits($unidades),
            'imported_at' => $this->importedAt ?? now(),
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
     * El CSV del Drive mezcla numeros simples ("4", "-2") con formato argentino
     * ("4,000", "1.098,00"): si hay coma, el punto es de miles y la coma es decimal.
     */
    private function parseUnits(mixed $value): float
    {
        if (is_string($value) && str_contains($value, ',')) {
            $value = str_replace(',', '.', str_replace('.', '', trim($value)));
        }

        return is_numeric($value) ? (float) $value : 0;
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
