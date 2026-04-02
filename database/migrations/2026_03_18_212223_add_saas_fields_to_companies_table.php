<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

public function up()
{
    Schema::table('companies', function (Blueprint $table) {
        $table->string('plan')->default('Free'); // Free, Pro, Enterprise
        $table->integer('mrr')->default(0);      // Monthly Recurring Revenue
        $table->string('industry')->nullable();
        $table->string('status')->default('Active'); // Active, Inactive, Pending
        $table->timestamp('last_login_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
};
