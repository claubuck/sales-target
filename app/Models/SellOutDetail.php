<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellOutDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_out_id',
        'point_of_sale',
        'client',
        'brand',
        'quantity',
    ];

    public function sellOut()
    {
        return $this->belongsTo(SellOut::class);
    }
}
