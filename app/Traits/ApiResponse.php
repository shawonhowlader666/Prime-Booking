<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ApiResponse Trait
 * ─────────────────
 * Provides consistent JSON envelope for ALL API responses.
 * Every API controller should use this trait.
 *
 * Standard envelope:
 * {
 *   "success": true|false,
 *   "message": "...",
 *   "data": {...},
 *   "meta": { "pagination": {...} }  // only for paginated responses
 * }
 */
trait ApiResponse
{
    protected function success(
        mixed  $data    = null,
        string $message = 'Success',
        int    $status  = 200,
        array  $extra   = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data instanceof LengthAwarePaginator) {
            $response['data'] = $data->items();
            $response['meta'] = [
                'pagination' => [
                    'total'        => $data->total(),
                    'per_page'     => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                    'from'         => $data->firstItem(),
                    'to'           => $data->lastItem(),
                    'has_more'     => $data->hasMorePages(),
                ],
            ];
        } else {
            $response['data'] = $data;
        }

        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        return response()->json($response, $status);
    }

    protected function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(
        string $message = 'An error occurred',
        int    $status  = 400,
        mixed  $errors  = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function unauthorized(string $message = 'Unauthenticated'): JsonResponse
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'Access denied'): JsonResponse
    {
        return $this->error($message, 403);
    }

    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }

    protected function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return $this->error($message, 500);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(['success' => true], 204);
    }
}
