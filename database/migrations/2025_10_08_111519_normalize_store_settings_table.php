<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Se non esiste, crea tabella corretta da zero
        if (! Schema::hasTable('store_settings')) {
            Schema::create('store_settings', function (Blueprint $table) {
                $table->id();
                $table->string('store_name')->nullable();
                $table->string('currency', 3)->default('EUR');
                $table->unsignedInteger('shipping_base_cents')->default(0);
                $table->unsignedInteger('free_shipping_threshold_cents')->default(0);
                $table->boolean('is_open')->default(true);
                $table->timestamps();
            });
        } else {
            // Esiste già: adattala senza rompere ciò che c'è
            // 1) Rimuovi eventuale schema key-value
            if (Schema::hasColumn('store_settings', 'key')) {
                Schema::table('store_settings', function (Blueprint $table) {
                    if (Schema::hasColumn('store_settings', 'key')) {
                        $table->dropColumn('key');
                    }
                    if (Schema::hasColumn('store_settings', 'value')) {
                        $table->dropColumn('value');
                    }
                });
            }

            // 2) Aggiungi campi mancanti, ma SOLO se assenti
            Schema::table('store_settings', function (Blueprint $table) {
                // NON aggiungere id se esiste già
                if (! Schema::hasColumn('store_settings', 'id')) {
                    $table->id()->first();
                }
                if (! Schema::hasColumn('store_settings', 'store_name')) {
                    $table->string('store_name')->nullable();
                }
                if (! Schema::hasColumn('store_settings', 'currency')) {
                    $table->string('currency', 3)->default('EUR');
                }
                if (! Schema::hasColumn('store_settings', 'shipping_base_cents')) {
                    $table->unsignedInteger('shipping_base_cents')->default(0);
                }
                if (! Schema::hasColumn('store_settings', 'free_shipping_threshold_cents')) {
                    $table->unsignedInteger('free_shipping_threshold_cents')->default(0);
                }
                if (! Schema::hasColumn('store_settings', 'is_open')) {
                    $table->boolean('is_open')->default(true);
                }
                if (! Schema::hasColumn('store_settings', 'created_at') &&
                    ! Schema::hasColumn('store_settings', 'updated_at')) {
                    $table->timestamps();
                }
            });
        }

        // 3) Se vuota, crea un record iniziale
        if (DB::table('store_settings')->count() === 0) {
            DB::table('store_settings')->insert([
                'store_name' => config('app.name', 'Pasticceria'),
                'currency' => 'EUR',
                'shipping_base_cents' => (int) env('SHIPPING_BASE_CENTS', 1000),
                'free_shipping_threshold_cents' => (int) env('FREE_SHIPPING_THRESHOLD_CENTS', 6900),
                'is_open' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // non torniamo al key-value per semplicità
        // Schema::dropIfExists('store_settings');
    }
};
