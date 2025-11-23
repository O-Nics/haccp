<?php

declare(strict_types=1);

namespace App\Http\Requests\Password;

use App\DataTransferObjects\Password\ResetPasswordData;
use App\Http\Requests\Base\BaseFormRequest;

class ResetPasswordRequest extends BaseFormRequest
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email', ''))),
        ]);
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
