<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Catalogo';
    protected static ?string $navigationLabel = 'Ordini';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dati ordine')->schema([
                Forms\Components\TextInput::make('code')->label('Codice')->disabled(),
                Forms\Components\TextInput::make('customer_name')->label('Cliente')->disabled(),
                Forms\Components\TextInput::make('email')->email()->disabled(),
                Forms\Components\TextInput::make('phone')->tel()->disabled(),
                Forms\Components\Textarea::make('delivery_address')->label('Indirizzo')->disabled()
                    ->formatStateUsing(fn ($s) => is_array($s) ? json_encode($s, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : $s),
            ])->columns(2),

            Forms\Components\Section::make('Totali')->schema([
                Forms\Components\TextInput::make('subtotal_cents')->numeric()->label('Subtotale')->disabled(),
                Forms\Components\TextInput::make('delivery_fee_cents')->numeric()->label('Spedizione')->disabled(),
                Forms\Components\TextInput::make('discount_cents')->numeric()->label('Sconto')->disabled(),
                Forms\Components\TextInput::make('total_cents')->numeric()->label('Totale')->disabled(),
                Forms\Components\TextInput::make('currency')->disabled(),
            ])->columns(5),

            Forms\Components\Section::make('Stati')->schema([
                Forms\Components\Select::make('order_status')->label('Stato ordine')->options([
                    'new' => 'Nuovo',
                    'processing' => 'In lavorazione',
                    'shipped' => 'Spedito',
                    'delivered' => 'Consegnato',
                    'canceled' => 'Annullato',
                ])->required(),
                Forms\Components\Select::make('payment_status')->label('Pagamento')->disabled()->options([
                    'pending' => 'In attesa',
                    'paid' => 'Pagato',
                    'failed' => 'Fallito',
                    'refunded' => 'Rimborsato',
                ]),
                Forms\Components\TextInput::make('stripe_payment_intent')->label('PI')->disabled(),
            ])->columns(3),

            Forms\Components\Section::make('Spedizione')->schema([
                Forms\Components\TextInput::make('courier_name')->label('Corriere'),
                Forms\Components\TextInput::make('tracking_code')->label('Tracking'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                Tables\Columns\TextColumn::make('code')->label('Codice')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('email')->toggleable(),
                Tables\Columns\TextColumn::make('total_cents')->label('Totale')->money('EUR', divideBy: 100)->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')->label('Pagamento')->colors([
                    'warning' => 'pending',
                    'success' => 'paid',
                    'danger'  => 'failed',
                    'gray'    => 'refunded',
                ])->sortable(),
                Tables\Columns\BadgeColumn::make('order_status')->label('Ordine')->colors([
                    'info'    => 'new',
                    'warning' => 'processing',
                    'primary' => 'shipped',
                    'success' => 'delivered',
                    'danger'  => 'canceled',
                ])->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')->label('Creato')->sortable(),
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getRelations(): array
    {
        return [OrderItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'), // lascia pure, o toglila se non vuoi creare manualmente
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
