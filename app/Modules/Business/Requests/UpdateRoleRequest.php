<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\UpdateRoleData;
use Spatie\LaravelData\Optional;

class UpdateRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'display_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes','nullable', 'string', 'max:1000'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*.id' => ['sometimes', 'string', 'exists:permissions,id'],
        ];
    }

    public function toDto(): UpdateRoleData
    {
        $displayName = $this->value('display_name');
        $permissions = $this->value('permissions');

        return new UpdateRoleData(
            name: $displayName instanceof Optional ? new Optional : to_slug($displayName),
            displayName: $displayName,
            description: $this->value('description'),
            permissionIds: $permissions instanceof Optional ? new Optional : array_map(function (array $permission): string {
                return $permission['id'];
            }, $permissions),
        );
    }
}
