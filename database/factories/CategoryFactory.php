<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
// database/factories/CategoryFactory.php
// database/factories/CategoryFactory.php
class CategoryFactory extends Factory {
    protected $model = Category::class;
    public function definition(){
        $name = $this->faker->unique()->randomElement(['Cornetteria','Torte','Mignon','Biscotti','Salati']);
        return ['name'=>$name,'slug'=>Str::slug($name),'description'=>$this->faker->sentence(),'is_visible'=>true];
    }
}



