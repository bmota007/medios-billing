<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('deposit_type')->nullable()->after('discount');
            $table->decimal('deposit_value', 10, 2)->nullable()->after('deposit_type');
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('deposit_value');
            $table->decimal('remaining_amount', 10, 2)->nullable()->after('deposit_amount');
            $table->date('remaining_due_date')->nullable()->after('remaining_amount');
            $table->string('contract_status')->default('pending')->after('status');
            $table->timestamp('contract_signed_at')->nullable()->after('contract_status');
            $table->boolean('auto_convert_after_contract')->default(true)->after('contract_signed_at');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'deposit_type',
                'deposit_value',
                'deposit_amount',
                'remaining_amount',
                'remaining_due_date',
                'contract_status',
                'contract_signed_at',
                'auto_convert_after_contract',
            ]);
        });
    }
};
