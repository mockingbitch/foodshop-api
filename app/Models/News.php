<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * News/Course/Chef article model (unified by type).
 *
 * @property int $id
 * @property string $type news|course|chef
 * @property int|null $category_id
 * @property array $title Multilingual
 * @property array $content Multilingual
 * @property array|null $excerpt
 * @property string|null $featured_image
 * @property array|null $gallery_images
 * @property string|null $video_link
 * @property string|null $chef_name
 * @property string|null $chef_specialty
 * @property float|null $course_price
 * @property int|null $course_duration
 * @property int|null $max_participants
 * @property string $status published|draft|archived
 * @property \Carbon\Carbon|null $published_at
 * @property int $view_count
 */
class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'type',
        'category_id',
        'title',
        'content',
        'excerpt',
        'featured_image',
        'gallery_images',
        'video_link',
        'chef_name',
        'chef_specialty',
        'course_price',
        'course_duration',
        'max_participants',
        'status',
        'published_at',
        'view_count',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'excerpt' => 'array',
        'gallery_images' => 'array',
        'published_at' => 'datetime',
        'course_price' => 'decimal:2',
    ];

    /**
     * Get the category (FoodCategory).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'category_id');
    }

    /**
     * Scope: filter by type (news, course, chef).
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: published articles (status published and published_at <= now).
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope: draft articles.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: type = news.
     */
    public function scopeNews($query)
    {
        return $query->where('type', 'news');
    }

    /**
     * Scope: type = course.
     */
    public function scopeCourse($query)
    {
        return $query->where('type', 'course');
    }

    /**
     * Scope: type = chef.
     */
    public function scopeChef($query)
    {
        return $query->where('type', 'chef');
    }

    /**
     * Get title for given language code.
     */
    public function getTitleByLanguage(string $languageCode): string
    {
        return $this->title[$languageCode] ?? $this->title['en'] ?? '';
    }

    /**
     * Get content for given language code.
     */
    public function getContentByLanguage(string $languageCode): string
    {
        return $this->content[$languageCode] ?? $this->content['en'] ?? '';
    }
}
