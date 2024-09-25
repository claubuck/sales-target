<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjetiveDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'objetive_id',
        'brand',
        'point_of_sale',
        'client',
        'quantity',
        'quantity_secondary',
        'percentage',
        'price',
    ];

    public function objetive()
    {
        return $this->belongsTo(Objetive::class);
    }
}
