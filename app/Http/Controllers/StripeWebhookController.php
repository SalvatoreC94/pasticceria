<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $secret  = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object;
            Order::where('stripe_payment_intent', $pi->id)->update([
                'payment_status' => 'paid',
                'order_status'   => 'processing',
            ]);
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
