<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Notifications\Notification;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncStripe')
                ->label('Sincronizza con Stripe')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->color('primary')
                ->action(fn () => $this->syncWithStripe()),
        ];
    }

    protected function syncWithStripe(): void
    {
        try {
            /** @var \App\Models\Order $order */
            $order = $this->record;

            if (! $order->stripe_payment_intent) {
                Notification::make()
                    ->title('Nessun pagamento Stripe associato')
                    ->warning()
                    ->send();
                return;
            }

            Stripe::setApiKey(config('services.stripe.secret'));
            $intent = PaymentIntent::retrieve($order->stripe_payment_intent);

            if ($intent && $intent->status === 'succeeded') {
                $order->update([
                    'payment_status' => Order::PAY_PAID,
                    'order_status'   => Order::STATUS_PREPARING, // niente più "processing"
                ]);

                Notification::make()
                    ->title('Ordine sincronizzato con Stripe')
                    ->body('Pagamento confermato. Stato aggiornato a "preparing".')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Pagamento non completato')
                    ->body('Stripe non ha confermato il pagamento per questo ordine.')
                    ->warning()
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Errore di sincronizzazione')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
