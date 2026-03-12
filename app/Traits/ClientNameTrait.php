<?php

namespace App\Traits;

use App\Models\ClientEquivalence;

trait ClientNameTrait
{
    public function nameClient($client)
    {
        $display = ClientEquivalence::where('cliente_comercial', $client)->value('cliente_display');

        return $display ?? $client;
    }
}
