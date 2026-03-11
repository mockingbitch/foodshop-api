<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Helper upload ảnh lên Imgur qua API v3.
 * Cần cấu hình: IMGUR_CLIENT_ID trong .env (đăng ký tại https://api.imgur.com/oauth2/addclient)
 */
class ImgurHelper
{
    private const UPLOAD_URL = 'https://api.imgur.com/3/image';

    /**
     * Upload một file ảnh lên Imgur.
     *
     * @param UploadedFile|string $file UploadedFile từ Laravel hoặc đường dẫn file local
     * @param string|null $title Tiêu đề ảnh (tùy chọn)
     * @param string|null $description Mô tả ảnh (tùy chọn)
     * @return string|null URL công khai của ảnh (link), null nếu lỗi
     */
    public static function upload(
        UploadedFile|string $file,
        ?string $title = null,
        ?string $description = null
    ): ?string {
        $clientId = config('services.imgur.client_id') ?? env('IMGUR_CLIENT_ID');

        if (empty($clientId)) {
            Log::error('Imgur: Missing IMGUR_CLIENT_ID in .env');
            return null;
        }

        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! is_file($path)) {
            Log::error('Imgur: File not found', ['path' => $path]);
            return null;
        }

        $imageData = base64_encode(file_get_contents($path));

        $multipart = [
            [
                'name' => 'image',
                'contents' => $imageData,
            ],
        ];
        if ($title !== null) {
            $multipart[] = ['name' => 'title', 'contents' => $title];
        }
        if ($description !== null) {
            $multipart[] = ['name' => 'description', 'contents' => $description];
        }

        try {
            $client = new Client(['timeout' => 30]);
            $response = $client->post(self::UPLOAD_URL, [
                'headers' => [
                    'Authorization' => 'Client-ID ' . $clientId,
                ],
                'multipart' => $multipart,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $success = $body['success'] ?? false;
            $data = $body['data'] ?? [];

            if ($success && isset($data['link'])) {
                Log::info('Imgur: Image uploaded', ['url' => $data['link']]);
                return $data['link'];
            }

            $error = $data['error'] ?? $body['data']['error'] ?? 'Unknown error';
            Log::warning('Imgur: Upload failed', ['error' => $error]);
            return null;
        } catch (GuzzleException $e) {
            Log::error('Imgur: Upload failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Upload nhiều ảnh lên Imgur.
     *
     * @param array<UploadedFile|string> $files
     * @return array<string> Danh sách URL đã upload thành công
     */
    public static function uploadMultiple(array $files): array
    {
        $urls = [];
        foreach ($files as $file) {
            $url = self::upload($file);
            if ($url) {
                $urls[] = $url;
            }
        }
        return $urls;
    }
}
