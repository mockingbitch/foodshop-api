<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for uploading multiple images (generic).
 *
 * Validates: images (required, array, max 5 items), type (optional: restaurant|food|news để lưu đúng thư mục).
 */
class UploadImagesRequest extends FormRequest
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
            'images' => 'required|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'type' => 'nullable|string|in:restaurant,food,news',
        ];
    }
}
