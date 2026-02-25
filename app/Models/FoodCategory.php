<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Food category model (hierarchical, multilingual via translations).
 *
 * @property int $id
 * @property string $code
 * @property int|null $parent_id
 * @property string|null $image_1
 * @property string|null $image_2
 * @property string|null $image_3
 * @property string|null $image_4
 * @property string|null $image_5
 * @property bool $is_active
 * @property int $sort_order
 */
class FoodCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'parent_id',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'image_5',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['name'];

    /**
     * Multilingual name from translations (en, vn, kr). Empty array if translations not loaded.
     */
    public function getNameAttribute(): array
    {
        if (! $this->relationLoaded('translations')) {
            return [];
        }
        return $this->translations->mapWithKeys(function ($t) {
            return [strtolower($t->language_code) => $t->name];
        })->all();
    }

    /**
     * Get parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'parent_id');
    }

    /**
     * Get child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(FoodCategory::class, 'parent_id');
    }

    /**
     * Get translations.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(FoodCategoryTranslation::class);
    }

    /**
     * Get food items in this category.
     */
    public function foodItems(): HasMany
    {
        return $this->hasMany(FoodItem::class);
    }

    /**
     * Get translation for language code.
     */
    public function getTranslation(string $languageCode)
    {
        return $this->translations()->where('language_code', $languageCode)->first();
    }

    /**
     * Get image URLs as array (image_1 to image_5, filtered).
     */
    public function getImages(): array
    {
        return array_filter([
            $this->image_1,
            $this->image_2,
            $this->image_3,
            $this->image_4,
            $this->image_5,
        ]);
    }

    /**
     * Scope: only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: root categories (no parent).
     */
    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }
}
