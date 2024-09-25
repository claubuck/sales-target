<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ean',
        'division',
        'brand',
        'universe',
        'product_line',
        'cu_code',
        'material',
        'presentacion',
        'formato',
        'category',
        'grupo_imputacion',
        'valuation_class',
        'femenino_masculino',
        'xl',
    ];
}
