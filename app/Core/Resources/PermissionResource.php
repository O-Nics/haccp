<?php

namespace App\Core\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Models\Permission;
use Illuminate\Http\Request;

/**
 * @mixin Permission
 */
class PermissionResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'can' => $this->can,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'display_name' => $this->display_name,
        ];
    }
}
