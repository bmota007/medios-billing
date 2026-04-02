<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_followups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'email_reminder',
                'sms_reminder',
                'final_notice',
                'ai_followup'
            ]);

            $table->timestamp('scheduled_for');
            $table->boolean('sent')->default(false);
            $table->timestamp('sent_at')->nullable();

            $table->text('message')->nullable();

            $table->timestamps();

            $table->index(['scheduled_for', 'sent']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_followups');
    }
};
