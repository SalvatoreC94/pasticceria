<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreSettingResource\Pages;
use App\Models\StoreSetting;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class StoreSettingResource extends Resource
{
    protected static ?string $model = StoreSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Impostazioni Store';
    protected static ?string $pluralModelLabel = 'Impostazioni Store';
    protected static ?string $modelLabel = 'Store';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('store_name')
                ->label('Nome Store')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('currency')
                ->label('Valuta')
                ->default('EUR')
                ->required()
                ->maxLength(3),

            Forms\Components\TextInput::make('shipping_base_cents')
                ->label('Costo base spedizione (cent)')
                ->numeric()
                ->minValue(0),

            Forms\Components\TextInput::make('free_shipping_threshold_cents')
                ->label('Soglia spedizione gratuita (cent)')
                ->numeric()
                ->minValue(0),

            Forms\Components\Toggle::make('is_open')
                ->label('Negozio aperto')
                ->default(true),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store_name')->label('Nome')->searchable(),
                Tables\Columns\TextColumn::make('currency')->label('Valuta')->sortable(),
                Tables\Columns\TextColumn::make('shipping_base_cents')->label('Spedizione (cent)')->sortable(),
                Tables\Columns\TextColumn::make('free_shipping_threshold_cents')->label('Soglia gratuita (cent)')->sortable(),
                Tables\Columns\IconColumn::make('is_open')->label('Aperto')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStoreSettings::route('/'),
            'create' => Pages\CreateStoreSetting::route('/create'),
            'edit' => Pages\EditStoreSetting::route('/{record}/edit'),
        ];
    }
}
