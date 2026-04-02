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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            
            // Identification
            $table->string('invoice_no')->unique();

            // Client Info (Snapshot for history)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('street_address')->nullable();
            $table->string('city_state_zip')->nullable();

            // Dates
            $table->date('invoice_date');
            $table->date('due_date');

            // Line Items & Money
            $table->json('items')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0.00); 
            $table->decimal('total', 15, 2)->default(0.00);
            $table->decimal('deposit_amount', 15, 2)->default(0.00);

            // Status & Tracking
            $table->string('status')->default('unpaid'); // unpaid, paid, overdue, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Payment Details
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('check_number')->nullable();
            $table->text('payment_notes')->nullable();
            
            // Flags
            $table->boolean('is_subscription')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
