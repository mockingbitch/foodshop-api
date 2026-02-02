<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Food category translation model (name, description, video_link per language).
 *
 * @property int $id
 * @property int $food_category_id
 * @property string $language_code
 * @property string $name
 * @property string|null $description
 * @property string|null $video_link
 */
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

    /**
     * Get the food category.
     */
    public function foodCategory(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class);
    }
}
