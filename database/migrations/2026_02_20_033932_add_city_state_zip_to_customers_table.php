<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // ✅ Check for billing_address existence first to prevent crash
            if (Schema::hasColumn('customers', 'billing_address')) {
                if (!Schema::hasColumn('customers', 'city')) {
                    $table->string('city')->nullable()->after('billing_address');
                }
                if (!Schema::hasColumn('customers', 'state')) {
                    $table->string('state')->nullable()->after('city');
                }
                if (!Schema::hasColumn('customers', 'zip')) {
                    $table->string('zip')->nullable()->after('state');
                }
            } else {
                // If billing_address is STILL missing for some reason, add them at the end
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('zip')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['city', 'state', 'zip']);
        });
    }
};
