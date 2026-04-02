<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('accept_card')->default(true);
            $table->boolean('accept_check')->default(false);
            $table->boolean('accept_cash')->default(false);
            $table->boolean('accept_zelle')->default(false);
            $table->boolean('accept_venmo')->default(false);

            $table->string('zelle_label')->nullable();
            $table->string('zelle_value')->nullable();
            $table->string('venmo_label')->nullable();
            $table->string('venmo_value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'accept_card',
                'accept_check',
                'accept_cash',
                'accept_zelle',
                'accept_venmo',
                'zelle_label',
                'zelle_value',
                'venmo_label',
                'venmo_value',
            ]);
        });
    }
};

