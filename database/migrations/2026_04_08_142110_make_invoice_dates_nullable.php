<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('invoice_date')->nullable()->change();
            $table->date('due_date')->nullable()->change();
            $table->date('remaining_due_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        //
    }
};
