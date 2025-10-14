<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_name_snapshot')) {
                $table->string('product_name_snapshot')->nullable()->after('product_id');
            } else {
                $table->string('product_name_snapshot')->nullable()->change();
            }

            if (!Schema::hasColumn('order_items', 'unit_price_cents')) {
                $table->integer('unit_price_cents')->nullable()->after('product_name_snapshot');
            }

            if (!Schema::hasColumn('order_items', 'total_cents')) {
                $table->integer('total_cents')->nullable()->after('unit_price_cents');
            }
        });
    }

    public function down(): void
    {
        // niente down per evitare perdita dati
    }
};
