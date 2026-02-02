<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Food item model (món ăn).
 *
 * @property int $id
 * @property int $restaurant_id
 * @property int $food_category_id
 * @property string|null $food_code
 * @property string $food_code_status pending|confirmed
 * @property array $name Multilingual
 * @property array|null $description
 * @property string $main_image
 * @property array|null $extra_images
 * @property float $price
 * @property float|null $price_usd
 * @property string $currency_code
 * @property bool $is_vegetarian
 * @property bool $is_best_seller
 * @property string $status active|hidden|pending
 */
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

    /**
     * Get the restaurant.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the food category.
     */
    public function foodCategory(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class);
    }

    /**
     * Get reviews (morphMany).
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Scope: only active food items.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: only hidden food items.
     */
    public function scopeHidden($query)
    {
        return $query->where('status', 'hidden');
    }

    /**
     * Scope: only pending food items.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: best seller items.
     */
    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    /**
     * Scope: vegetarian items.
     */
    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    /**
     * Scope: pending food code confirmation.
     */
    public function scopePendingCodeConfirmation($query)
    {
        return $query->where('food_code_status', 'pending');
    }

    /**
     * Scope: confirmed food code.
     */
    public function scopeConfirmedCode($query)
    {
        return $query->where('food_code_status', 'confirmed');
    }

    /**
     * Get name for given language code.
     */
    public function getNameByLanguage(string $languageCode): string
    {
        return $this->name[$languageCode] ?? $this->name['en'] ?? '';
    }

    /**
     * Get extra images array.
     */
    public function getExtraImages(): array
    {
        return $this->extra_images ?? [];
    }
}
