<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE quotes
            MODIFY status ENUM('draft','sent','approved','declined','converted')
            NOT NULL DEFAULT 'draft'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE quotes
            MODIFY status ENUM('draft','sent')
            NOT NULL DEFAULT 'draft'
        ");
    }
};
