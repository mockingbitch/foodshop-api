<?php

namespace App\Services;

use App\Helpers\ImageUploadHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Image upload: Cloudinary hoặc Imgur (theo IMAGE_UPLOAD_DRIVER trong .env).
 * Ảnh <200KB: giữ nguyên. Ảnh >=200KB: resize + nén về ~200-500KB.
 * Folders: restaurant | food | news.
 */
class FileUploadService
{
    public function __construct(
        protected ImageProcessorService $imageProcessor
    ) {}
    /** Ảnh nhà hàng: restaurant/outside | restaurant/inside */
    public const FOLDER_RESTAURANT = 'restaurant';

    /** Ảnh món ăn: food/main | food/extra */
    public const FOLDER_FOOD = 'food';

    /** Ảnh tin tức: news/featured | news/gallery */
    public const FOLDER_NEWS = 'news';

    /**
     * Upload multiple images to folder.
     *
     * @param UploadedFile[] $images
     * @param string $folder Base folder (FOLDER_RESTAURANT | FOLDER_FOOD | FOLDER_NEWS)
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
     * Upload restaurant images: outside (max 2), inside (max 5).
     *
     * @param UploadedFile[]|null $outsideImages
     * @param UploadedFile[]|null $insideImages
     * @return array{outside_images: array, inside_images: array}
     */
    public function uploadRestaurantImages(?array $outsideImages = null, ?array $insideImages = null): array
    {
        $result = ['outside_images' => [], 'inside_images' => []];

        if (! empty($outsideImages)) {
            foreach ($outsideImages as $image) {
                $result['outside_images'][] = $this->processAndStoreImage($image, self::FOLDER_RESTAURANT . '/outside');
            }
        }
        if (! empty($insideImages)) {
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
     * Upload food item images: main_image (required) + extra_images (optional).
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

        if (! empty($extraImages)) {
            foreach ($extraImages as $image) {
                $result['extra_images'][] = $this->processAndStoreImage($image, self::FOLDER_FOOD . '/extra');
            }
        }
        Log::info('Food images uploaded', ['extra_count' => count($result['extra_images'])]);
        return $result;
    }

    /**
     * Upload news images: featured_image (optional) + gallery_images (optional, max 10).
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

    /** Ngưỡng (bytes): ảnh nhỏ hơn thì giữ nguyên, không nén. */
    private const SKIP_PROCESSING_THRESHOLD = 200_000; // 200KB

    /**
     * Upload ảnh: nếu <200KB giữ nguyên, nếu >=200KB thì nén về ~200-500KB.
     *
     * @param UploadedFile $image
     * @param string $folder folder path (vd: restaurant/outside, food/main)
     * @return string Public URL of stored image
     */
    public function processAndStoreImage(UploadedFile $image, string $folder): string
    {
        $path = $image->getRealPath();
        $fileSize = filesize($path);

        if ($fileSize !== false && $fileSize < self::SKIP_PROCESSING_THRESHOLD) {
            $uploadPath = $path;
            $tempPath = null;
        } else {
            $blob = $this->imageProcessor->compressToTarget($image);
            $tempPath = sys_get_temp_dir() . '/' . 'upload_' . uniqid() . '.jpg';
            file_put_contents($tempPath, $blob);
            $uploadPath = $tempPath;
        }

        try {
            $url = ImageUploadHelper::upload($uploadPath, $folder);

            if (! $url) {
                Log::error('Image upload failed', ['folder' => $folder]);
                throw new RuntimeException(
                    'Image upload failed. Check CLOUDINARY_* or IMGUR_CLIENT_ID in .env.'
                );
            }

            return $url;
        } finally {
            if ($tempPath !== null && is_file($tempPath)) {
                unlink($tempPath);
            }
        }
    }
}
