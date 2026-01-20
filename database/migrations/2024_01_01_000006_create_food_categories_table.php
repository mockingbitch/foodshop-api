<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // 4-digit code
            $table->foreignId('parent_id')->nullable()->constrained('food_categories')->nullOnDelete();
            
            // Images (5 images - shared across all languages)
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
            $table->string('image_4')->nullable();
            $table->string('image_5')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('parent_id');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_categories');
    }
};
