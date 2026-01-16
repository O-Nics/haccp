<?php

// app/Http/Requests/RefreshTokenRequest.php

declare(strict_types=1);

namespace App\Features\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use App\Features\User\Data\InviteUserData;

class InviteUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ];
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
