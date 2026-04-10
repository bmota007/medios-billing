<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {

            $table->id();

            $table->string('name'); // company name
            $table->string('email')->nullable();

            $table->string('logo')->nullable(); // company logo

            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            $table->string('domain')->nullable();     // future custom domain
            $table->string('subdomain')->nullable();  // SaaS subdomain

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
