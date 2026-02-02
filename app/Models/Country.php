<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Country model (reference data).
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $cctld
 * @property string|null $phone_code
 * @property string|null $currency_code
 * @property string|null $currency_symbol
 * @property bool $is_active
 */
class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'cctld',
        'phone_code',
        'currency_code',
        'currency_symbol',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get restaurants in this country.
     */
    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    /**
     * Scope: only active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
