<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating news/course/chef article.
 *
 * Validates: type, category_id, title, content, excerpt, featured_image,
 * gallery_images, video_link, status, published_at (all optional/sometimes)
 */
class UpdateNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'sometimes|in:news,course,chef',
            'category_id' => 'nullable|exists:food_categories,id',
            'title' => 'sometimes|array',
            'content' => 'sometimes|array',
            'excerpt' => 'nullable|array',
            'featured_image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'video_link' => 'nullable|string',
            'status' => 'sometimes|in:published,draft,archived',
            'published_at' => 'nullable|date',
        ];
    }
}
