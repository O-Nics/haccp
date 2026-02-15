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
            'status' => $this->pivot->status,
            'role' => $this->pivot != null ? new BusinessRoleResource($this->pivot->role) : null,
        ];
    }
}
