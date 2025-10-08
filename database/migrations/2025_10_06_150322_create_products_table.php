<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160);
            $table->string('slug', 180)->unique();
            $table->string('sku', 80)->unique();
            $table->unsignedInteger('price_cents'); // es. 1299 = €12,99
            $table->unsignedInteger('stock_qty')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->json('images')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['is_visible', 'stock_qty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
