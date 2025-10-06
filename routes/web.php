<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = Category::where('is_visible', true)->withCount('products')->orderBy('name')->get();
    $products   = Product::where('is_visible', true)->latest()->take(12)->get();

    return view('catalogo', compact('categories', 'products'));
})->name('catalogo');

Route::get('/categorie/{category:slug}', function (Category $category) {
    $products = $category->products()->where('is_visible', true)->latest()->paginate(12);

    return view('categoria', compact('category', 'products'));
})->name('categoria.show');

Route::get('/prodotti/{product:slug}', function (Product $product) {
    abort_unless($product->is_visible, 404);
    return view('prodotto', compact('product'));
})->name('prodotto.show');
