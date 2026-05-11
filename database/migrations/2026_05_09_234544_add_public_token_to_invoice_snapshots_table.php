<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_snapshots', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_snapshots', 'public_token')) {
                $table->string('public_token')
                    ->nullable()
                    ->unique()
                    ->after('invoice_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_snapshots', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_snapshots', 'public_token')) {
                $table->dropColumn('public_token');
            }
        });
    }
};
