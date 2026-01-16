<?php

declare(strict_types=1);

namespace App\Core\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base JSON Resource with automatic 'data' wrapping
 *
 * All API resources should extend this class to ensure consistent response structure:
 * {
 *   "data": { ... resource content ... }
 * }
 */
abstract class BaseJsonResource extends JsonResource
{
    protected int $status = Response::HTTP_OK;

    protected ?string $message = null;

    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->status);
    }

    public function with($request): array
    {
        return $this->message
            ? ['message' => $this->message]
            : [];
    }

    public function status(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed  $resource
     */
    public static function collection($resource): BaseAnonymousCollection
    {
        return new BaseAnonymousCollection($resource, static::class);
    }
}
