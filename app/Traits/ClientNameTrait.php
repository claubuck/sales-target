<?php

namespace App\Traits;

trait ClientNameTrait
{
    public function nameClient($client)
    {
        $replacements = [
            'CORTASSA' => 'PARFUMERIE',
            'FARMACITY' => 'GTL',
            'GRUPO ROUGE' => 'ROUGE',
            'FREE SHOP' => 'FIORANI',
            'PLEYADE' => 'BALCON',
            'SALVADO HNOS' => 'SALVADO',
            'PERFUGROUP' => 'PIGMENTO',
            // Agrega más pares de 'parcial' => 'completo' según necesites
        ];

        // Si el nombre parcial existe en el array, lo reemplaza; de lo contrario, lo deja igual
        return $replacements[$client] ?? $client;
    }
}
