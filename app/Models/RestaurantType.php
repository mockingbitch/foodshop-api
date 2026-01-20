<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function getNameByLanguage(string $languageCode): string
    {
        return $this->name[$languageCode] ?? $this->name['en'] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
