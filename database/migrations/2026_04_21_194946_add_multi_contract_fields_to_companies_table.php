<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            for ($i = 1; $i <= 4; $i++) {
                $table->string("contract_{$i}_name")->nullable();
                $table->string("contract_{$i}_path")->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            for ($i = 1; $i <= 4; $i++) {
                $table->dropColumn("contract_{$i}_name");
                $table->dropColumn("contract_{$i}_path");
            }
        });
    }
};
