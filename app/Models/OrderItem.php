<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id','product_id','name','price_cents','qty',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
