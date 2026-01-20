<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // Unique restaurant code
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Owner
            $table->foreignId('country_id')->constrained();
            $table->foreignId('restaurant_type_id')->constrained();
            
            // Basic Information (Multilingual)
            $table->json('name'); // {"en": "Restaurant Name", "vn": "Tên nhà hàng", "kr": "레스토랑 이름"}
            $table->json('description')->nullable();
            $table->string('city', 100);
            $table->string('address');
            $table->string('phone', 20);
            $table->string('zalo', 50)->nullable();
            $table->string('email')->nullable();
            
            // Location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Images (Outside: max 2, Inside: max 5)
            $table->string('outside_image_1')->nullable();
            $table->string('outside_image_2')->nullable();
            $table->string('inside_image_1')->nullable();
            $table->string('inside_image_2')->nullable();
            $table->string('inside_image_3')->nullable();
            $table->string('inside_image_4')->nullable();
            $table->string('inside_image_5')->nullable();
            
            // Social Links
            $table->string('youtube_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('webpage_link')->nullable();
            
            // Delivery
            $table->boolean('delivery_available')->default(false);
            $table->json('remark')->nullable(); // Delivery & payment conditions (multilingual)
            
            // Status & Ratings
            $table->enum('status', ['active', 'hidden', 'pending'])->default('pending');
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            
            // Business Hours
            $table->json('business_hours')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'country_id']);
            $table->index(['latitude', 'longitude']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
