<?php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use App\Core\Requests\NormalizesEmail;
use Domain\Auth\Data\ConfirmPasswordResetData;
use Domain\Auth\Models\PasswordResetToken;

class CheckResetPasswordResetRequest extends BaseFormRequest
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
        ];
    }

    public function toDto(): ConfirmPasswordResetData
    {
        return new ConfirmPasswordResetData(
            email: $this->input('email'),
            token: $this->input('token'),
        );
    }
}
