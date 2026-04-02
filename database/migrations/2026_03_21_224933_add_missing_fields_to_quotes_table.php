<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('quotes', 'public_token')) {
                $table->string('public_token')->nullable()->after('quote_number');
            }

            if (!Schema::hasColumn('quotes', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0)->after('due_date');
            }

            if (!Schema::hasColumn('quotes', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0)->after('subtotal');
            }

            if (!Schema::hasColumn('quotes', 'deposit_type')) {
                $table->string('deposit_type')->nullable()->after('total');
            }

            if (!Schema::hasColumn('quotes', 'deposit_value')) {
                $table->decimal('deposit_value', 10, 2)->default(0)->after('deposit_type');
            }

            if (!Schema::hasColumn('quotes', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->default(0)->after('deposit_value');
            }

            if (!Schema::hasColumn('quotes', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->default(0)->after('deposit_amount');
            }

            if (!Schema::hasColumn('quotes', 'remaining_due_date')) {
                $table->date('remaining_due_date')->nullable()->after('remaining_amount');
            }

            if (!Schema::hasColumn('quotes', 'contract_required')) {
                $table->boolean('contract_required')->default(false)->after('remaining_due_date');
            }

            if (!Schema::hasColumn('quotes', 'signature_required')) {
                $table->boolean('signature_required')->default(false)->after('contract_required');
            }

            if (!Schema::hasColumn('quotes', 'auto_convert_after_contract')) {
                $table->boolean('auto_convert_after_contract')->default(false)->after('signature_required');
            }

            if (!Schema::hasColumn('quotes', 'contract_status')) {
                $table->string('contract_status')->nullable()->after('auto_convert_after_contract');
            }

            if (!Schema::hasColumn('quotes', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('contract_status');
            }

            if (!Schema::hasColumn('quotes', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable()->after('sent_at');
            }

            if (!Schema::hasColumn('quotes', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('viewed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $columns = [
                'company_id',
                'public_token',
                'subtotal',
                'discount',
                'deposit_type',
                'deposit_value',
                'deposit_amount',
                'remaining_amount',
                'remaining_due_date',
                'contract_required',
                'signature_required',
                'auto_convert_after_contract',
                'contract_status',
                'sent_at',
                'viewed_at',
                'accepted_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('quotes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
