<?php

namespace App\Filament\Resources;

use App\Models\StoreSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class StoreSettingResource extends Resource
{
    protected static ?string $model = StoreSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Impostazioni';
    protected static ?string $navigationLabel = 'Impostazioni negozio';

    // ✅ porta la voce di menu direttamente alla pagina Edit del record esistente (o lo crea)
    public static function getNavigationUrl(): string
    {
        $record = StoreSetting::query()->first();
        if (! $record) {
            $record = StoreSetting::create([
                'store_name' => config('app.name', 'Pasticceria'),
                'currency' => 'EUR',
                'shipping_base_cents' => (int) env('SHIPPING_BASE_CENTS', 1000),
                'free_shipping_threshold_cents' => (int) env('FREE_SHIPPING_THRESHOLD_CENTS', 6900),
                'is_open' => true,
            ]);
        }

        return static::getUrl('edit', ['record' => $record->getKey()]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('store_name')->label('Nome negozio'),
            Forms\Components\TextInput::make('currency')->label('Valuta')->default('EUR')->maxLength(3),
            Forms\Components\TextInput::make('shipping_base_cents')->label('Spedizione base (centesimi)')->numeric(),
            Forms\Components\TextInput::make('free_shipping_threshold_cents')->label('Soglia spedizione gratuita (centesimi)')->numeric(),
            Forms\Components\Toggle::make('is_open')->label('Negozio aperto')->default(true),
        ])->columns(2);
    }

    public static function getPages(): array
{
    return [
        'index' => \App\Filament\Resources\StoreSettingResource\Pages\ListStoreSettings::route('/'),
        'edit'  => \App\Filament\Resources\StoreSettingResource\Pages\EditStoreSetting::route('/{record}/edit'),
    ];
}

}
