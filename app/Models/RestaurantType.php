<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Restaurant type model (e.g. General, Snack Bar, Buffet).
 *
 * @property int $id
 * @property string $code
 * @property array $name Multilingual name (en, vn, kr)
 * @property bool $is_active
 */
class RestaurantType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get restaurants of this type.
     */
    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    /**
     * Get name for given language code.
     */
    public function getNameByLanguage(string $languageCode): string
    {
        return $this->name[$languageCode] ?? $this->name['en'] ?? '';
    }

    /**
     * Scope: only active types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
