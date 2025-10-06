<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','code','email','customer_name','phone',
        'delivery_address','delivery_fee_cents','subtotal_cents','discount_cents','total_cents',
        'currency','payment_status','order_status','courier_name','tracking_code','stripe_payment_intent',
    ];

    protected $casts = [
        'delivery_address' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
