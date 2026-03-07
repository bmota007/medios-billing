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
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();

        $table->string('invoice_no')->unique();

        // Client info
        $table->string('customer_name');
        $table->string('customer_email');
        $table->string('customer_phone')->nullable();

        $table->string('street_address');
        $table->string('city_state_zip');

        // Dates
        $table->date('invoice_date');
        $table->date('due_date');

        // Money
        $table->decimal('total', 10, 2);

        // Notes
        $table->text('notes')->nullable();

        // Tracking
        $table->timestamp('sent_at')->nullable();

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
