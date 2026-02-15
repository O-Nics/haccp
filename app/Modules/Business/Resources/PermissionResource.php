<?php

declare(strict_types=1);

namespace App\Modules\Business\Resources;

use App\Core\Resources\BaseJsonResource;
use Domain\Business\Models\Permission;
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
