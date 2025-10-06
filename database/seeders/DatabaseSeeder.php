<?php

namespace Database\Seeders;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Support\Str;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
  public function run(): void
{
    // Categorie fisse
    $cats = collect(['Cornetteria','Torte','Mignon','Biscotti','Salati'])->map(function ($name){
        return Category::firstOrCreate(
            ['slug'=>Str::slug($name)],
            ['name'=>$name,'description'=>null,'is_visible'=>true]
        );
    });

    // Prodotti demo
    Product::factory()->count(15)->create();

    // Impostazioni negozio (soglie spedizione)
    StoreSetting::updateOrCreate(['key'=>'shipping.base_cents'], ['value'=>env('SHIPPING_BASE_CENTS',1000)]);
    StoreSetting::updateOrCreate(['key'=>'shipping.free_threshold_cents'], ['value'=>env('FREE_SHIPPING_THRESHOLD_CENTS',6900)]);
}

}
