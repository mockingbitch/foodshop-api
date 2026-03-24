<?php

namespace App\Http\Requests\FoodItem;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating an existing food item.
 *
 * Same fields as StoreFoodItemRequest (optional for PATCH) plus is_best_seller.
 * Validates: restaurant_id, food_category_id, name (en required with name; vi, ko optional), description,
 * main_image, extra_images, price, currency_code, serving_size, weight, is_vegetarian, is_best_seller.
 */
class UpdateFoodItemRequest extends FormRequest
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
        $merge = $this->normalizeBooleans(['is_vegetarian', 'is_best_seller']);
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
            'restaurant_id' => 'sometimes|exists:restaurants,id',
            'food_category_id' => 'sometimes|exists:food_categories,id',
            'name' => 'sometimes|array',
            'name.en' => 'required_with:name|string|max:255',
            'name.vi' => 'nullable|string|max:255',
            'name.ko' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'main_image' => 'sometimes|string',
            'extra_images' => 'nullable|array|max:5',
            'price' => 'sometimes|numeric|min:0',
            'currency_code' => 'sometimes|string|max:5',
            'serving_size' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'is_vegetarian' => 'sometimes|boolean',
            'is_best_seller' => 'sometimes|boolean',
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
