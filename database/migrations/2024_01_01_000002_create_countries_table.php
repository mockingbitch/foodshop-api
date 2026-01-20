<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique(); // VN, US, KR, etc.
            $table->string('name', 100);
            $table->string('cctld', 10); // Country code top-level domain (e.g., .vn, .kr)
            $table->string('phone_code', 10)->nullable(); // +84, +82, etc.
            $table->string('currency_code', 5)->nullable(); // VND, USD, KRW
            $table->string('currency_symbol', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
