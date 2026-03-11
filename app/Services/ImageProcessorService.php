<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

/**
 * Xử lý ảnh: resize + nén JPEG để đạt dung lượng mục tiêu (mặc định 200-500KB).
 */
class ImageProcessorService
{
    /** Chiều rộng tối đa (px). */
    private const MAX_WIDTH = 1200;

    /** Chất lượng JPEG tối thiểu. */
    private const MIN_QUALITY = 40;

    /**
     * Resize và nén ảnh về ~200-500KB.
     *
     * @return string Binary JPEG content
     */
    public function compressToTarget(UploadedFile $file): string
    {
        $path = $file->getRealPath();
        $mime = $file->getMimeType();

        $source = $this->loadImage($path, $mime);
        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($source);
            throw new RuntimeException('Invalid image dimensions.');
        }

        $maxWidth = min($width, self::MAX_WIDTH);
        $newHeight = (int) round($height * ($maxWidth / $width));
        $newWidth = $maxWidth;

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        if ($dest === false) {
            imagedestroy($source);
            throw new RuntimeException('Failed to create destination image.');
        }

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);

        $targetMaxBytes = (config('services.image_upload.target_max_kb', 500)) * 1024;
        $blob = $this->encodeWithTargetSize($dest, $targetMaxBytes);
        imagedestroy($dest);

        return $blob;
    }

    private function loadImage(string $path, string $mime): \GdImage
    {
        $source = match (true) {
            str_contains($mime, 'jpeg') || str_contains($mime, 'jpg') => imagecreatefromjpeg($path),
            str_contains($mime, 'png') => imagecreatefrompng($path),
            str_contains($mime, 'gif') => imagecreatefromgif($path),
            str_contains($mime, 'webp') => imagecreatefromwebp($path),
            default => throw new \InvalidArgumentException('Unsupported image type: ' . $mime),
        };

        if ($source === false) {
            throw new RuntimeException('Failed to load image.');
        }

        return $source;
    }

    /**
     * Encode ảnh JPEG, giảm quality cho đến khi đạt target size.
     */
    private function encodeWithTargetSize(\GdImage $image, int $targetMaxBytes): string
    {
        $quality = 75;

        while ($quality >= self::MIN_QUALITY) {
            ob_start();
            imagejpeg($image, null, $quality);
            $blob = ob_get_clean();

            if ($blob !== false && strlen($blob) <= $targetMaxBytes) {
                return $blob;
            }

            $quality -= 10;
        }

        ob_start();
        imagejpeg($image, null, self::MIN_QUALITY);
        $blob = ob_get_clean();

        if ($blob === false || $blob === '') {
            throw new RuntimeException('Failed to encode JPEG.');
        }

        return $blob;
    }
}
