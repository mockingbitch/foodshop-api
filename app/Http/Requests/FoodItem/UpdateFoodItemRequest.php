<?php

namespace App\Http\Requests\FoodItem;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating an existing food item.
 *
 * Validates: name, description, main_image, extra_images, price,
 * serving_size, weight, is_vegetarian, is_best_seller (all optional/sometimes)
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
            'name' => 'sometimes|array',
            'description' => 'nullable|array',
            'main_image' => 'sometimes|string',
            'extra_images' => 'nullable|array|max:5',
            'price' => 'sometimes|numeric|min:0',
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
