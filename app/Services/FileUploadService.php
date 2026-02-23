<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Image upload business logic: process and store images (resize, encode jpg, save to public disk).
 * Uses PHP GD. Folders tách biệt: restaurant | food | news.
 */
class FileUploadService
{
    /** Thư mục gốc upload (storage/app/public) */
    public const UPLOAD_BASE = 'uploads';

    /** Ảnh nhà hàng: uploads/restaurant/outside | uploads/restaurant/inside */
    public const FOLDER_RESTAURANT = 'uploads/restaurant';

    /** Ảnh món ăn: uploads/food/main | uploads/food/extra */
    public const FOLDER_FOOD = 'uploads/food';

    /** Ảnh tin tức: uploads/news/featured | uploads/news/gallery */
    public const FOLDER_NEWS = 'uploads/news';

    /**
     * Upload multiple images to folder. Mặc định lưu vào food.
     *
     * @param UploadedFile[] $images
     * @param string $folder Base folder (dùng FOLDER_RESTAURANT | FOLDER_FOOD | FOLDER_NEWS hoặc subfolder)
     * @return array List of stored image URLs
     */
    public function uploadImages(array $images, string $folder = self::FOLDER_FOOD): array
    {
        $urls = [];
        foreach ($images as $image) {
            $urls[] = $this->processAndStoreImage($image, $folder);
        }
        Log::info('Images uploaded', ['folder' => $folder, 'count' => count($urls)]);
        return $urls;
    }

    /**
     * Upload restaurant images: outside (max 2), inside (max 5). Lưu vào uploads/restaurant/*.
     *
     * @param UploadedFile[]|null $outsideImages
     * @param UploadedFile[]|null $insideImages
     * @return array{outside_images: array, inside_images: array}
     */
    public function uploadRestaurantImages(?array $outsideImages = null, ?array $insideImages = null): array
    {
        $result = ['outside_images' => [], 'inside_images' => []];

        if (!empty($outsideImages)) {
            foreach ($outsideImages as $image) {
                $result['outside_images'][] = $this->processAndStoreImage($image, self::FOLDER_RESTAURANT . '/outside');
            }
        }
        if (!empty($insideImages)) {
            foreach ($insideImages as $image) {
                $result['inside_images'][] = $this->processAndStoreImage($image, self::FOLDER_RESTAURANT . '/inside');
            }
        }
        $total = count($result['outside_images']) + count($result['inside_images']);
        if ($total > 0) {
            Log::info('Restaurant images uploaded', ['outside' => count($result['outside_images']), 'inside' => count($result['inside_images'])]);
        }
        return $result;
    }

    /**
     * Upload food item images: main_image (required) + extra_images (optional). Lưu vào uploads/food/*.
     *
     * @param UploadedFile $mainImage
     * @param UploadedFile[]|null $extraImages
     * @return array{main_image: string, extra_images: array}
     */
    public function uploadFoodImages(UploadedFile $mainImage, ?array $extraImages = null): array
    {
        $result = [
            'main_image' => $this->processAndStoreImage($mainImage, self::FOLDER_FOOD . '/main'),
            'extra_images' => [],
        ];

        if (!empty($extraImages)) {
            foreach ($extraImages as $image) {
                $result['extra_images'][] = $this->processAndStoreImage($image, self::FOLDER_FOOD . '/extra');
            }
        }
        Log::info('Food images uploaded', ['extra_count' => count($result['extra_images'])]);
        return $result;
    }

    /**
     * Upload news images: featured_image (optional, 1 file) + gallery_images (optional, max 10). Lưu vào uploads/news/*.
     *
     * @param UploadedFile|null $featuredImage
     * @param UploadedFile[]|null $galleryImages
     * @return array{featured_image: string|null, gallery_images: array}
     */
    public function uploadNewsImages(?UploadedFile $featuredImage = null, ?array $galleryImages = null): array
    {
        $result = [
            'featured_image' => null,
            'gallery_images' => [],
        ];

        if ($featuredImage) {
            $result['featured_image'] = $this->processAndStoreImage($featuredImage, self::FOLDER_NEWS . '/featured');
        }
        if (! empty($galleryImages)) {
            foreach ($galleryImages as $image) {
                $result['gallery_images'][] = $this->processAndStoreImage($image, self::FOLDER_NEWS . '/gallery');
            }
        }
        if ($result['featured_image'] || count($result['gallery_images']) > 0) {
            Log::info('News images uploaded', [
                'has_featured' => (bool) $result['featured_image'],
                'gallery_count' => count($result['gallery_images']),
            ]);
        }
        return $result;
    }

    /**
     * Resize image (max width 1200, aspect ratio), encode as jpg 85%, store to public disk.
     * Uses PHP GD.
     *
     * @param UploadedFile $image
     * @param string $folder
     * @return string Public URL of stored image
     */
    public function processAndStoreImage(UploadedFile $image, string $folder): string
    {
        $path = $folder . '/' . time() . '_' . uniqid() . '.jpg';

        try {
            $blob = $this->resizeAndEncodeJpeg($image, 1200, 85);
            Storage::disk('public')->put($path, $blob);
            return Storage::url($path);
        } catch (\Throwable $e) {
            Log::error('Image upload failed', ['folder' => $folder, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Load image via GD, resize (max width, keep aspect ratio, no upsize), encode as JPEG.
     *
     * @param UploadedFile $file
     * @param int $maxWidth
     * @param int $quality 1-100
     * @return string Binary JPEG content
     */
    private function resizeAndEncodeJpeg(UploadedFile $file, int $maxWidth = 1200, int $quality = 85): string
    {
        $path = $file->getRealPath();
        $mime = $file->getMimeType();

        $source = match (true) {
            str_contains($mime, 'jpeg') || str_contains($mime, 'jpg') => imagecreatefromjpeg($path),
            str_contains($mime, 'png') => imagecreatefrompng($path),
            str_contains($mime, 'gif') => imagecreatefromgif($path),
            str_contains($mime, 'webp') => imagecreatefromwebp($path),
            default => throw new \InvalidArgumentException('Unsupported image type: ' . $mime),
        };

        if ($source === false) {
            throw new \RuntimeException('Failed to load image.');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        if ($width <= 0 || $height <= 0) {
            imagedestroy($source);
            throw new \RuntimeException('Invalid image dimensions.');
        }

        if ($width <= $maxWidth) {
            $newWidth = $width;
            $newHeight = $height;
        } else {
            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));
        }

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        if ($dest === false) {
            imagedestroy($source);
            throw new \RuntimeException('Failed to create destination image.');
        }

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);

        ob_start();
        imagejpeg($dest, null, $quality);
        $blob = ob_get_clean();
        imagedestroy($dest);

        if ($blob === false || $blob === '') {
            throw new \RuntimeException('Failed to encode JPEG.');
        }

        return $blob;
    }
}
