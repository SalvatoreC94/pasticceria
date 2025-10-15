<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Stripe\StripeClient;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncStripe')
                ->label('Sincronizza con Stripe')
                ->action(function () {
                    $record = $this->record; // Order
                    if (!$record->stripe_payment_intent) {
                        $this->notify('warning', 'Nessun PaymentIntent associato.');
                        return;
                    }
                    $secret = config('services.stripe.secret', env('STRIPE_SECRET'));
                    $stripe = new StripeClient($secret);
                    $pi = $stripe->paymentIntents->retrieve($record->stripe_payment_intent);

                    if ($pi->status === 'succeeded') {
                        $record->update([
                            'payment_status' => 'paid',
                            'order_status'   => 'processing',
                        ]);
                        $this->notify('success', 'Ordine aggiornato a paid/processing.');
                    } elseif (in_array($pi->status, ['requires_payment_method', 'canceled'])) {
                        $record->update(['payment_status' => 'failed']);
                        $this->notify('danger', 'Pagamento fallito/cancellato.');
                    } else {
                        $this->notify('info', 'Stato Stripe: ' . $pi->status);
                    }
                }),
        ];
    }
}
