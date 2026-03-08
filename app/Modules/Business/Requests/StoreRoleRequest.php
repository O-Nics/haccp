<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\StoreRoleData;

final class StoreRoleRequest extends BaseFormRequest
{
    /**
     * Validation rules for creating a role.
     * All fields are required; permissions must reference existing permission IDs.
     */
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['required', 'array'],
            'permissions.*.id' => ['required', 'string', 'exists:permissions,id'],
        ];
    }

    /**
     * Maps validated input to a StoreRoleData DTO.
     * The slug name is computed from display_name at this stage.
     */
    public function toDto(): StoreRoleData
    {
        return new StoreRoleData(
            name: to_slug($this->input('display_name')),
            displayName: $this->input('display_name'),
            description: $this->input('description'),
            permissionIds: array_map(function ($permission) {
                return $permission['id'];
            }, $this->input('permissions')),
        );
    }
}
