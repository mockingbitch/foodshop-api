<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

/**
 * Helper thống nhất để upload ảnh lên Cloudinary hoặc Imgur.
 * Chọn provider qua config: IMAGE_UPLOAD_DRIVER=cloudinary hoặc imgur (mặc định: cloudinary)
 */
class ImageUploadHelper
{
    public const DRIVER_CLOUDINARY = 'cloudinary';

    public const DRIVER_IMGUR = 'imgur';

    /**
     * Upload ảnh lên provider được cấu hình (cloudinary hoặc imgur).
     *
     * @param UploadedFile|string $file
     * @param string|null $folder Chỉ dùng với Cloudinary (thư mục). Bỏ qua với Imgur.
     * @return string|null URL công khai của ảnh
     */
    public static function upload(UploadedFile|string $file, ?string $folder = null): ?string
    {
        $driver = config('services.image_upload.driver') ?? env('IMAGE_UPLOAD_DRIVER', self::DRIVER_CLOUDINARY);

        return match (strtolower($driver)) {
            self::DRIVER_IMGUR => ImgurHelper::upload($file),
            default => CloudinaryHelper::upload($file, $folder),
        };
    }

    /**
     * Upload nhiều ảnh.
     *
     * @param array<UploadedFile|string> $files
     * @param string|null $folder Chỉ dùng với Cloudinary
     * @return array<string>
     */
    public static function uploadMultiple(array $files, ?string $folder = null): array
    {
        $driver = config('services.image_upload.driver') ?? env('IMAGE_UPLOAD_DRIVER', self::DRIVER_CLOUDINARY);

        return match (strtolower($driver)) {
            self::DRIVER_IMGUR => ImgurHelper::uploadMultiple($files),
            default => CloudinaryHelper::uploadMultiple($files, $folder),
        };
    }
}
