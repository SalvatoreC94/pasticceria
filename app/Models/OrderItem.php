<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    // La tabella non ha created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name_snapshot',
        'unit_price_cents',
        'total_cents',
        'qty',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
