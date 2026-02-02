<?php

namespace App\Http\Requests\ExchangeRate;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for currency conversion.
 *
 * Validates: amount (required), from_currency (required), to_currency (required)
 */
class ConvertCurrencyRequest extends FormRequest
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
            'amount' => 'required|numeric',
            'from_currency' => 'required|string|max:5',
            'to_currency' => 'required|string|max:5',
        ];
    }
}
