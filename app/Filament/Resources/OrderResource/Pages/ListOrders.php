<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Notifications\Notification;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('syncStripe')
                ->label('Sincronizza')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function (Order $record) {
                    if (! $record->stripe_payment_intent) {
                        Notification::make()->title('Nessun pagamento Stripe associato')->warning()->send();
                        return;
                    }

                    try {
                        Stripe::setApiKey(config('services.stripe.secret'));
                        $intent = PaymentIntent::retrieve($record->stripe_payment_intent);

                        if ($intent && $intent->status === 'succeeded') {
                            $record->update([
                                'payment_status' => Order::PAY_PAID,
                                'order_status'   => Order::STATUS_PREPARING,
                            ]);
                            Notification::make()->title('Ordine sincronizzato')->success()->send();
                        } else {
                            Notification::make()->title('Pagamento non completato')->warning()->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()->title('Errore di sincronizzazione')->body($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}
