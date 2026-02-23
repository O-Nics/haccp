<?php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use App\Core\Requests\NormalizesEmail;
use Domain\Auth\Data\ResetPasswordData;
use Domain\Auth\Models\PasswordResetToken;

class ResetPasswordRequest extends BaseFormRequest
{
    use NormalizesEmail;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string', 'size:'.PasswordResetToken::TOKEN_LENGTH],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function toDto(): ResetPasswordData
    {
        return new ResetPasswordData(
            email: $this->input('email'),
            token: $this->input('token'),
            password: $this->input('password'),
        );
    }
}
