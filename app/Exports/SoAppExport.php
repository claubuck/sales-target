<?php

namespace App\Exports;

use App\Models\Brand;
use App\Models\ObjetiveDetail;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SoAppExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ObjetiveDetail::where('objetive_id', $this->id)->select('client', 'point_of_sale', 'brand', 'price', 'quantity_with_percentage')->get();
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'POS',
            'Division',
            'Marca',
            'Objetivo',
            'Unidades'
        ];
    }

    /**
     * Mapea los datos para cada fila.
     *
     * @param $objetiveDetail
     * @return array
     */
    public function map($objetiveDetail): array
    {
        return [
            $this->nameClient($objetiveDetail->client),
            $objetiveDetail->point_of_sale ?? '-',
            $this->getDivision($objetiveDetail->brand),
            $objetiveDetail->brand ?? '-',
            $objetiveDetail->price ?? 0,
            $objetiveDetail->quantity_with_percentage ?? 0,
        ];
    }

    /**
     * Método auxiliar para obtener la división en base a la marca.
     *
     * @param $brand
     * @return string
     */
    public function getDivision($brand)
    {
        return Brand::where('name', $brand)->first()->axis ?? '-';
    }

    /**
     * Método para definir el ancho de las columnas.
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
        ];
    }

    /**
     * Aplica estilos a las celdas.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Centrar el contenido de todas las celdas y cabeceras
        $sheet->getStyle('A1:F' . $sheet->getHighestRow())->getAlignment()->setHorizontal('center');

        // Estilo para las cabeceras (centradas y en azul)
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->applyFromArray([
            'fill' => [
                'fillType' => 'solid',
                'startColor' => [
                    'rgb' => '2075b9', // Azul
                ],
            ],
        ]);

        // Aplicar color alternado para las filas
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                // Fila par - color celeste claro
                $sheet->getStyle('A' . $row . ':F' . $row)
                    ->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'startColor' => [
                                'rgb' => 'FFFFFF', // Color blanco
                            ],
                        ],
                    ]);
            } else {
                // Fila impar - color blanco (opcional, por defecto ya es blanco)
                $sheet->getStyle('A' . $row . ':F' . $row)
                    ->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'startColor' => [
                                'rgb' => '9fcef5', // Color celeste claro
                            ],
                        ],
                    ]);
            }
        }

         // Formato de moneda para la columna E (Objetivo)
         $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('$#,##0.00');

        return [];
    }

    /**
     * Método auxiliar para obtener el nombre del cliente.
     *
     * @param $client
     * @return string
     */

    public function nameClient($client)
    {
        $replacements = [
            'CORTASSA' => 'PARFUMERIE',
            'FARMACITY' => 'GTL',
            // Agrega más pares de 'parcial' => 'completo' según necesites
        ];

        // Si el nombre parcial existe en el array, lo reemplaza; de lo contrario, lo deja igual
        return $replacements[$client] ?? $client;
    }
}
