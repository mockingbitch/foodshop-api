<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['news', 'course', 'chef']); // Combined module
            $table->foreignId('category_id')->nullable()->constrained('food_categories')->nullOnDelete();
            
            // Multilingual fields
            $table->json('title');
            $table->json('content');
            $table->json('excerpt')->nullable();
            
            // Media
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('video_link')->nullable();
            
            // Additional fields for chef type
            $table->string('chef_name')->nullable();
            $table->string('chef_specialty')->nullable();
            
            // Additional fields for course type
            $table->decimal('course_price', 10, 2)->nullable();
            $table->integer('course_duration')->nullable(); // in hours
            $table->integer('max_participants')->nullable();
            
            $table->enum('status', ['published', 'draft', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->integer('view_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'status']);
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
