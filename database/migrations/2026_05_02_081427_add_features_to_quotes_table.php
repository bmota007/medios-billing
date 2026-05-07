<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'expiry_date')) { $table->date('expiry_date')->nullable()->after('quote_date'); }
            if (!Schema::hasColumn('quotes', 'subtotal')) { $table->decimal('subtotal', 15, 2)->default(0)->after('items'); }
            if (!Schema::hasColumn('quotes', 'tax_percent')) { $table->decimal('tax_percent', 5, 2)->default(0)->after('subtotal'); }
        });
    }
    public function down(): void {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['expiry_date', 'subtotal', 'tax_percent']);
        });
    }
};
