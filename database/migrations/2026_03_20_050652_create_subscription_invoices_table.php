<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('invoice_no')->unique();
            $table->string('stripe_invoice_id')->nullable()->index();
            $table->string('stripe_customer_id')->nullable()->index();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();

            $table->decimal('amount', 10, 2)->default(35.00);
            $table->string('currency')->default('usd');
            $table->string('status')->default('paid');

            $table->json('items')->nullable();

            $table->timestamp('invoice_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
