<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = Cart::fromSession();
        abort_if(!$cart || $cart->items()->count() === 0, 404);

        $items = $cart->items()->with('product')->get();
        $subtotal = $items->sum('total_cents');

        $shipping = app(ShippingService::class)->fee($subtotal);
        $total = $subtotal + $shipping;

        return view('checkout.show', compact('cart','items','subtotal','shipping','total'));
    }

    public function createOrder(Request $r, ShippingService $ship)
    {
        $data = $r->validate([
            'email'          => 'required|email',
            'name'           => 'required|string|max:120',
            'phone'          => 'nullable|string|max:30',
            'address.via'    => 'required|string|max:140',
            'address.civico' => 'required|string|max:20',
            'address.cap'    => 'required|regex:/^\d{5}$/',
            'address.citta'  => 'required|string|max:100',
            'address.prov'   => 'required|string|size:2',
        ]);

        $cart = Cart::fromSession();
        abort_if(!$cart || $cart->items()->count() === 0, 400, 'Carrello vuoto');

        $items = $cart->items()->with('product')->get();

        // Validazione stock just-in-time
        foreach ($items as $it) {
            $p = $it->product;
            if (!is_null($p->stock) && $it->qty > $p->stock) {
                return response()->json(['error' => 'Stock insufficiente per '.$p->name], 422);
            }
        }

        $subtotal = $items->sum('total_cents');
        $shipping = $ship->fee($subtotal);
        $total    = $subtotal + $shipping;

        $order = Order::create([
            'user_id'              => auth()->id(),
            'code'                 => Str::upper(Str::random(8)),
            'email'                => $data['email'],
            'customer_name'        => $data['name'],
            'phone'                => $data['phone'] ?? null,
            'delivery_address'     => $data['address'],
            'delivery_fee_cents'   => $shipping,
            'subtotal_cents'       => $subtotal,
            'total_cents'          => $total,
            'currency'             => env('APP_CURRENCY', 'EUR'),
            'payment_status'       => 'pending',
            'order_status'         => 'pending',
        ]);

        foreach ($items as $it) {
            $order->items()->create([
                'product_id'            => $it->product_id,
                'product_name_snapshot' => $it->product->name,
                'qty'                   => $it->qty,
                'unit_price_cents'      => $it->unit_price_cents,
                'total_cents'           => $it->total_cents,
            ]);
        }

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        $pi = $stripe->paymentIntents->create([
            'amount'                     => $total,
            'currency'                   => 'eur',
            'receipt_email'              => $order->email,
            'metadata'                   => ['order_code' => $order->code],
            'automatic_payment_methods'  => ['enabled' => true],
        ]);

        $order->update(['stripe_payment_intent' => $pi->id]);

        return response()->json([
            'clientSecret' => $pi->client_secret,
            'orderCode'    => $order->code,
        ]);
    }
}
