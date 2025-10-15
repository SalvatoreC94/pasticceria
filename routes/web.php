<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;

// Root: gli admin vanno al pannello; tutti gli altri al catalogo pubblico
Route::get('/', function () {
    if (auth()->check() && auth()->user()->is_admin) {
        return redirect('/admin');
    }
    return redirect()->route('catalogo');
});

// Catalogo pubblico
Route::get('/catalogo', function () {
    $categories = Category::where('is_visible', true)
        ->withCount('products')
        ->orderBy('name')
        ->get();

    $products = Product::where('is_visible', true)
        ->latest()
        ->take(12)
        ->get();

    return view('catalogo', compact('categories', 'products'));
})->name('catalogo');

// Pagina categoria
Route::get('/categorie/{category:slug}', function (Category $category) {
    $products = $category->products()
        ->where('is_visible', true)
        ->latest()
        ->paginate(12);

    return view('categoria', compact('category', 'products'));
})->name('categoria.show');

// Pagina prodotto
Route::get('/prodotti/{product:slug}', function (Product $product) {
    abort_unless($product->is_visible, 404);
    return view('prodotto', compact('product'));
})->name('prodotto.show');

// Carrello
Route::prefix('carrello')->group(function () {
    Route::post('/aggiungi', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/aggiorna/{item}', [CartController::class, 'update'])
        ->whereNumber('item')
        ->name('cart.update');
    Route::delete('/rimuovi/{item}', [CartController::class, 'remove'])
        ->whereNumber('item')
        ->name('cart.remove');
    Route::get('/', [CartController::class, 'show'])->name('cart.show');
});

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder'])->name('checkout.create');

// Webhook Stripe (invocabile)
Route::post('/stripe/webhook', StripeWebhookController::class)
    ->name('stripe.webhook');


// Thank you
Route::view('/checkout/thank-you', 'checkout.thank-you')->name('order.thankyou');

// Rotte di Breeze (login/registrazione)
require __DIR__ . '/auth.php';

// Fallback 404 (evita errori brutti su URL inesistenti)
Route::fallback(function () {
    abort(404);
});
