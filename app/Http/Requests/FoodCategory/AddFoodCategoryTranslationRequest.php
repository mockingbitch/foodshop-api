<?php

namespace App\Http\Requests\FoodCategory;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for adding or updating a food category translation.
 *
 * Validates: language_code, name, description, video_link
 */
class AddFoodCategoryTranslationRequest extends FormRequest
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
            'language_code' => 'required|string|max:5',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'video_link' => 'nullable|string',
        ];
    }
}
