<?php

declare(strict_types=1);

namespace App\Modules\Product\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Modules\Business\Resources\BusinessResource;
use Domain\Product\Models\Product;
use Illuminate\Http\Request;

/**
 * @mixin Product
 */
class ProductResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,

            // Relationships
            'business' => $this->whenLoaded('business', function () {
                return new BusinessResource($this->business);
            }),
        ];
    }
}
