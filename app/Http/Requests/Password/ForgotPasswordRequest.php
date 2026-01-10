<?php

declare(strict_types=1);

namespace App\Http\Requests\Password;

use App\Features\Auth\Data\ForgotPasswordData;
use App\Http\Requests\Base\BaseFormRequest;

class ForgotPasswordRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email', ''))),
        ]);
    }

    public function toDto(): ForgotPasswordData
    {
        return new ForgotPasswordData(
            email: $this->input('email'),
        );
    }
}
