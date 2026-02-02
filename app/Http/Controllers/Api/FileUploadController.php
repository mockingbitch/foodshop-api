<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\FileUpload\UploadFoodImagesRequest;
use App\Http\Requests\FileUpload\UploadImagesRequest;
use App\Http\Requests\FileUpload\UploadRestaurantImagesRequest;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;

/**
 * Image upload: generic images, restaurant (outside/inside), food (main + extra). Resize & store to public disk.
 */
class FileUploadController extends BaseApiController
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Upload multiple images (max 5). Stored under food-images.
     */
    public function uploadImages(UploadImagesRequest $request): JsonResponse
    {
        $images = $this->fileUploadService->uploadImages(
            $request->file('images'),
            'food-images'
        );

        return $this->success(['images' => $images], 'Images uploaded successfully');
    }

    /**
     * Upload restaurant images: outside (max 2), inside (max 5).
     */
    public function uploadRestaurantImages(UploadRestaurantImagesRequest $request): JsonResponse
    {
        $result = $this->fileUploadService->uploadRestaurantImages(
            $request->file('outside_images'),
            $request->file('inside_images')
        );

        return $this->success(['images' => $result], 'Restaurant images uploaded successfully');
    }

    /**
     * Upload food item images: main_image (required) + extra_images (max 5).
     */
    public function uploadFoodImages(UploadFoodImagesRequest $request): JsonResponse
    {
        $result = $this->fileUploadService->uploadFoodImages(
            $request->file('main_image'),
            $request->file('extra_images')
        );

        return $this->success(['images' => $result], 'Food images uploaded successfully');
    }
}
