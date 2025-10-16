<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_cents',
        'sku',
        'stock',
        'is_visible',
        'ingredients',
        'allergens',
        'nutritional_facts',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'allergens' => 'array',
        'nutritional_facts' => 'array',
        'is_visible' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
