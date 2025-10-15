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
        // Leggi payload grezzo e intestazione firma
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        // Secret del webhook da config/services.php o .env
        $secret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');
        if (! $secret) {
            Log::error('Stripe: webhook secret mancante (STRIPE_WEBHOOK_SECRET).');
            return response('Missing webhook secret', 500);
        }

        try {
            // Non strettamente necessario per verificare la firma,
            // ma utile se poi altre parti interrogano Stripe:
            Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

            // Verifica la firma e costruisci l’evento
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            // Payload non valido
            Log::error('Stripe: payload non valido', ['err' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Firma non valida
            Log::error('Stripe: firma non valida', ['err' => $e->getMessage()]);
            return response('Invalid signature', 400);
        } catch (\Throwable $e) {
            Log::error('Stripe: errore generico in verifica webhook', ['err' => $e->getMessage()]);
            return response('Webhook error', 500);
        }

        $type   = $event->type;
        $object = $event->data->object;

        Log::info('Stripe: evento ricevuto', ['type' => $type]);

        /**
         * Payment Element / PaymentIntent flow
         */
        if (in_array($type, ['payment_intent.succeeded', 'payment_intent.payment_failed'])) {
            $pi       = $object; // \Stripe\PaymentIntent
            $intentId = $pi->id ?? null;

            Log::info('Stripe: PI ricevuto', ['pi' => $intentId]);

            if ($intentId) {
                $order = Order::where('stripe_payment_intent', $intentId)->first();

                Log::info('Stripe: match ordine', [
                    'found' => (bool) $order,
                    'order_id' => $order?->id,
                    'code' => $order?->code,
                ]);

                if ($order) {
                    if ($type === 'payment_intent.succeeded') {
                        $order->update([
                            'payment_status' => 'paid',
                            'order_status'   => 'processing',
                        ]);
                        Log::info("Stripe: ordine {$order->code} aggiornato a paid/processing");
                    } else {
                        $order->update([
                            'payment_status' => 'failed',
                        ]);
                        Log::warning("Stripe: ordine {$order->code} aggiornato a failed");
                    }
                } else {
                    Log::warning('Stripe: nessun ordine trovato per PI', ['pi' => $intentId]);
                }
            }
        }

        /**
         * (Opzionale) Checkout Session flow
         * Se un domani userai Stripe Checkout, questo evento chiude comunque l’ordine.
         */
        if ($type === 'checkout.session.completed') {
            $intentId = $object->payment_intent ?? null;

            Log::info('Stripe: checkout.session.completed', ['pi' => $intentId]);

            if ($intentId) {
                $order = Order::where('stripe_payment_intent', $intentId)->first();

                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'order_status'   => 'processing',
                    ]);
                    Log::info("Stripe: ordine {$order->code} aggiornato da checkout.session a paid/processing");
                } else {
                    Log::warning('Stripe: nessun ordine per checkout.session', ['pi' => $intentId]);
                }
            }
        }

        // Rispondi 204: tutto ok (idempotente)
        return response()->noContent();
    }
}
