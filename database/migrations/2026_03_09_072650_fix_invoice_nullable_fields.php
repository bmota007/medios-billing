<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixInvoiceNullableFields extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {

            $table->string('customer_name')->nullable()->change();
            $table->string('customer_email')->nullable()->change();
            $table->string('street_address')->nullable()->change();
            $table->string('city_state_zip')->nullable()->change();

        });
    }

    public function down()
    {
        //
    }
}
