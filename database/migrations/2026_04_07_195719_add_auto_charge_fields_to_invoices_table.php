<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            $table->decimal('subtotal_amount', 10, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);

            $table->decimal('deposit_percent', 5, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);

            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->date('remaining_due_date')->nullable();

            $table->boolean('auto_charge_enabled')->default(false);

            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_payment_method_id')->nullable();

            $table->timestamp('deposit_paid_at')->nullable();
            $table->timestamp('remaining_charged_at')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            $table->dropColumn([
                'subtotal_amount',
                'tax_percent',
                'tax_amount',
                'deposit_percent',
                'deposit_amount',
                'remaining_balance',
                'remaining_due_date',
                'auto_charge_enabled',
                'stripe_customer_id',
                'stripe_payment_method_id',
                'deposit_paid_at',
                'remaining_charged_at',
            ]);

        });
    }
};
