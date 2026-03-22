<?php

namespace App\Http\Requests\FoodCategory;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating an existing food category.
 *
 * Validates: code, parent_id, image_1-5, sort_order, translations (optional)
 * translations: array of { language_code, name, description?, video_link? } per item
 */
class UpdateFoodCategoryRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'code' => 'sometimes|string|max:10|unique:food_categories,code,' . $id,
            'parent_id' => 'nullable|exists:food_categories,id',
            'image_1' => 'nullable|string',
            'image_2' => 'nullable|string',
            'image_3' => 'nullable|string',
            'image_4' => 'nullable|string',
            'image_5' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'translations' => 'nullable|array',
            'translations.*.language_code' => 'required|string|max:5',
            'translations.*.name' => 'required|string|max:200',
            'translations.*.description' => 'nullable|string',
            'translations.*.video_link' => 'nullable|string',
        ];
    }
}
