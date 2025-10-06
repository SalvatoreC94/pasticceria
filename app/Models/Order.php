<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id','code','email','customer_name','phone','delivery_address',
        'delivery_fee_cents','subtotal_cents','discount_cents','total_cents','currency',
        'payment_status','order_status','courier_name','tracking_code','stripe_payment_intent'
    ];
    protected $casts = ['delivery_address'=>'array'];
    public function items(){ return $this->hasMany(OrderItem::class); }
    public function user(){ return $this->belongsTo(User::class); }
}

