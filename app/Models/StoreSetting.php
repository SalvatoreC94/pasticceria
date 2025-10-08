<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'currency',
        'shipping_base_cents',
        'free_shipping_threshold_cents',
        'is_open',
    ];
}
