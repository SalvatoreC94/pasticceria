<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // === Costanti per gli stati ===
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PREPARING  = 'preparing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_CANCELLED  = 'cancelled';

    public const PAY_PENDING  = 'pending';
    public const PAY_PAID     = 'paid';
    public const PAY_FAILED   = 'failed';
    public const PAY_REFUNDED = 'refunded';

    // === Campi modificabili ===
    protected $fillable = [
        'code',
        'user_id',
        'email',
        'customer_name',
        'phone',
        'delivery_address',
        'delivery_fee_cents',
        'subtotal_cents',
        'discount_cents',
        'total_cents',
        'currency',
        'payment_status',
        'order_status',
        'courier_name',
        'tracking_code',
        'stripe_payment_intent',
    ];

    // === Cast JSON ===
    protected $casts = [
        'delivery_address' => 'array',
    ];

    // === Relazioni ===
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
