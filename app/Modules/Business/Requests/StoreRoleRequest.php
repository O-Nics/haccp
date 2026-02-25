<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\IndexRoleData;
use Domain\Business\Data\StoreRoleData;

class StoreRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['required', 'array'],
            'permissions.*.id' => ['required', 'string', 'exists:permissions,id'],
        ];
    }

    public function toDto(): StoreRoleData
    {
        return new StoreRoleData(
            displayName: $this->input('display_name'),
            description: $this->input('description'),
            permissionIds: array_map(function ($permission) {
                return $permission['id'];
            }, $this->input('permissions')),
        );
    }
}
