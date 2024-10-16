<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquivalenceDoors extends Model
{
    use HasFactory;

    protected $fillable = [
        'client',
        'sucursal',
        'sucursal_objetivo_ba',
    ];
}
