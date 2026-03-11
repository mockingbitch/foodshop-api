<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Helper upload ảnh lên Cloudinary qua REST API.
 * Cần cấu hình: CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET trong .env
 */
class CloudinaryHelper
{
    private const UPLOAD_URL = 'https://api.cloudinary.com/v1_1/%s/image/upload';

    /**
     * Upload một file ảnh lên Cloudinary.
     *
     * @param UploadedFile|string $file UploadedFile từ Laravel hoặc đường dẫn file local
     * @param string|null $folder Thư mục trên Cloudinary (vd: restaurant/outside, food/main)
     * @param array $options Thêm options: public_id, tags,...
     * @return string|null URL công khai của ảnh sau khi upload, null nếu lỗi
     */
    public static function upload(
        UploadedFile|string $file,
        ?string $folder = null,
        array $options = []
    ): ?string {
        $cloudName = config('services.cloudinary.cloud_name') ?? env('CLOUDINARY_CLOUD_NAME');
        $apiKey = config('services.cloudinary.api_key') ?? env('CLOUDINARY_API_KEY');
        $apiSecret = config('services.cloudinary.api_secret') ?? env('CLOUDINARY_API_SECRET');

        if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
            Log::error('Cloudinary: Missing credentials. Check CLOUDINARY_* in .env');
            return null;
        }

        $url = sprintf(self::UPLOAD_URL, $cloudName);
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! is_file($path)) {
            Log::error('Cloudinary: File not found', ['path' => $path]);
            return null;
        }

        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($path, 'r'),
                'filename' => $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($path),
            ],
        ];

        if ($folder) {
            $multipart[] = ['name' => 'folder', 'contents' => $folder];
        }

        foreach ($options as $key => $value) {
            if (is_scalar($value)) {
                $multipart[] = ['name' => $key, 'contents' => (string) $value];
            }
        }

        try {
            $client = new Client(['timeout' => 30]);
            $response = $client->post($url, [
                'auth' => [$apiKey, $apiSecret],
                'multipart' => $multipart,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $secureUrl = $body['secure_url'] ?? $body['url'] ?? null;

            if ($secureUrl) {
                Log::info('Cloudinary: Image uploaded', ['url' => $secureUrl]);
                return $secureUrl;
            }

            Log::warning('Cloudinary: Unexpected response', ['body' => $body]);
            return null;
        } catch (GuzzleException $e) {
            Log::error('Cloudinary: Upload failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Upload nhiều ảnh lên Cloudinary.
     *
     * @param array<UploadedFile|string> $files
     * @param string|null $folder
     * @return array<string> Danh sách URL đã upload thành công
     */
    public static function uploadMultiple(array $files, ?string $folder = null): array
    {
        $urls = [];
        foreach ($files as $file) {
            $url = self::upload($file, $folder);
            if ($url) {
                $urls[] = $url;
            }
        }
        return $urls;
    }
}
