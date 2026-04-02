<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();

            $table->string('customer_name');
            $table->string('customer_email')->nullable();

            $table->string('signature_image');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('signed_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
