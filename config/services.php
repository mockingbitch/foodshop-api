<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],

    'imgur' => [
        'client_id' => env('IMGUR_CLIENT_ID'),
    ],

    'image_upload' => [
        'driver' => env('IMAGE_UPLOAD_DRIVER', 'cloudinary'), // cloudinary | imgur
        'max_size_kb' => (int) ((float) (env('IMAGE_MAX_SIZE_MB') ?: 2) * 1024), // mặc định 2MB (input validation)
        'target_max_kb' => (int) (env('IMAGE_TARGET_MAX_KB') ?: 500), // nén về tối đa 500KB sau khi xử lý
    ],

];
