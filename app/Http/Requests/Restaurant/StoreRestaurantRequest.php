<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a new restaurant.
 *
 * Validates: country_id, restaurant_type_id, name (en required; vi, ko optional), description (WYSIWYG/CKEditor HTML),
 * city, address, phone, zalo, email, latitude, longitude, main_image, outside/inside images,
 * youtube_link, facebook_link, webpage_link, delivery_available, remark
 */
class StoreRestaurantRequest extends FormRequest
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
            'country_id' => 'required|exists:countries,id',
            'restaurant_type_id' => 'required|exists:restaurant_types,id',
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.vi' => 'nullable|string|max:255',
            'name.ko' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'zalo' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
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
