<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig, $endpointSecret);
        } catch (\Throwable $e) {
            return response('Invalid payload', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object;
            $order = Order::where('stripe_payment_intent', $pi->id)->first();
            if ($order && $order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'paid', 'order_status' => 'preparing']);

                // Decrementa stock se definito
                foreach ($order->items as $it) {
                    $p = $it->product;
                    if ($p && !is_null($p->stock)) {
                        $p->decrement('stock', $it->qty);
                    }
                }

                // TODO: invia email conferma pagamento al cliente / notifica allo shop
            }
        }

        return response()->noContent();
    }
}
