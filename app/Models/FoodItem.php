<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'food_category_id',
        'food_code',
        'food_code_status',
        'name',
        'description',
        'main_image',
        'extra_images',
        'price',
        'price_usd',
        'currency_code',
        'serving_size',
        'weight',
        'is_vegetarian',
        'is_best_seller',
        'customer_rating',
        'customer_review_count',
        'status',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'extra_images' => 'array',
        'price' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'is_vegetarian' => 'boolean',
        'is_best_seller' => 'boolean',
        'customer_rating' => 'decimal:2',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function foodCategory(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeHidden($query)
    {
        return $query->where('status', 'hidden');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    public function scopePendingCodeConfirmation($query)
    {
        return $query->where('food_code_status', 'pending');
    }

    public function scopeConfirmedCode($query)
    {
        return $query->where('food_code_status', 'confirmed');
    }

    public function getNameByLanguage(string $languageCode): string
    {
        return $this->name[$languageCode] ?? $this->name['en'] ?? '';
    }

    public function getExtraImages(): array
    {
        return $this->extra_images ?? [];
    }
}
