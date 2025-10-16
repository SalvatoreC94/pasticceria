<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'currency',
        'shipping_base_cents',
        'free_shipping_threshold_cents',
        'is_open',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'shipping_base_cents' => 'integer',
        'free_shipping_threshold_cents' => 'integer',
    ];
}
