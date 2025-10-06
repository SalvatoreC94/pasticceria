<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Cornetto alla crema',
            'Cornetto al cioccolato',
            'Torta mimosa',
            'Crostata di frutta',
            'Bigné al cioccolato',
            'Biscotti alle mandorle',
            'Rustico salato',
            'Cannolo siciliano',
            'Babà al rum',
            'Cheesecake ai frutti rossi',
        ]);

        // Categoria esistente o fallback "Varie"
        $categoryId = Category::inRandomOrder()->value('id')
            ?? Category::firstOrCreate(
                ['slug' => 'varie'],
                ['name' => 'Varie', 'description' => null, 'is_visible' => true]
            )->id;

        // Unicità assicurata sullo slug (suffisso numerico univoco)
        $suffix = fake()->unique()->numerify('####'); // 0000–9999
        $slug   = Str::slug($name) . '-' . $suffix;

        $price = fake()->numberBetween(200, 3000); // 2,00€ – 30,00€

        return [
            'category_id'      => $categoryId,
            'name'             => $name,
            'slug'             => $slug,
            'description'      => fake()->text(120),
            'price_cents'      => $price,
            'weight_grams'     => fake()->numberBetween(80, 1200),
            'is_visible'       => true,
            'is_made_to_order' => fake()->boolean(20),
            'allergens'        => ['glutine', 'latte'],
            'lead_time_hours'  => fake()->randomElement([0, 24, 48]),
            'stock'            => fake()->randomElement([null, 30, 50, 100]),
            'image_path'       => null,
        ];
    }
}
