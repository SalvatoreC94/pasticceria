<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret', env('STRIPE_WEBHOOK_SECRET'));

        if (!$secret) {
            Log::error('Stripe: webhook secret mancante.');
            return response('Missing webhook secret', 500);
        }

        try {
            // non obbligatorio per la verifica, ma utile se poi interroghi l’API
            Stripe::setApiKey(config('services.stripe.secret', env('STRIPE_SECRET')));
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe: payload non valido', ['err' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe: firma non valida', ['err' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        $type   = $event->type;
        $object = $event->data->object;

        Log::info('Stripe: evento ricevuto', ['type' => $type]);

        // Payment Element / PI events:
        if (in_array($type, ['payment_intent.succeeded', 'payment_intent.payment_failed'])) {
            $pi = $object; // \Stripe\PaymentIntent
            $intentId = $pi->id ?? null;

            if ($intentId) {
                $order = Order::where('stripe_payment_intent', $intentId)->first();

                Log::info('Stripe: matching ordine', [
                    'pi' => $intentId,
                    'found' => (bool) $order,
                    'code' => $order?->code,
                ]);

                if ($order) {
                    if ($type === 'payment_intent.succeeded') {
                        $order->update([
                            'payment_status' => 'paid',
                            'order_status'   => 'preparing',
                        ]);
                    } else {
                        $order->update(['payment_status' => 'failed']);
                    }
                }
            }
        }

        // (opzionale) Se mai userai Stripe Checkout:
        if ($type === 'checkout.session.completed') {
            // $object->payment_intent contiene l’ID PI
            $intentId = $object->payment_intent ?? null;
            if ($intentId) {
                $order = Order::where('stripe_payment_intent', $intentId)->first();
                if ($order) {
                    $order->update([
                        'order_status' => Order::STATUS_PREPARING,
'payment_status' => Order::PAY_PAID,

                    ]);
                }
            }
        }

        return response()->noContent();
    }
}
