<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\DeleteRoleData;

class DeleteRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['string', 'exists:roles,id'],
        ];
    }

    public function toDto(): DeleteRoleData
    {
        return new DeleteRoleData(
            roleIds: $this->input('role_ids'),
        );
    }
}
