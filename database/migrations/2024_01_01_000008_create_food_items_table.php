<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_category_id')->constrained();
            $table->string('food_code', 50)->unique(); // Format: KR-0001-0102
            $table->enum('food_code_status', ['pending', 'confirmed', 'rejected'])->default('pending');
            
            // Multilingual fields
            $table->json('name'); // {"en": "Food Name", "vn": "Tên món ăn", "kr": "음식 이름"}
            $table->json('description')->nullable();
            
            // Images
            $table->string('main_image'); // 1 main image (required)
            $table->json('extra_images')->nullable(); // 5 extra images (optional)
            
            // Pricing
            $table->decimal('price', 10, 2); // Local currency price
            $table->decimal('price_usd', 10, 2)->nullable(); // Auto-calculated USD price
            $table->string('currency_code', 5)->default('USD');
            
            // Food Details
            $table->integer('serving_size')->nullable(); // Number of servings
            $table->integer('weight')->nullable(); // Weight in grams
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_best_seller')->default(false);
            
            // Ratings
            $table->decimal('customer_rating', 3, 2)->default(0);
            $table->integer('customer_review_count')->default(0);
            
            // Status
            $table->enum('status', ['active', 'hidden', 'pending'])->default('pending');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['restaurant_id', 'status']);
            $table->index(['food_category_id', 'status']);
            $table->index('food_code');
            $table->index('is_best_seller');
            $table->index('food_code_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
