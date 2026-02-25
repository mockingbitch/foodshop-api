<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating an existing restaurant.
 *
 * Validates: name, description, city, address, phone, zalo, email,
 * main_image, outside/inside images, youtube_link, facebook_link, webpage_link,
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
            'main_image' => 'nullable|string|max:500',
            'outside_image_1' => 'nullable|string|max:500',
            'outside_image_2' => 'nullable|string|max:500',
            'inside_image_1' => 'nullable|string|max:500',
            'inside_image_2' => 'nullable|string|max:500',
            'inside_image_3' => 'nullable|string|max:500',
            'inside_image_4' => 'nullable|string|max:500',
            'inside_image_5' => 'nullable|string|max:500',
            'youtube_link' => 'nullable|string|max:500',
            'facebook_link' => 'nullable|string|max:500',
            'webpage_link' => 'nullable|string|max:500',
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
