<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\InviteMemberData;

class InviteMemberRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): InviteMemberData
    {
        return new InviteMemberData(
            businessId: $this->input('business_id'),
            email: $this->input('email'),
            firstName: $this->input('first_name'),
            lastName: $this->input('last_name'),
        );
    }
}
