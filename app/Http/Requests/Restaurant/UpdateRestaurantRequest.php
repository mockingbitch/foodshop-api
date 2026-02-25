<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating an existing restaurant.
 *
 * Validates: name, description, city, address, phone, zalo, email,
 * delivery_available, remark (all optional/sometimes)
 */
class UpdateRestaurantRequest extends FormRequest
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
        $merge = $this->normalizeBooleans(['delivery_available']);
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
            'city' => 'sometimes|string|max:100',
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string|max:20',
            'zalo' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'delivery_available' => 'sometimes|boolean',
            'remark' => 'nullable|array',
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
