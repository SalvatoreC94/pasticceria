<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('code')->unique();
            $t->string('email');
            $t->string('customer_name');
            $t->string('phone')->nullable();
            $t->json('delivery_address');
            $t->integer('delivery_fee_cents')->default(0);
            $t->integer('subtotal_cents');
            $t->integer('discount_cents')->default(0);
            $t->integer('total_cents');
            $t->string('currency', 3)->default('EUR');
            $t->enum('payment_status', ['pending','paid','failed','refunded'])->default('pending');
            $t->enum('order_status', ['pending','preparing','shipped','completed','cancelled'])->default('pending');
            $t->string('courier_name')->nullable();
            $t->string('tracking_code')->nullable();
            $t->string('stripe_payment_intent')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
