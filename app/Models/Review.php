<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Review model (polymorphic: FoodItem or Restaurant).
 *
 * @property int $id
 * @property string $reviewable_type
 * @property int $reviewable_id
 * @property string $reviewer_name
 * @property string|null $reviewer_email
 * @property int $rating 1-5
 * @property string|null $comment
 * @property array|null $images
 * @property string $status approved|pending|rejected
 */
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'comment',
        'images',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    /**
     * Get the reviewable model (FoodItem or Restaurant).
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: rejected reviews.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
