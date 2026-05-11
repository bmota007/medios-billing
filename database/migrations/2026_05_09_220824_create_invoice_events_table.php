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
        Schema::create('invoice_events', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('invoice_id');

            $table->unsignedBigInteger('company_id')
                  ->nullable();

            $table->unsignedBigInteger('user_id')
                  ->nullable();

            $table->string('event_type')
                  ->nullable();

            $table->string('title')
                  ->nullable();

            $table->text('description')
                  ->nullable();

            $table->json('event_data')
                  ->nullable();

            $table->string('ip_address')
                  ->nullable();

            $table->timestamps();

            $table->index('invoice_id');

            $table->index('company_id');

            $table->index('event_type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_events');
    }
};
