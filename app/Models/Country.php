<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
