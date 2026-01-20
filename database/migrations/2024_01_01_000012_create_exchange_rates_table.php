<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 5); // USD
            $table->string('to_currency', 5); // VND, KRW, etc.
            $table->decimal('rate', 15, 6); // Exchange rate
            $table->decimal('buy_rate', 15, 6)->nullable();
            $table->decimal('sell_rate', 15, 6)->nullable();
            $table->date('rate_date');
            $table->string('source', 50)->default('vietcombank'); // API source
            $table->timestamps();
            
            $table->unique(['from_currency', 'to_currency', 'rate_date']);
            $table->index('rate_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
