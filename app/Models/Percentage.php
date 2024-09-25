<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Percentage extends Model
{
    use HasFactory;

    protected $fillable = [
        'objetive_id',
        'brand',
        'percentage',
        'scope',
    ];

    public function objetive()
    {
        return $this->belongsTo(Objetive::class);
    }
}
