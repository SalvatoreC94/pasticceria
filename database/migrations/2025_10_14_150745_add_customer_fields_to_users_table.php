<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone'))    $table->string('phone')->nullable()->after('email');
            if (!Schema::hasColumn('users', 'address'))  $table->string('address')->nullable()->after('phone');
            if (!Schema::hasColumn('users', 'city'))     $table->string('city')->nullable()->after('address');
            if (!Schema::hasColumn('users', 'province')) $table->string('province', 2)->nullable()->after('city');
            if (!Schema::hasColumn('users', 'zip'))      $table->string('zip', 10)->nullable()->after('province');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            // opzionale: puoi anche non droppare in down
            if (Schema::hasColumn('users', 'zip'))      $table->dropColumn('zip');
            if (Schema::hasColumn('users', 'province')) $table->dropColumn('province');
            if (Schema::hasColumn('users', 'city'))     $table->dropColumn('city');
            if (Schema::hasColumn('users', 'address'))  $table->dropColumn('address');
            if (Schema::hasColumn('users', 'phone'))    $table->dropColumn('phone');
        });
    }
};
