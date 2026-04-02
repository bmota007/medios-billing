<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {

            if (!Schema::hasColumn('companies','logo')) {
                $table->string('logo')->nullable()->after('email');
            }

            if (!Schema::hasColumn('companies','primary_color')) {
                $table->string('primary_color', 20)->nullable()->after('logo');
            }

            if (!Schema::hasColumn('companies','plan_name')) {
                $table->string('plan_name')->nullable()->after('subdomain');
            }

            if (!Schema::hasColumn('companies','monthly_price')) {
                $table->decimal('monthly_price', 10, 2)->default(0)->after('plan_name');
            }

            if (!Schema::hasColumn('companies','subscription_status')) {
                $table->string('subscription_status')->default('inactive')->after('monthly_price');
            }

            if (!Schema::hasColumn('companies','subscription_started_at')) {
                $table->timestamp('subscription_started_at')->nullable()->after('subscription_status');
            }

            if (!Schema::hasColumn('companies','subscription_ends_at')) {
                $table->timestamp('subscription_ends_at')->nullable()->after('subscription_started_at');
            }

        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {

            if (Schema::hasColumn('companies','logo')) {
                $table->dropColumn('logo');
            }

            if (Schema::hasColumn('companies','primary_color')) {
                $table->dropColumn('primary_color');
            }

            if (Schema::hasColumn('companies','plan_name')) {
                $table->dropColumn('plan_name');
            }

            if (Schema::hasColumn('companies','monthly_price')) {
                $table->dropColumn('monthly_price');
            }

            if (Schema::hasColumn('companies','subscription_status')) {
                $table->dropColumn('subscription_status');
            }

            if (Schema::hasColumn('companies','subscription_started_at')) {
                $table->dropColumn('subscription_started_at');
            }

            if (Schema::hasColumn('companies','subscription_ends_at')) {
                $table->dropColumn('subscription_ends_at');
            }

        });
    }
};
