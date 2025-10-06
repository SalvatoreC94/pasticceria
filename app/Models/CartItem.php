<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id','product_id','qty','unit_price_cents','total_cents'];
    public function cart(){ return $this->belongsTo(Cart::class); }
    public function product(){ return $this->belongsTo(Product::class); }
}

