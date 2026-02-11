<?php

namespace App\Http\Requests\FoodItem;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for listing/searching food items (query params).
 *
 * Validates: restaurant_id, category_id, best_seller, vegetarian, search, per_page
 */
class IndexFoodItemsRequest extends FormRequest
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
        $merge = $this->normalizeBooleans(['best_seller', 'vegetarian']);
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
            'restaurant_id' => 'nullable|integer|exists:restaurants,id',
            'category_id' => 'nullable|integer|exists:food_categories,id',
            'best_seller' => 'nullable|boolean',
            'vegetarian' => 'nullable|boolean',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
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

    /**
     * Get filters array for FoodItemService::index (with proper types).
     *
     * @return array{restaurant_id?: int, category_id?: int, best_seller?: bool, vegetarian?: bool, search?: string, per_page?: int}
     */
    public function filters(): array
    {
        $validated = $this->validated();

        return [
            'restaurant_id' => $validated['restaurant_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'best_seller' => $this->boolean('best_seller'),
            'vegetarian' => $this->boolean('vegetarian'),
            'search' => $validated['search'] ?? null,
            'per_page' => $validated['per_page'] ?? null,
        ];
    }
}
