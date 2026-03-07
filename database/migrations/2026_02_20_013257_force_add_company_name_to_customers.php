<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'company_name')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('company_name')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        //
    }
};
