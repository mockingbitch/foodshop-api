<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for uploading news images (featured + gallery).
 *
 * Validates: featured_image (optional, 1 file), gallery_images (optional, max 10),
 * each: image, mimes jpeg/png/jpg/webp, max 5MB
 */
class UploadNewsImagesRequest extends FormRequest
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
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
