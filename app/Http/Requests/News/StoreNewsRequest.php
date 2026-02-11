<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating news/course/chef article.
 *
 * Content and excerpt are WYSIWYG: HTML per language (e.g. content.en, content.vn).
 * Allowed tags: p, h1–h6, ul, ol, li, strong, em, a, img, blockquote, table, etc.
 * Server sanitizes HTML before save. Client should render with v-html / dangerouslySetInnerHTML.
 */
class StoreNewsRequest extends FormRequest
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
            'type' => 'required|in:news,course,chef',
            'category_id' => 'nullable|exists:food_categories,id',
            'title' => 'required|array',
            'title.en' => 'required|string',
            'content' => 'required|array',
            'content.en' => 'required|string',
            'excerpt' => 'nullable|array',
            'featured_image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'video_link' => 'nullable|string',
            'chef_name' => 'nullable|string',
            'chef_specialty' => 'nullable|string',
            'course_price' => 'nullable|numeric',
            'course_duration' => 'nullable|integer',
            'max_participants' => 'nullable|integer',
            'status' => 'required|in:published,draft,archived',
            'published_at' => 'nullable|date',
        ];
    }
}
