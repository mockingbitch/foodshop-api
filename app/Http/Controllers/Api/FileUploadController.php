<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\FileUpload\UploadFoodImagesRequest;
use App\Http\Requests\FileUpload\UploadImagesRequest;
use App\Http\Requests\FileUpload\UploadNewsImagesRequest;
use App\Http\Requests\FileUpload\UploadRestaurantImagesRequest;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;

/**
 * Image upload: generic images, restaurant (outside/inside), food (main + extra), news (featured + gallery). Resize & store to public disk.
 */
class FileUploadController extends BaseApiController
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Upload multiple images (max 5). Lưu vào uploads/food. Response: urls (mảng URL ảnh đầy đủ).
     */
    public function uploadImages(UploadImagesRequest $request): JsonResponse
    {
        $urls = $this->fileUploadService->uploadImages(
            $request->file('images'),
            FileUploadService::FOLDER_FOOD
        );

        return $this->success(['urls' => $urls, 'images' => $urls], 'Images uploaded successfully');
    }

    /**
     * Upload restaurant images: outside (max 2), inside (max 5). Response: urls (outside_images, inside_images là mảng URL).
     */
    public function uploadRestaurantImages(UploadRestaurantImagesRequest $request): JsonResponse
    {
        $result = $this->fileUploadService->uploadRestaurantImages(
            $request->file('outside_images'),
            $request->file('inside_images')
        );

        return $this->success(['urls' => $result, 'images' => $result], 'Restaurant images uploaded successfully');
    }

    /**
     * Upload food item images: main_image (required) + extra_images (max 5). Response: urls (main_image, extra_images là URL).
     */
    public function uploadFoodImages(UploadFoodImagesRequest $request): JsonResponse
    {
        $result = $this->fileUploadService->uploadFoodImages(
            $request->file('main_image'),
            $request->file('extra_images')
        );

        return $this->success(['urls' => $result, 'images' => $result], 'Food images uploaded successfully');
    }

    /**
     * Upload news images: featured_image (optional) + gallery_images (optional, max 10).
     * Response: urls (featured_image, gallery_images) — dùng các URL này khi POST/PUT news.
     */
    public function uploadNewsImages(UploadNewsImagesRequest $request): JsonResponse
    {
        $result = $this->fileUploadService->uploadNewsImages(
            $request->file('featured_image'),
            $request->file('gallery_images')
        );

        return $this->success(['urls' => $result, 'images' => $result], 'News images uploaded successfully');
    }
}
