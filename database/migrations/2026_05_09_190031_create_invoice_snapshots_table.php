<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_snapshots', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('invoice_id');

            $table->string('invoice_no');

            $table->string('snapshot_type');

            // original
            // deposit_receipt
            // final_receipt
            // revision

            $table->json('snapshot_data');

            $table->decimal(
                'amount',
                12,
                2
            )->nullable();

            $table->string(
                'payment_reference'
            )->nullable();

            $table->timestamp(
                'snapshot_created_at'
            )->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'invoice_snapshots'
        );
    }
};
