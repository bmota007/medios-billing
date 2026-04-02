<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('status');
            }

            if (!Schema::hasColumn('invoices', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('invoices', 'check_number')) {
                $table->string('check_number')->nullable()->after('payment_reference');
            }

            if (!Schema::hasColumn('invoices', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('check_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('invoices', 'payment_method')) {
                $columns[] = 'payment_method';
            }

            if (Schema::hasColumn('invoices', 'payment_reference')) {
                $columns[] = 'payment_reference';
            }

            if (Schema::hasColumn('invoices', 'check_number')) {
                $columns[] = 'check_number';
            }

            if (Schema::hasColumn('invoices', 'payment_notes')) {
                $columns[] = 'payment_notes';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
