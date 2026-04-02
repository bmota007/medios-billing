<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->string('quote_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->date('quote_date')->nullable();
            $table->date('valid_until')->nullable();

            $table->enum('status', [
                'draft',
                'sent',
                'viewed',
                'accepted',
                'declined',
                'expired',
                'converted'
            ])->default('draft');

            $table->boolean('contract_required')->default(false);
            $table->boolean('signature_required')->default(false);
            $table->boolean('converted_to_invoice')->default(false);

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();

            $table->text('internal_notes')->nullable();
            $table->text('customer_notes')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
