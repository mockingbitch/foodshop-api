<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(FoodCategory::class, 'parent_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(FoodCategoryTranslation::class);
    }

    public function foodItems(): HasMany
    {
        return $this->hasMany(FoodItem::class);
    }

    public function getTranslation(string $languageCode)
    {
        return $this->translations()->where('language_code', $languageCode)->first();
    }

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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }
}
