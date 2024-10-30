<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'CAROLINA HERRERA',
                'description' => 'Description 1',
                'weighted_price' => 1500,
                'axis' => 'EJE 1',
            ],
            [
                'name' => 'RABANNE',
                'description' => 'Description 2',
                'weighted_price' => 1500,
                'axis' => 'EJE 1',
            ],
            [
                'name' => 'JEAN PAUL GAULTIER',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 2',
            ],
            [
                'name' => 'NINA RICCI',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 2',
            ],
            [
                'name' => 'BANDERAS',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 3',
            ],
            [
                'name' => 'ADOLFO DOMINGUEZ',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 3',
            ],
            [
                'name' => 'BENETTON',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 3',
            ],
            [
                'name' => 'SHAKIRA',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 3',
            ],
            [
                'name' => 'AGATHA RUIZ DE LA PRADA',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 3',
            ],
            [
                'name' => 'PACHA',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 3',
            ],
            [
                'name' => 'RAPSODIA',
                'description' => 'Description 3',
                'weighted_price' => 1500,
                'axis' => 'EJE 3',
            ],
            
        ];

        foreach ($brands as $brand) {
            \App\Models\Brand::create($brand);
        }
    }
}
