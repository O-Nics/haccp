<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\DeleteMemberData;

final class DeleteMemberRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'member_ids' => ['required', 'array'],
            'member_ids.*' => ['string', 'exists:members,id'],
        ];
    }

    public function toDto(): DeleteMemberData
    {
        return new DeleteMemberData(
            memberIds: $this->input('member_ids'),
        );
    }
}
