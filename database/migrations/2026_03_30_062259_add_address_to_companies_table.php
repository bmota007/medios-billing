<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'street_address')) {
                $table->string('street_address')->nullable();
            }
            if (!Schema::hasColumn('companies', 'city_state_zip')) {
                $table->string('city_state_zip')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['street_address', 'city_state_zip']);
        });
    }
};
