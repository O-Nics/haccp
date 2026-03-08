<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\UpdateRoleData;
use Spatie\LaravelData\Optional;

final class UpdateRoleRequest extends BaseFormRequest
{
    /**
     * Validation rules for partially updating a role (PATCH semantics).
     * All fields are optional — only provided fields are validated and applied.
     */
    public function rules(): array
    {
        return [
            'display_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*.id' => ['required', 'string', 'exists:permissions,id'],
        ];
    }

    /**
     * Maps validated input to an UpdateRoleData DTO.
     * Fields absent from the request are set to Optional, signalling the repository
     * to leave those attributes unchanged (PATCH semantics).
     * The slug name is recomputed from display_name only when it is provided.
     */
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
