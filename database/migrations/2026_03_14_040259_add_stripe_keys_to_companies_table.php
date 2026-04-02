<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }
            if (!Schema::hasColumn('companies', 'stripe_id')) {
                $table->string('stripe_id')->nullable();
            }
            if (!Schema::hasColumn('companies', 'website')) {
                $table->string('website')->nullable();
            }
            if (!Schema::hasColumn('companies', 'is_vetted')) {
                $table->boolean('is_vetted')->default(false);
            }
            // We skip 'phone' because it already exists!
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'stripe_id', 'website', 'is_vetted']);
        });
    }
};
