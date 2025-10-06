<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show()
    {
        $cart = Cart::fromSession();
        $items = $cart?->items()->with('product')->get() ?? collect();
        $subtotal = $items->sum('total_cents');
        return view('cart.show', compact('cart','items','subtotal'));
    }

    public function add(Request $r)
    {
        $data = $r->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1|max:20',
        ]);

        $cart = Cart::fromSession();
        $product = Product::findOrFail($data['product_id']);

        // Se stock definito, verifica
        if (!is_null($product->stock) && $data['qty'] > $product->stock) {
            return back()->withErrors(['qty' => 'Quantità oltre lo stock disponibile.']);
        }

        // Se esiste già riga stesso prodotto, somma qty
        $item = $cart->items()->where('product_id', $product->id)->first();
        if ($item) {
            $newQty = $item->qty + $data['qty'];
            if (!is_null($product->stock) && $newQty > $product->stock) {
                return back()->withErrors(['qty' => 'Quantità oltre lo stock disponibile.']);
            }
            $item->update([
                'qty' => $newQty,
                'total_cents' => $newQty * $item->unit_price_cents,
            ]);
        } else {
            $cart->items()->create([
                'product_id'       => $product->id,
                'qty'              => $data['qty'],
                'unit_price_cents' => $product->price_cents,
                'total_cents'      => $product->price_cents * $data['qty'],
            ]);
        }

        return redirect()->route('cart.show')->with('ok', 'Aggiunto al carrello.');
    }

    public function update(Request $r, CartItem $item)
    {
        $r->validate(['qty' => 'required|integer|min:1|max:20']);

        $product = $item->product;
        if (!is_null($product->stock) && $r->qty > $product->stock) {
            return back()->withErrors(['qty' => 'Quantità oltre lo stock disponibile.']);
        }

        $item->update([
            'qty' => $r->qty,
            'total_cents' => $r->qty * $item->unit_price_cents,
        ]);

        return back()->with('ok', 'Carrello aggiornato.');
    }

    public function remove(CartItem $item)
    {
        $item->delete();
        return back()->with('ok', 'Rimosso dal carrello.');
    }
}
