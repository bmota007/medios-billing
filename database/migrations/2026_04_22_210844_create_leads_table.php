<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('business_name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default('new'); // new, contacted, quote_sent, won, lost
            $table->decimal('value', 12, 2)->default(0);
            $table->date('follow_up_date')->nullable();

            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('follow_up_date');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
