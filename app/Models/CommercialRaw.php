<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommercialRaw extends Model
{
    protected $table = 'commercial_raw';

    protected $fillable = [
        'ano',
        'mes',
        'marca',
        'cliente',
        'sucursal',
        'unidades',
        'imported_at',
    ];

    protected $casts = [
        'ano' => 'integer',
        'unidades' => 'decimal:2',
        'imported_at' => 'datetime',
    ];
}
