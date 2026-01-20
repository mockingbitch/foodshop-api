<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodCategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_category_id',
        'language_code',
        'name',
        'description',
        'video_link',
    ];

    public function foodCategory(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class);
    }
}
