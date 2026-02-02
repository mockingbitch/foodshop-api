<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for uploading restaurant images (outside/inside).
 *
 * Validates: outside_images (max 2), inside_images (max 5),
 * each: image, mimes jpeg/png/jpg/webp, max 5MB
 */
class UploadRestaurantImagesRequest extends FormRequest
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
            'outside_images' => 'nullable|array|max:2',
            'outside_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'inside_images' => 'nullable|array|max:5',
            'inside_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
