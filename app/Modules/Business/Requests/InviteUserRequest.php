<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\InviteUserData;
use Domain\Business\Models\Business;

class InviteUserRequest extends BaseFormRequest
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

    public function business(): Business
    {
        return Business::findOrFail($this->input('business_id'));
    }

    public function toDto(): InviteUserData
    {
        return new InviteUserData(
            email: $this->input('email'),
            first_name: $this->input('first_name'),
            last_name: $this->input('last_name'),
        );
    }
}
