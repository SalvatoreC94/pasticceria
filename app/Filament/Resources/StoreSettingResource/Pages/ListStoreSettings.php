<?php

namespace App\Filament\Resources\StoreSettingResource\Pages;

use App\Filament\Resources\StoreSettingResource;
use App\Models\StoreSetting;
use Filament\Resources\Pages\ListRecords;

class ListStoreSettings extends ListRecords
{
    protected static string $resource = StoreSettingResource::class;

    public function mount(): void
    {
        // garantisci che esista 1 record
        $record = StoreSetting::first();
        if (! $record) {
            $record = StoreSetting::create([
                'store_name' => config('app.name', 'Pasticceria'),
                'currency' => 'EUR',
                'shipping_base_cents' => (int) env('SHIPPING_BASE_CENTS', 1000),
                'free_shipping_threshold_cents' => (int) env('FREE_SHIPPING_THRESHOLD_CENTS', 6900),
                'is_open' => true,
            ]);
        }

        // reindirizza direttamente all’edit
        $this->redirect(StoreSettingResource::getUrl('edit', ['record' => $record->getKey()]));
    }
}
