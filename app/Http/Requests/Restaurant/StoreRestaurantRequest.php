<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a new restaurant.
 *
 * Validates: country_id, restaurant_type_id, name (multilingual), description,
 * city, address, phone, zalo, email, latitude, longitude, delivery_available, remark
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
            'description' => 'nullable|array',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'zalo' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'delivery_available' => 'boolean',
            'remark' => 'nullable|array',
        ];
    }
}
