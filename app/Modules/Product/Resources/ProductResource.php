<?php

declare(strict_types=1);

namespace App\Modules\Product\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Modules\Business\Resources\MemberResource;
use Domain\Business\Models\Business;
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
            'name' => $this->name,

            // Relationships
            'business' => $this->whenLoaded('business', function () {
                return new ProductResource($this->business);
            }),
        ];
    }
}
