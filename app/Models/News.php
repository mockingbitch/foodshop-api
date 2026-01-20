<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function category(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'category_id');
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeNews($query)
    {
        return $query->where('type', 'news');
    }

    public function scopeCourse($query)
    {
        return $query->where('type', 'course');
    }

    public function scopeChef($query)
    {
        return $query->where('type', 'chef');
    }

    public function getTitleByLanguage(string $languageCode): string
    {
        return $this->title[$languageCode] ?? $this->title['en'] ?? '';
    }

    public function getContentByLanguage(string $languageCode): string
    {
        return $this->content[$languageCode] ?? $this->content['en'] ?? '';
    }
}
