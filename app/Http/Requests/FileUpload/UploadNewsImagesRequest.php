<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for uploading news images (featured + gallery).
 *
 * Validates: featured_image (optional), gallery_images (max 10),
 * mỗi ảnh max 2MB, mimes jpeg/png/jpg/webp.
 */
class UploadNewsImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxKb = config('services.image_upload.max_size_kb', 2048);
        return [
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:' . $maxKb,
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:' . $maxKb,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'featured_image.image' => 'File phải là định dạng ảnh hợp lệ.',
            'featured_image.mimes' => 'Ảnh chỉ hỗ trợ: jpeg, png, jpg, webp.',
            'featured_image.max' => 'Ảnh nổi bật không được vượt quá 2MB.',
            'gallery_images.max' => 'Tối đa 10 ảnh trong gallery.',
            'gallery_images.*.image' => 'File phải là định dạng ảnh hợp lệ.',
            'gallery_images.*.mimes' => 'Ảnh chỉ hỗ trợ: jpeg, png, jpg, webp.',
            'gallery_images.*.max' => 'Mỗi ảnh không được vượt quá 2MB.',
        ];
    }
}
