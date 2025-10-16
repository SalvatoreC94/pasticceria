<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreSetting;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        StoreSetting::updateOrCreate(
            ['store_name' => 'Pasticceria Maitardi'],
            [
                'currency' => 'EUR',
                'shipping_base_cents' => 800,
                'free_shipping_threshold_cents' => 5000,
                'is_open' => true,
            ]
        );
    }
}
