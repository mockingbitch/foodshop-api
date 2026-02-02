<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Restaurant model.
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int $country_id
 * @property int $restaurant_type_id
 * @property array $name Multilingual (en, vn, kr)
 * @property array|null $description
 * @property string $city
 * @property string $address
 * @property string $phone
 * @property string|null $zalo
 * @property string|null $email
 * @property float|null $latitude
 * @property float|null $longitude
 * @property bool $delivery_available
 * @property array|null $remark
 * @property string $status active|hidden|pending
 */
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

    /**
     * Get the owner user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the restaurant type.
     */
    public function restaurantType(): BelongsTo
    {
        return $this->belongsTo(RestaurantType::class);
    }

    /**
     * Get food items of this restaurant.
     */
    public function foodItems(): HasMany
    {
        return $this->hasMany(FoodItem::class);
    }

    /**
     * Get menus of this restaurant.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Get reviews (morphMany).
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Scope: only active restaurants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: only hidden restaurants.
     */
    public function scopeHidden($query)
    {
        return $query->where('status', 'hidden');
    }

    /**
     * Scope: only pending restaurants.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: restaurants within radius (km) of lat/long (Haversine).
     */
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

    /**
     * Get name for given language code.
     */
    public function getNameByLanguage(string $languageCode): string
    {
        return $this->name[$languageCode] ?? $this->name['en'] ?? '';
    }

    /**
     * Get outside images (max 2) as array.
     */
    public function getOutsideImages(): array
    {
        return array_filter([
            $this->outside_image_1,
            $this->outside_image_2,
        ]);
    }

    /**
     * Get inside images (max 5) as array.
     */
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
