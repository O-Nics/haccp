<?php

declare(strict_types=1);

namespace App\Features\User\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @mixin User
 */
class UserResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ];
    }
}
