<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Categorie base
        $cats = collect([
            ['name' => 'Torte classiche', 'slug' => 'torte-classiche'],
            ['name' => 'Monoporzioni',   'slug' => 'monoporzioni'],
            ['name' => 'Biscotti',       'slug' => 'biscotti'],
        ])->map(function ($c) { return Category::firstOrCreate($c + ['is_visible' => true]); });

        // Prodotti
        $products = Product::factory(12)->create();

        // Assegna 1-2 categorie a prodotto
        $products->each(function ($p) use ($cats) {
            $p->categories()->sync($cats->random(rand(1, 2))->pluck('id')->toArray());
        });
    }
}
