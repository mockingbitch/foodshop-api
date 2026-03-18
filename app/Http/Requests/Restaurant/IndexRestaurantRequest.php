<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for listing/searching restaurants.
 *
 * Filters: owner_id, country_id, restaurant_type_id, delivery_available, search, lat, lng, radius, per_page
 */
class IndexRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Backward compatibility: some clients send `name` instead of `search` on /restaurants/search
        if (($this->input('search') === null || $this->input('search') === '') && $this->filled('name')) {
            $this->merge(['search' => $this->input('name')]);
        }

        $merge = $this->normalizeBooleans(['delivery_available']);
        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'owner_id' => 'nullable|integer|min:1',
            'country_id' => 'nullable|integer|min:1',
            'restaurant_type_id' => 'nullable|integer|min:1',
            'delivery_available' => 'nullable|boolean',
            'search' => 'nullable|string',
            // accepted for backward compatibility (mapped into `search` in prepareForValidation)
            'name' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
            // allow integer or "all"
            'per_page' => 'nullable',
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

