<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // general, snack_bar, buffet
            $table->json('name'); // Multilingual: {"en": "General", "vn": "Chung", "kr": "일반"}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_types');
    }
};
