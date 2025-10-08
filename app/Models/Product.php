<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','sku','price_cents','stock_qty','is_visible','images','description'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'images'     => 'array',
    ];

    public function categories() {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    // Accessor comodo per Vue
    public function getPriceFormattedAttribute(): string {
        return number_format($this->price_cents / 100, 2, ',', '.');
    }
}
