<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEquivalence extends Model
{
    use HasFactory;

    protected $table = 'client_equivalences';

    protected $fillable = [
        'cliente_comercial',
        'cliente_display',
    ];
}
