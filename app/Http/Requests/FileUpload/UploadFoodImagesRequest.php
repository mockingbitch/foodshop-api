<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for uploading food item images (main + extra).
 *
 * Validates: main_image (required), extra_images (max 5),
 * each: image, mimes jpeg/png/jpg/webp, max 5MB
 */
class UploadFoodImagesRequest extends FormRequest
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
            'main_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'extra_images' => 'nullable|array|max:5',
            'extra_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
