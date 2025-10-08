<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array {
        $name = ucfirst($this->faker->unique()->words(3, true));
        return [
            'name'        => $name,
            'slug'        => Str::slug($name . '-' . Str::random(4)),
            'sku'         => strtoupper(Str::random(8)),
            'price_cents' => $this->faker->numberBetween(300, 4500),
            'stock_qty'   => $this->faker->numberBetween(0, 50),
            'is_visible'  => true,
            'images'      => [$this->faker->imageUrl(640, 480, 'food', true)],
            'description' => $this->faker->paragraph(),
        ];
    }
}
