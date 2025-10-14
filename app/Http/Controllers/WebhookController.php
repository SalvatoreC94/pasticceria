<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object; // \Stripe\PaymentIntent
            $piId = $pi->id ?? null;

            // Trova ordine creato al checkout
            $order = Order::where('stripe_payment_intent', $piId)->first();
            if (! $order) {
                return response()->noContent();
            }

            // Se già pagato, idempotente
            if ($order->payment_status === 'paid') {
                return response()->noContent();
            }

            DB::transaction(function () use ($order) {
                // Aggiorna stati
                $order->update([
                    'payment_status' => 'paid',
                    'order_status'   => 'processing',
                ]);

               
                $cart = Cart::query()
                    ->where('user_id', $order->user_id)
                    ->orWhere('token', optional($order->delivery_address)['cart_token'] ?? null)
                    ->with('items.product')
                    ->first();

                if ($cart && $cart->items->count()) {
                    $order->items()->delete();
                    foreach ($cart->items as $it) {
                        $product = $it->product;
                        $unit    = $product->price_cents;
                        $line    = $unit * $it->qty;

                        OrderItem::create([
                            'order_id'              => $order->id,
                            'product_id'            => $product->id,
                            'product_name_snapshot' => $product->name,
                            'unit_price_cents'      => $unit,
                            'total_cents'           => $line,
                            'qty'                   => $it->qty,
                        ]);

                        Product::where('id', $product->id)->decrement('stock_qty', $it->qty);
                    }
                    $cart->items()->delete();
                }
                
            });
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $pi = $event->data->object;
            Order::where('stripe_payment_intent', $pi->id)->update([
                'payment_status' => 'failed',
            ]);
        }

        return response()->noContent();
    }
}
