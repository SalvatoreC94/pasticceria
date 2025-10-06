<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_cents',
        'weight_grams',
        'is_visible',
        'is_made_to_order',
        'allergens',
        'lead_time_hours',
        'stock',
        'image_path',
    ];

    protected $casts = [
        'allergens'        => 'array',
        'is_visible'       => 'boolean',
        'is_made_to_order' => 'boolean',
    ];
public function getRouteKeyName(): string { return 'slug'; }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
