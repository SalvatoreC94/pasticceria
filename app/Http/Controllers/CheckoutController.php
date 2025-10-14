<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    /**
     * Mostra la pagina di checkout con riepilogo carrello.
     */
    public function show(Request $request)
    {
        $cart = Cart::fromSession();
        $items = $cart?->items()->with('product')->get() ?? collect();

        if ($items->isEmpty()) {
            return redirect()->route('cart.show')->withErrors('Il carrello è vuoto.');
        }

        $subtotal = (int) $items->sum('total_cents');
        $shipping = $this->calcShipping($subtotal);
        $total    = $subtotal + $shipping;

        return view('checkout.show', [
            'items'    => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total'    => $total,
        ]);
    }

    /**
     * Valida i dati, crea Order + OrderItems e apre un PaymentIntent Stripe.
     * Ritorna JSON: { clientSecret, orderCode }
     */
    public function createOrder(Request $request)
    {
        // 1) Validazione: indirizzo OBBLIGATORIO qui (registrazione leggera)
        $data = $request->validate([
            'name'            => ['required','string','max:255'],
            'email'           => ['required','email','max:255'],
            'phone'           => ['nullable','string','max:30'],
            'address.via'     => ['required','string','max:140'],
            'address.civico'  => ['required','string','max:20'],
            'address.cap'     => ['required','regex:/^\d{5}$/'],
            'address.citta'   => ['required','string','max:100'],
            'address.prov'    => ['required','string','size:2'],
        ]);

        // 2) Carrello & totali
        $cart = Cart::fromSession();
        $items = $cart?->items()->with('product')->get() ?? collect();
        if ($items->isEmpty()) {
            return response()->json(['error' => 'Carrello vuoto.'], 422);
        }

        // opzionale: controllo stock
        foreach ($items as $it) {
            $p = $it->product;
            if (! $p || ! $p->is_visible) {
                return response()->json(['error' => 'Uno dei prodotti non è disponibile.'], 422);
            }
            if (!is_null($p->stock) && $it->qty > $p->stock) {
                return response()->json(['error' => "Stock insufficiente per {$p->name}."], 422);
            }
        }

        $subtotal = (int) $items->sum('total_cents');
        $shipping = $this->calcShipping($subtotal);
        $total    = $subtotal + $shipping;

        // 3) Crea ordine
        $order = Order::create([
            'user_id'             => auth()->id(),
            'code'                => strtoupper(Str::random(10)),
            'email'               => $data['email'],
            'customer_name'       => $data['name'],
            'phone'               => $data['phone'] ?? null,
            'delivery_address'    => $data['address'], // JSON
            'delivery_fee_cents'  => $shipping,
            'subtotal_cents'      => $subtotal,
            'discount_cents'      => 0,
            'total_cents'         => $total,
            'currency'            => 'EUR',
            'payment_status'      => 'pending',
            'order_status'        => 'new',
            'courier_name'        => 'Corriere',
            'tracking_code'       => null,
            'stripe_payment_intent' => null,
        ]);

        // 4) Righe ordine (snapshot nome e prezzi al momento dell'acquisto)
foreach ($items as $it) {
    $product = $it->product; // già eager loaded
    $unit    = $it->unit_price_cents ?? $product->price_cents;  // fallback allistino
    $line    = $it->total_cents       ?? ($unit * $it->qty);

    \App\Models\OrderItem::create([
        'order_id'              => $order->id,
        'product_id'            => $it->product_id,
        'product_name_snapshot' => $product->name,
        'unit_price_cents'      => $unit,
        'total_cents'           => $line,
        'qty'                   => $it->qty,
    ]);
}

        // 5) PaymentIntent Stripe
        try {
            $secret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
            if (!$secret) {
                throw new \RuntimeException('Stripe secret non configurato.');
            }

            $stripe = new StripeClient($secret);
            $pi = $stripe->paymentIntents->create([
                'amount'   => $total,       // centesimi
                'currency' => 'eur',
                'receipt_email' => $order->email,
                'metadata' => [
                    'order_id'   => (string) $order->id,
                    'order_code' => $order->code,
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            $order->update(['stripe_payment_intent' => $pi->id]);

        } catch (\Throwable $e) {
            // fall back: segna fallito e torna errore leggibile
            $order->update(['payment_status' => 'failed']);
            return response()->json(['error' => 'Stripe error: '.$e->getMessage()], 500);
        }

        // 6) (facoltativo) scala stock
        foreach ($items as $it) {
            $p = $it->product;
            if (!is_null($p->stock)) {
                $p->decrement('stock', $it->qty);
            }
        }

        // 7) Svuota carrello
        $cart->items()->delete();

        return response()->json([
            'clientSecret' => $pi->client_secret,
            'orderCode'    => $order->code,
        ]);
    }

    /**
     * Spedizione: 10€ sotto 69€, gratis da 69€ in su (configurabile da .env).
     */
    private function calcShipping(int $subtotalCents): int
    {
        $base      = (int) env('SHIPPING_BASE_CENTS', 1000);     // 10,00 €
        $threshold = (int) env('FREE_SHIPPING_THRESHOLD_CENTS', 6900); // 69,00 €
        return $subtotalCents >= $threshold ? 0 : $base;
    }
}
