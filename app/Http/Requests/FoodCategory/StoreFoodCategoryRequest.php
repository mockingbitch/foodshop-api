<?php

namespace App\Http\Requests\FoodCategory;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a new food category with translations.
 *
 * Validates: code (unique), parent_id, image_1-5, sort_order,
 * translations (language_code, name, description, video_link per item)
 */
class StoreFoodCategoryRequest extends FormRequest
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
            'code' => 'required|string|max:10|unique:food_categories,code',
            'parent_id' => 'nullable|exists:food_categories,id',
            'image_1' => 'nullable|string',
            'image_2' => 'nullable|string',
            'image_3' => 'nullable|string',
            'image_4' => 'nullable|string',
            'image_5' => 'nullable|string',
            'sort_order' => 'integer',
            'translations' => 'required|array|min:1',
            'translations.*.language_code' => 'required|string|max:5',
            'translations.*.name' => 'required|string|max:200',
            'translations.*.description' => 'nullable|string',
            'translations.*.video_link' => 'nullable|string',
        ];
    }
}
