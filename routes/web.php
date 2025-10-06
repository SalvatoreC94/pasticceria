<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// Root: gli admin vanno al pannello, tutti gli altri al catalogo pubblico
Route::get('/', function () {
    if (auth()->check() && auth()->user()->is_admin) {
        return redirect('/admin');
    }
    return redirect()->route('catalogo');
});

// Catalogo pubblico
Route::get('/catalogo', function () {
    $categories = Category::where('is_visible', true)->withCount('products')->orderBy('name')->get();
    $products   = Product::where('is_visible', true)->latest()->take(12)->get();
    return view('catalogo', compact('categories', 'products'));
})->name('catalogo');

// Pagine categoria e prodotto
Route::get('/categorie/{category:slug}', function (Category $category) {
    $products = $category->products()->where('is_visible', true)->latest()->paginate(12);
    return view('categoria', compact('category','products'));
})->name('categoria.show');

Route::get('/prodotti/{product:slug}', function (Product $product) {
    abort_unless($product->is_visible, 404);
    return view('prodotto', compact('product'));
})->name('prodotto.show');

Route::prefix('carrello')->group(function () {
    Route::post('/aggiungi', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/aggiorna/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/rimuovi/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/', [CartController::class, 'show'])->name('cart.show');
});

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'createOrder'])->name('checkout.create');

Route::view('/ordine/confermato', 'ordine-confermato')->name('order.thankyou');

// Rotte di Breeze (login/registrazione)
require __DIR__.'/auth.php';
