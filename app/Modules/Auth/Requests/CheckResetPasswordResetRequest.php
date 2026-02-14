<?php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Auth\Data\ConfirmPasswordResetData;

class CheckResetPasswordResetRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string', 'size:64'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email', ''))),
        ]);
    }

    public function toDto(): ConfirmPasswordResetData
    {
        return new ConfirmPasswordResetData(
            email: $this->input('email'),
            token: $this->input('token'),
        );
    }
}
