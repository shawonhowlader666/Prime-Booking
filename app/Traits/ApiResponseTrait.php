<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Success JSON Response Transformer
     */
    public function successResponse($data = [], string $message = 'Success', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'meta' => [
                'code' => $statusCode,
                'timestamp' => now()->toIso8601String(),
            ]
        ], $statusCode);
    }

    /**
     * Error JSON Response Transformer
     */
    public function errorResponse(string $message = 'An error occurred', int $statusCode = Response::HTTP_BAD_REQUEST, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'meta' => [
                'code' => $statusCode,
                'timestamp' => now()->toIso8601String(),
            ]
        ], $statusCode);
    }
}
