<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a restaurant menu.
 *
 * Validates: restaurant_id, name (multilingual), description, image, sort_order
 */
class StoreMenuRequest extends FormRequest
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
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|array',
            'name.en' => 'required|string',
            'description' => 'nullable|array',
            'image' => 'nullable|string',
            'sort_order' => 'integer',
        ];
    }
}
