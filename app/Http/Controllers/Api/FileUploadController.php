<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileUploadController extends Controller
{
    public function uploadImages(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $uploadedImages[] = $this->processAndStoreImage($image, 'food-images');
        }

        return response()->json([
            'message' => 'Images uploaded successfully',
            'images' => $uploadedImages,
        ]);
    }

    public function uploadRestaurantImages(Request $request)
    {
        $request->validate([
            'outside_images' => 'nullable|array|max:2',
            'outside_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'inside_images' => 'nullable|array|max:5',
            'inside_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $uploadedImages = [
            'outside_images' => [],
            'inside_images' => [],
        ];

        if ($request->hasFile('outside_images')) {
            foreach ($request->file('outside_images') as $image) {
                $uploadedImages['outside_images'][] = $this->processAndStoreImage($image, 'restaurant-images/outside');
            }
        }

        if ($request->hasFile('inside_images')) {
            foreach ($request->file('inside_images') as $image) {
                $uploadedImages['inside_images'][] = $this->processAndStoreImage($image, 'restaurant-images/inside');
            }
        }

        return response()->json([
            'message' => 'Restaurant images uploaded successfully',
            'images' => $uploadedImages,
        ]);
    }

    public function uploadFoodImages(Request $request)
    {
        $request->validate([
            'main_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'extra_images' => 'nullable|array|max:5',
            'extra_images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $uploadedImages = [
            'main_image' => null,
            'extra_images' => [],
        ];

        // Upload main image
        if ($request->hasFile('main_image')) {
            $uploadedImages['main_image'] = $this->processAndStoreImage(
                $request->file('main_image'),
                'food-images/main'
            );
        }

        // Upload extra images
        if ($request->hasFile('extra_images')) {
            foreach ($request->file('extra_images') as $image) {
                $uploadedImages['extra_images'][] = $this->processAndStoreImage(
                    $image,
                    'food-images/extra'
                );
            }
        }

        return response()->json([
            'message' => 'Food images uploaded successfully',
            'images' => $uploadedImages,
        ]);
    }

    private function processAndStoreImage($image, $folder)
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $path = $folder . '/' . $filename;

        // Resize and optimize image
        $img = Image::make($image)
            ->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 85);

        // Store image
        Storage::disk('public')->put($path, (string) $img);

        // Return path
        return Storage::url($path);
    }
}
