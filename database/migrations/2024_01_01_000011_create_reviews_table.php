<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable'); // restaurant_id or food_item_id
            $table->string('reviewer_name', 100);
            $table->string('reviewer_email', 100)->nullable();
            $table->integer('rating'); // 1-5 stars
            $table->text('comment')->nullable();
            $table->json('images')->nullable(); // Review images
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->index(['reviewable_type', 'reviewable_id', 'status']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
