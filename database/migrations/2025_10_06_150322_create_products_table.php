<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->integer('price_cents');
            $t->integer('weight_grams')->default(0);
            $t->boolean('is_visible')->default(true);
            $t->boolean('is_made_to_order')->default(false);
            $t->json('allergens')->nullable();
            $t->integer('lead_time_hours')->default(0);
            $t->integer('stock')->nullable();
            $t->string('image_path')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
