<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for uploading multiple images (generic).
 *
 * Validates: images (required, array, max 5), mỗi ảnh max 2MB, mimes jpeg/png/jpg/webp.
 */
class UploadImagesRequest extends FormRequest
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
            'images' => 'required|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:' . $maxKb,
            'type' => 'nullable|string|in:restaurant,food,news',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxMb = config('services.image_upload.max_size_kb', 2048) / 1024;
        return [
            'images.required' => 'Vui lòng chọn ít nhất một ảnh.',
            'images.max' => 'Tối đa 5 ảnh mỗi lần upload.',
            'images.*.required' => 'Ảnh không được để trống.',
            'images.*.image' => 'File phải là định dạng ảnh hợp lệ.',
            'images.*.mimes' => 'Ảnh chỉ hỗ trợ định dạng: jpeg, png, jpg, webp.',
            'images.*.max' => 'Mỗi ảnh không được vượt quá ' . $maxMb . 'MB.',
        ];
    }
}
