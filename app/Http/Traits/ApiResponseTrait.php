<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

/**
 * Trait for standardised API JSON responses.
 *
 * Usage in controllers: return $this->success($data); return $this->error('Not found', 404);
 * For list APIs (paginated or per_page=all): return $this->successList($data);
 */
trait ApiResponseTrait
{
    /**
     * Success response for list APIs. Accepts LengthAwarePaginator or Collection (when per_page=all).
     * Ensures response shape: data.data = items, data.total (and pagination meta when paginated).
     *
     * @param LengthAwarePaginator|Collection $data
     */
    protected function successList(LengthAwarePaginator|Collection $data, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        if ($data instanceof Collection) {
            $payload = [
                'data' => $data->values()->all(),
                'total' => $data->count(),
            ];
            return $this->success($payload, $message, $statusCode);
        }
        return $this->success($data, $message, $statusCode);
    }

    /**
     * Success response (200).
     *
     * @param mixed $data Response payload (array, model, paginator, etc.)
     * @param string|null $message Optional message
     * @param int $statusCode HTTP status
     */
    protected function success(mixed $data = null, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $body = ['success' => true];

        if ($message !== null) {
            $body['message'] = $message;
        }

        if ($data !== null) {
            $body['data'] = $data;
        }

        return response()->json($body, $statusCode);
    }

    /**
     * Created response (201).
     *
     * @param mixed $data Created resource(s)
     * @param string|null $message Optional message
     */
    protected function created(mixed $data = null, ?string $message = null): JsonResponse
    {
        return $this->success($data, $message ?? 'Created successfully', 201);
    }

    /**
     * No content (204).
     */
    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Error response.
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status (default 400)
     * @param mixed $errors Optional validation/field errors (array or object)
     */
    protected function error(string $message, int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        $body = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $statusCode);
    }

    /**
     * Not found (404).
     */
    protected function notFound(?string $message = null): JsonResponse
    {
        return $this->error($message ?? 'Resource not found', 404);
    }

    /**
     * Unauthorized (401).
     */
    protected function unauthorized(?string $message = null): JsonResponse
    {
        return $this->error($message ?? 'Unauthorized', 401);
    }

    /**
     * Forbidden (403).
     */
    protected function forbidden(?string $message = null): JsonResponse
    {
        return $this->error($message ?? 'Forbidden', 403);
    }

    /**
     * Validation error (422).
     *
     * @param array|\Illuminate\Contracts\Support\MessageBag $errors Field errors (e.g. from ValidationException)
     * @param string|null $message Override default message
     */
    protected function validationError(mixed $errors, ?string $message = null): JsonResponse
    {
        $message = $message ?? 'The given data was invalid.';
        $errors = $errors instanceof \Illuminate\Contracts\Support\MessageBag
            ? $errors->toArray()
            : $errors;

        return $this->error($message, 422, $errors);
    }
}
