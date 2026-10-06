<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalWorkType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'execution_time',
        'show_quantity',
        'description_required',
    ];

    protected $casts = [
        'execution_time' => 'decimal:2',
        'show_quantity' => 'boolean',
        'description_required' => 'boolean',
    ];
}
