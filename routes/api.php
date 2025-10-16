<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;

Route::get('/health', fn () => ['ok' => true, 'time' => now()->toISOString()]);

Route::get('/categories', function () {
    return Category::select('id','name','slug','is_visible')->orderBy('name')->get();
});

Route::get('/categories/{slug}/products', function (string $slug) {
    $cat = Category::where('slug', $slug)->firstOrFail();
    return $cat->products()
        ->select('products.id','products.name','products.slug','products.sku','products.price_cents','products.stock')
        ->orderBy('products.name')
        ->get();
});
