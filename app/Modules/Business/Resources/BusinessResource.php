<?php

declare(strict_types=1);

namespace App\Modules\Business\Resources;

use App\Core\Resources\BaseJsonResource;
use Domain\Business\Models\Business;
use Illuminate\Http\Request;

/**
 * @mixin Business
 */
class BusinessResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,

            // Relationships
            'members' => $this->whenLoaded('members', MemberResource::collection($this->members)),
        ];
    }
}
