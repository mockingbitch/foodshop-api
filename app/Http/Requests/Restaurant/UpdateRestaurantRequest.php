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
            'delivery_available' => 'boolean',
            'remark' => 'nullable|array',
        ];
    }
}
