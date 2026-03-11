<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for uploading food item images (main + extra).
 *
 * Validates: main_image (required), extra_images (max 5),
 * mỗi ảnh max 2MB, mimes jpeg/png/jpg/webp.
 */
class UploadFoodImagesRequest extends FormRequest
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
            'main_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:' . $maxKb,
            'extra_images' => 'nullable|array|max:5',
            'extra_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:' . $maxKb,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'main_image.required' => 'Ảnh chính món ăn là bắt buộc.',
            'main_image.image' => 'File phải là định dạng ảnh hợp lệ.',
            'main_image.mimes' => 'Ảnh chỉ hỗ trợ: jpeg, png, jpg, webp.',
            'main_image.max' => 'Ảnh chính không được vượt quá 2MB.',
            'extra_images.max' => 'Tối đa 5 ảnh phụ.',
            'extra_images.*.image' => 'File phải là định dạng ảnh hợp lệ.',
            'extra_images.*.mimes' => 'Ảnh chỉ hỗ trợ: jpeg, png, jpg, webp.',
            'extra_images.*.max' => 'Mỗi ảnh phụ không được vượt quá 2MB.',
        ];
    }
}
