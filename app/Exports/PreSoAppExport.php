<?php

namespace App\Exports;

use App\Models\Percentage;
use App\Models\ObjetiveDetail;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PreSoAppExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
        // Obtenemos los datos agrupados por cliente y punto de venta
        $data = ObjetiveDetail::where('objetive_id', $this->id)
            ->select('client', 'point_of_sale', 'brand', 'quantity_with_percentage', 'price')
            ->get()
            ->groupBy(['client', 'point_of_sale']); // Agrupamos por cliente y punto de venta

        // Inicializamos el array de resultado
        $result = [];

        // Iteramos cada grupo (cliente y punto de venta)
        foreach ($data as $client => $pointOfSales) {
            foreach ($pointOfSales as $pointOfSale => $rows) {
                // Inicializamos las cantidades por marca y totales
                $row = [
                    'client' => $client,
                    'point_of_sale' => $pointOfSale,
                    'CAROLINA HERRERA' => '',
                    'RABANNE' => '',
                    'JEAN PAUL GAULTIER' => '',
                    'NINA RICCI' => '',
                    'BANDERAS' => '',
                    'ADOLFO DOMINGUEZ' => '',
                    'BENETTON' => '',
                    'SHAKIRA' => '',
                    'AGATHA' => '',
                    'PACHA' => '',
                    'RAPSODIA' => '',
                    'TOTAL UNIDADES' => 0,
                    'TOTAL FACTURACION' => 0,
                ];

                // Iteramos cada fila dentro del grupo para asignar cantidades a la marca correspondiente
                foreach ($rows as $detail) {
                    // Asignamos la cantidad de la marca al campo correspondiente
                    $row[$detail->brand] = $detail->quantity_with_percentage;

                    // Sumar total de unidades y facturación
                    $row['TOTAL UNIDADES'] += $detail->quantity_with_percentage;
                    $row['TOTAL FACTURACION'] += $detail->price;
                }

                // Formateamos 'TOTAL UNIDADES' y 'TOTAL FACTURACION' en miles y sin decimales
                $row['TOTAL UNIDADES'] = number_format($row['TOTAL UNIDADES'], 0, ',', '.');
                $row['TOTAL FACTURACION'] = number_format($row['TOTAL FACTURACION'], 0, ',', '.');

                // Agregamos la fila procesada a los resultados
                $result[] = $row;
            }
        }

        // Convertimos los resultados a una colección para la exportación
        return collect($result);
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
                'AGATHA',
                'PACHA',
                'RAPSODIA',
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
                'FINAL % ' . $this->getPercentage('AGATHA'),
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
                'TOTAL UNIDADES ' . $this->getTotalUnits('AGATHA'),
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
        return [
            // Estilo para las columnas de TOTAL UNIDADES y TOTAL FACTURACION
            'N' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
            'O' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
            // Ajusta las letras de columna según la disposición de tus datos
        ];
    }
}
