<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_category_id')->constrained()->cascadeOnDelete();
            $table->string('language_code', 5); // VN, US, KR, etc.
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('video_link')->nullable();
            $table->timestamps();
            
            $table->unique(['food_category_id', 'language_code']);
            $table->index('language_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_category_translations');
    }
};
