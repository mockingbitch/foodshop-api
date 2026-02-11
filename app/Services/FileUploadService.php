<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Image upload business logic: process and store images (resize, encode jpg, save to public disk).
 * Handles generic images, restaurant (outside/inside), and food (main + extra).
 */
class FileUploadService
{
    /**
     * Upload multiple images to folder. Each image is resized and stored.
     *
     * @param UploadedFile[] $images
     * @param string $folder Base folder (e.g. food-images)
     * @return array List of stored image URLs
     */
    public function uploadImages(array $images, string $folder = 'food-images'): array
    {
        $urls = [];
        foreach ($images as $image) {
            $urls[] = $this->processAndStoreImage($image, $folder);
        }
        Log::info('Images uploaded', ['folder' => $folder, 'count' => count($urls)]);
        return $urls;
    }

    /**
     * Upload restaurant images: outside (max 2), inside (max 5). Returns keyed array.
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
                $result['outside_images'][] = $this->processAndStoreImage($image, 'restaurant-images/outside');
            }
        }
        if (!empty($insideImages)) {
            foreach ($insideImages as $image) {
                $result['inside_images'][] = $this->processAndStoreImage($image, 'restaurant-images/inside');
            }
        }
        $total = count($result['outside_images']) + count($result['inside_images']);
        if ($total > 0) {
            Log::info('Restaurant images uploaded', ['outside' => count($result['outside_images']), 'inside' => count($result['inside_images'])]);
        }
        return $result;
    }

    /**
     * Upload food item images: main_image (required) + extra_images (optional). Returns keyed array.
     *
     * @param UploadedFile $mainImage
     * @param UploadedFile[]|null $extraImages
     * @return array{main_image: string, extra_images: array}
     */
    public function uploadFoodImages(UploadedFile $mainImage, ?array $extraImages = null): array
    {
        $result = [
            'main_image' => $this->processAndStoreImage($mainImage, 'food-images/main'),
            'extra_images' => [],
        ];

        if (!empty($extraImages)) {
            foreach ($extraImages as $image) {
                $result['extra_images'][] = $this->processAndStoreImage($image, 'food-images/extra');
            }
        }
        Log::info('Food images uploaded', ['extra_count' => count($result['extra_images'])]);
        return $result;
    }

    /**
     * Upload news images: featured_image (optional, 1 file) + gallery_images (optional, max 10).
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
            $result['featured_image'] = $this->processAndStoreImage($featuredImage, 'news-images/featured');
        }
        if (! empty($galleryImages)) {
            foreach ($galleryImages as $image) {
                $result['gallery_images'][] = $this->processAndStoreImage($image, 'news-images/gallery');
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
     *
     * @param UploadedFile $image
     * @param string $folder
     * @return string Public URL of stored image
     */
    public function processAndStoreImage(UploadedFile $image, string $folder): string
    {
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $path = $folder . '/' . $filename;

        try {
            $img = Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 85);

            Storage::disk('public')->put($path, (string) $img);
            return Storage::url($path);
        } catch (\Throwable $e) {
            Log::error('Image upload failed', ['folder' => $folder, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
