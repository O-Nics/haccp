<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

if (! function_exists('apiResponse')) {
    /**
     * Create a standardized API response with optional data wrapping
     *
     * @param  mixed  $data  Resource, array, or any data to return
     * @param  int  $status  HTTP status code (default: 200)
     * @param  array<string, mixed>  $headers  Additional headers
     * @param  string|null  $message  Optional message for success/error responses
     * @return JsonResponse
     *
     * @example
     * // With JsonResource (automatically wrapped in 'data')
     * return apiResponse(new UserResource($user), 201);
     *
     * @example
     * // With array data
     * return apiResponse(['message' => 'Success'], 200);
     *
     * @example
     * // With custom message
     * return apiResponse(null, 204, [], 'Resource deleted successfully');
     */
    function apiResponse(
        mixed $data = null,
        int $status = Response::HTTP_OK,
        array $headers = [],
        ?string $message = null
    ): JsonResponse {
        // Build response array
        $response = [];

        // Add message if provided
        if ($message !== null) {
            $response['message'] = $message;
        }

        // Handle JsonResource (already wrapped in 'data')
        if ($data instanceof JsonResource) {
            return $data->response()
                ->setStatusCode($status)
                ->withHeaders($headers);
        }

        // Handle other data types
        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status, $headers);
    }
}

if (! function_exists('apiSuccess')) {
    /**
     * Create a successful API response with optional message
     *
     * @param  mixed  $data  Data to return
     * @param  string|null  $message  Success message
     * @param  int  $status  HTTP status code (default: 200)
     * @return JsonResponse
     *
     * @example
     * return apiSuccess($user, 'User created successfully', 201);
     */
    function apiSuccess(
        mixed $data = null,
        ?string $message = null,
        int $status = Response::HTTP_NO_CONTENT
    ): JsonResponse {
        return apiResponse($data, $status, [], $message);
    }
}

if (! function_exists('apiError')) {
    /**
     * Create an error API response
     *
     * @param  string  $message  Error message
     * @param  int  $status  HTTP status code (default: 400)
     * @param  array<string, mixed>  $errors  Validation errors or additional error details
     * @return JsonResponse
     *
     * @example
     * return apiError('Invalid credentials', 401);
     *
     * @example
     * // With validation errors
     * return apiError('Validation failed', 422, ['email' => ['Email is required']]);
     */
    function apiError(
        string $message,
        int $status = Response::HTTP_BAD_REQUEST,
        array $errors = []
    ): JsonResponse {
        $response = ['message' => $message];

        if (! empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
