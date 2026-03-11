<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for uploading restaurant images (outside/inside).
 *
 * Validates: outside_images (max 2), inside_images (max 5),
 * mỗi ảnh max 2MB, mimes jpeg/png/jpg/webp.
 */
class UploadRestaurantImagesRequest extends FormRequest
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
            'outside_images' => 'nullable|array|max:2',
            'outside_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:' . $maxKb,
            'inside_images' => 'nullable|array|max:5',
            'inside_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:' . $maxKb,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'outside_images.max' => 'Tối đa 2 ảnh mặt ngoài nhà hàng.',
            'inside_images.max' => 'Tối đa 5 ảnh bên trong nhà hàng.',
            'outside_images.*.image' => 'File phải là định dạng ảnh hợp lệ.',
            'outside_images.*.mimes' => 'Ảnh chỉ hỗ trợ: jpeg, png, jpg, webp.',
            'outside_images.*.max' => 'Mỗi ảnh không được vượt quá 2MB.',
            'inside_images.*.image' => 'File phải là định dạng ảnh hợp lệ.',
            'inside_images.*.mimes' => 'Ảnh chỉ hỗ trợ: jpeg, png, jpg, webp.',
            'inside_images.*.max' => 'Mỗi ảnh không được vượt quá 2MB.',
        ];
    }
}
