<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'user_id',
        'country_id',
        'restaurant_type_id',
        'name',
        'description',
        'city',
        'address',
        'phone',
        'zalo',
        'email',
        'latitude',
        'longitude',
        'outside_image_1',
        'outside_image_2',
        'inside_image_1',
        'inside_image_2',
        'inside_image_3',
        'inside_image_4',
        'inside_image_5',
        'youtube_link',
        'facebook_link',
        'webpage_link',
        'delivery_available',
        'remark',
        'status',
        'rating',
        'review_count',
        'business_hours',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'remark' => 'array',
        'business_hours' => 'array',
        'delivery_available' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function restaurantType(): BelongsTo
    {
        return $this->belongsTo(RestaurantType::class);
    }

    public function foodItems(): HasMany
    {
        return $this->hasMany(FoodItem::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
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

    public function scopeNearby($query, $latitude, $longitude, $radiusInKm = 10)
    {
        // Haversine formula for distance calculation
        $query->selectRaw("
            *,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance
        ", [$latitude, $longitude, $latitude])
        ->having('distance', '<=', $radiusInKm)
        ->orderBy('distance');
    }

    public function getNameByLanguage(string $languageCode): string
    {
        return $this->name[$languageCode] ?? $this->name['en'] ?? '';
    }

    public function getOutsideImages(): array
    {
        return array_filter([
            $this->outside_image_1,
            $this->outside_image_2,
        ]);
    }

    public function getInsideImages(): array
    {
        return array_filter([
            $this->inside_image_1,
            $this->inside_image_2,
            $this->inside_image_3,
            $this->inside_image_4,
            $this->inside_image_5,
        ]);
    }
}
