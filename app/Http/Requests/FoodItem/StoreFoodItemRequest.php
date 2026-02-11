<?php

namespace App\Http\Requests\FoodItem;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a new food item.
 *
 * Validates: restaurant_id, food_category_id, name (multilingual), description,
 * main_image, extra_images, price, currency_code, serving_size, weight, is_vegetarian
 */
class StoreFoodItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation (normalize string "true"/"false" to boolean).
     */
    protected function prepareForValidation(): void
    {
        $merge = $this->normalizeBooleans(['is_vegetarian']);
        if ($merge !== []) {
            $this->merge($merge);
        }
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
            'food_category_id' => 'required|exists:food_categories,id',
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'description' => 'nullable|array',
            'main_image' => 'required|string',
            'extra_images' => 'nullable|array|max:5',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|max:5',
            'serving_size' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'is_vegetarian' => 'sometimes|boolean',
        ];
    }

    /**
     * Normalize string "true"/"false" to boolean for given keys.
     *
     * @param  array<string>  $keys
     * @return array<string, bool>
     */
    protected function normalizeBooleans(array $keys): array
    {
        $merge = [];
        foreach ($keys as $key) {
            $value = $this->input($key);
            if ($value !== null) {
                $merge[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $value;
            }
        }
        return $merge;
    }
}
