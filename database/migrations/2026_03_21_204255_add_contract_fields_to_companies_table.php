<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('contract_template_path')->nullable()->after('logo_path');
            $table->string('contract_template_type')->nullable()->after('contract_template_path');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'contract_template_path',
                'contract_template_type',
            ]);
        });
    }
};
