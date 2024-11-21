<?php

namespace App\Exports;

use App\Models\Brand;
use App\Models\Percentage;
use App\Models\ObjetiveDetail;
use App\Traits\ClientNameTrait;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PreSoAppExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    use ClientNameTrait;

    private $id;

    // Define el mapa de colores en PHP
    private $colorMap = [
        "EJE 1" => "58d68d", // Verde
        "EJE 2" => "85c1e9", // Azul
        "EJE 3" => "a569bd", // Morado
    ];

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Obtenemos los datos agrupados por cliente y punto de venta
        $data = ObjetiveDetail::where('objetive_id', $this->id)
            ->select('client', 'point_of_sale', 'brand', 'quantity_with_percentage', 'price')
            ->get()
            ->groupBy(['client', 'point_of_sale']);

        // Inicializamos el array de resultado
        $result = [];

        // Variable para subtotales de cada cliente
        $subtotalRow = [
            'client' => 'Subtotal',
            'point_of_sale' => '',
            'CAROLINA HERRERA' => 0,
            'RABANNE' => 0,
            'JEAN PAUL GAULTIER' => 0,
            'NINA RICCI' => 0,
            'BANDERAS' => 0,
            'ADOLFO DOMINGUEZ' => 0,
            'BENETTON' => 0,
            'SHAKIRA' => 0,
            'AGATHA RUIZ DE LA PRADA' => 0,
            'PACHA' => 0,
            'RAPSODIA' => 0,
            'DISTRIBUTED BRANDS' => '',
            'TOTAL UNIDADES' => 0,
            'TOTAL FACTURACION' => 0,
        ];

        $currentClient = null;

        // Iteramos cada grupo (cliente y punto de venta)
        foreach ($data as $client => $pointOfSales) {
            if ($currentClient !== null && $currentClient !== $client) {
                // Añadimos la fila de subtotales del cliente anterior al resultado
                $result[] = $this->formatRow($subtotalRow);
                // Reiniciamos subtotales
                $subtotalRow = array_fill_keys(array_keys($subtotalRow), 0);
                $subtotalRow['client'] = 'Subtotal';
            }

            $currentClient = $client;

            foreach ($pointOfSales as $pointOfSale => $rows) {
                // Inicializamos las cantidades por marca y totales
                $row = [
                    'client' => $this->nameClient($client),
                    'point_of_sale' => $pointOfSale,
                    'CAROLINA HERRERA' => '',
                    'RABANNE' => '',
                    'JEAN PAUL GAULTIER' => '',
                    'NINA RICCI' => '',
                    'BANDERAS' => '',
                    'ADOLFO DOMINGUEZ' => '',
                    'BENETTON' => '',
                    'SHAKIRA' => '',
                    'AGATHA RUIZ DE LA PRADA' => '',
                    'PACHA' => '',
                    'RAPSODIA' => '',
                    'DISTRIBUTED BRANDS' => '',
                    'TOTAL UNIDADES' => 0,
                    'TOTAL FACTURACION' => 0,
                ];

                // Iteramos cada fila dentro del grupo para asignar cantidades a la marca correspondiente
                foreach ($rows as $detail) {
                    $row[$detail->brand] = $detail->quantity_with_percentage;
                    $row['TOTAL UNIDADES'] += $detail->quantity_with_percentage;
                    $row['TOTAL FACTURACION'] += $detail->price;

                    // Sumar al subtotal de la marca y totales
                    $subtotalRow[$detail->brand] += $detail->quantity_with_percentage;
                    $subtotalRow['TOTAL UNIDADES'] += $detail->quantity_with_percentage;
                    $subtotalRow['TOTAL FACTURACION'] += $detail->price;
                }

                // Formateamos 'TOTAL UNIDADES' y 'TOTAL FACTURACION' en miles y sin decimales
                $row['TOTAL UNIDADES'] = number_format($row['TOTAL UNIDADES'], 0, ',', '.');
                $row['TOTAL FACTURACION'] = number_format($row['TOTAL FACTURACION'], 0, ',', '.');

                // Agregamos la fila procesada a los resultados
                $result[] = $row;
            }
        }

        // Añadimos la fila de subtotales del último cliente
        if ($currentClient !== null) {
            $result[] = $this->formatRow($subtotalRow);
        }

        return collect($result);
    }

    private function formatRow($row)
    {
        // Formatear los totales en miles sin decimales
        $row['TOTAL UNIDADES'] = number_format($row['TOTAL UNIDADES'], 0, ',', '.');
        $row['TOTAL FACTURACION'] = number_format($row['TOTAL FACTURACION'], 0, ',', '.');
        return $row;
    }


    public function headings(): array
    {
        return [
            [
                'CADENA',
                'PDV',
                'CAROLINA HERRERA',
                'RABANNE',
                'JEAN PAUL GAULTIER',
                'NINA RICCI',
                'BANDERAS',
                'ADOLFO DOMINGUEZ',
                'BENETTON',
                'SHAKIRA',
                'AGATHA RUIZ DE LA PRADA',
                'PACHA',
                'RAPSODIA',
                'DISTRIBUTED BRANDS' => '',
                'TOTAL UNIDADES',
                'TOTAL FACTURACION'
            ],

            // Fila para los porcentajes
            [
                '',
                '',
                'FINAL % ' . $this->getPercentage('CAROLINA HERRERA'),
                'FINAL % ' . $this->getPercentage('RABANNE'),
                'FINAL % ' . $this->getPercentage('JEAN PAUL GAULTIER'),
                'FINAL % ' . $this->getPercentage('NINA RICCI'),
                'FINAL % ' . $this->getPercentage('BANDERAS'),
                'FINAL % ' . $this->getPercentage('ADOLFO DOMINGUEZ'),
                'FINAL % ' . $this->getPercentage('BENETTON'),
                'FINAL % ' . $this->getPercentage('SHAKIRA'),
                'FINAL % ' . $this->getPercentage('AGATHA RUIZ DE LA PRADA'),
                'FINAL % ' . $this->getPercentage('PACHA'),
                'FINAL % ' . $this->getPercentage('RAPSODIA'),
                '',
                ''
            ],

            // Fila para las unidades totales debajo de cada marca
            [
                '',
                '',
                'TOTAL UNIDADES ' . $this->getTotalUnits('CAROLINA HERRERA'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('RABANNE'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('JEAN PAUL GAULTIER'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('NINA RICCI'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('BANDERAS'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('ADOLFO DOMINGUEZ'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('BENETTON'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('SHAKIRA'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('AGATHA RUIZ DE LA PRADA'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('PACHA'),
                'TOTAL UNIDADES ' . $this->getTotalUnits('RAPSODIA'),
                ''
            ],

            // Fila para las unidades totales debajo de cada marca
            [
                '',
                '',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                'UNIDADES',
                ''
            ]
        ];
    }


    private function getPercentage($brand)
    {
        $percentage = Percentage::where('objetive_id', $this->id)->where('brand', $brand)->first();
        return $percentage ? $percentage->real_percentage : '';
    }

    // Método para obtener el total de unidades de cada marca
    private function getTotalUnits($brand)
    {
        // Obtiene la suma de quantity_with_percentage
        $totalUnits = ObjetiveDetail::where('objetive_id', $this->id)
            ->where('brand', $brand)
            ->sum('quantity_with_percentage');

        // Retorna el total formateado en miles
        return number_format($totalUnits, 0, ',', '.');
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        // Configura los estilos de alineación para las columnas N y O
        $styles = [
            'N' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]],
            'O' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]],
        ];

        // Aplica color de fondo rojo para las columnas A y B en las primeras cuatro filas
        for ($row = 1; $row <= 4; $row++) {
            $sheet->getStyle("A$row:B$row")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '044e8c'], // Rojo
                ],
            ]);
        }

        // Aplica el color de fondo en base al eje de la marca solo en filas 1 a 4
        foreach ($this->collection()->take(4) as $rowIndex => $row) {
            foreach (array_keys($row) as $columnIndex => $brand) {
                $brandModel = Brand::where('name', $brand)->first();

                if ($brandModel && isset($this->colorMap[$brandModel->axis])) {
                    $colorHex = $this->colorMap[$brandModel->axis];

                    // Ajuste para la columna actual y fila
                    $cellCoordinate = $sheet->getCellByColumnAndRow($columnIndex + 1, $rowIndex + 1)->getCoordinate();

                    // Aplica el color de fondo específico para la marca
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => ltrim($colorHex, '#')],
                        ],
                    ]);
                }
            }
        }

        return $styles;
    }
}
