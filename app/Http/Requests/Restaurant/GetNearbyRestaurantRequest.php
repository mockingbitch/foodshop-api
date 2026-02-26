<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for getting restaurants nearby by coordinates.
 *
 * Validates: lat (required), lng (required), radius (optional, 1-100 km), per_page (optional)
 */
class GetNearbyRestaurantRequest extends FormRequest
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
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
