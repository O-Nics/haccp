<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\InviteMemberData;
use Domain\Business\Models\Business;

class InviteMemberRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): InviteMemberData
    {
        return new InviteMemberData(
            email: $this->input('email'),
            firstName: $this->input('first_name'),
            lastName: $this->input('last_name'),
        );
    }
}
