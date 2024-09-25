<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objetive extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',   
        'compare_period',
        'compare_period_secondary',
        'status',
    ];

    public function objetiveDetails()
    {
        return $this->hasMany(ObjetiveDetail::class);
    }

    public function percentages()
    {
        return $this->hasMany(Percentage::class);
    }
}
