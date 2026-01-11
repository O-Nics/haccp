<?php

declare(strict_types=1);

namespace App\Core\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
    /**
     * Wrap the resource in a 'data' key
     */
    public static $wrap = 'data';

    /**
     * Transform the resource into an array.
     * Child classes must implement this method.
     */
}
