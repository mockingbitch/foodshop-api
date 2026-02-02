<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Language model (reference data for multilingual content).
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $native_name
 * @property string|null $flag_code
 * @property bool $is_active
 * @property int $sort_order
 */
class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag_code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: only active languages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
