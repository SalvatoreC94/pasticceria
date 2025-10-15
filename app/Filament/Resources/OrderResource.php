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

    protected static ?string $navigationIcon  = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Vendite';
    protected static ?string $navigationLabel = 'Ordini';
    protected static ?string $pluralModelLabel = 'Ordini';
    protected static ?string $modelLabel = 'Ordine';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dati ordine')->schema([
                Forms\Components\TextInput::make('code')->label('Codice')->disabled(),
                Forms\Components\TextInput::make('customer_name')->label('Cliente')->disabled(),
                Forms\Components\TextInput::make('email')->disabled(),
                Forms\Components\TextInput::make('phone')->disabled(),

                Forms\Components\Textarea::make('delivery_address')
                    ->label('Indirizzo di consegna')
                    ->rows(6)
                    ->disabled()
                    ->formatStateUsing(fn ($state) =>
                        is_array($state)
                            ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                            : (string) $state
                    ),

                Forms\Components\TextInput::make('delivery_fee_cents')
                    ->label('Spedizione')
                    ->disabled()
                    ->suffix('€')
                    ->formatStateUsing(fn ($state) => number_format((($state ?? 0) / 100), 2, ',', '.')),

                Forms\Components\TextInput::make('subtotal_cents')
                    ->label('Subtotale')
                    ->disabled()
                    ->suffix('€')
                    ->formatStateUsing(fn ($state) => number_format((($state ?? 0) / 100), 2, ',', '.')),

                Forms\Components\TextInput::make('total_cents')
                    ->label('Totale')
                    ->disabled()
                    ->suffix('€')
                    ->formatStateUsing(fn ($state) => number_format((($state ?? 0) / 100), 2, ',', '.')),

                Forms\Components\Select::make('payment_status')
                    ->label('Pagamento')
                    ->options([
                        'pending'  => 'pending',
                        'paid'     => 'paid',
                        'failed'   => 'failed',
                        'refunded' => 'refunded',
                    ])
                    ->required(),

                Forms\Components\Select::make('order_status')
                    ->label('Stato ordine')
                    ->options([
                        'new'        => 'new',
                        'processing' => 'processing',
                        'shipped'    => 'shipped',
                        'delivered'  => 'delivered',
                        'canceled'   => 'canceled',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('courier_name')->label('Corriere'),
                Forms\Components\TextInput::make('tracking_code')->label('Tracking'),
                Forms\Components\TextInput::make('stripe_payment_intent')->label('Stripe PI')->disabled(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Codice')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('total_cents')->label('Totale')->money('EUR', divideBy: 100)->sortable(),
                Tables\Columns\TextColumn::make('payment_status')->label('Pagamento')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending'  => 'warning',
                        'paid'     => 'success',
                        'failed'   => 'danger',
                        'refunded' => 'gray',
                        default    => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_status')->label('Stato')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'new'        => 'warning',
                        'processing' => 'info',
                        'shipped'    => 'primary',
                        'delivered'  => 'success',
                        'canceled'   => 'danger',
                        default      => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')->label('Creato')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Apri'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
