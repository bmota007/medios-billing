<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_payment_method_id')->nullable();
            $table->timestamp('deposit_paid_at')->nullable();
            $table->timestamp('remaining_charged_at')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
            $table->integer('installment_total')->nullable();
            $table->integer('installment_current')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_payment_method_id',
                'deposit_paid_at',
                'remaining_charged_at',
                'retry_count',
                'last_retry_at',
                'installment_total',
                'installment_current'
            ]);
        });
    }
};
